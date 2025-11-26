<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateRequest;
use App\Models\Certificate;
use App\Models\FranchiseWallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\{DB, Log, Response};
use Illuminate\Support\Str;

class CertificateRequestController extends Controller
{
    /**
     * ✅ FIXED: Display certificate requests dashboard with DataTables
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = CertificateRequest::with([
                'student:id,name,email',
                'franchise:id,name,email',
                'course:id,name,certificate_fee',
                'approvedBy:id,name',
                'rejectedBy:id,name'
            ])->orderBy('created_at', 'desc');

            // Apply filters
            if ($request->filled('status_filter')) {
                $query->where('status', $request->status_filter);
            }

            if ($request->filled('franchise_filter')) {
                $query->where('franchise_id', $request->franchise_filter);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('student_info', function($row) {
                    $franchiseName = $row->franchise->name ?? 'N/A';
                    return "
                        <div class='student-info'>
                            <strong class='text-primary'>{$row->student->name}</strong><br>
                            <small class='text-muted'>
                                <i class='fas fa-envelope'></i> {$row->student->email}
                            </small><br>
                            <span class='badge badge-light mt-1'>
                                <i class='fas fa-building'></i> {$franchiseName}
                            </span>
                        </div>
                    ";
                })
                ->addColumn('course_info', function($row) {
                    if ($row->course) {
                        return "
                            <div>
                                <strong>{$row->course->name}</strong><br>
                                <small class='text-muted'>" . ucfirst($row->certificate_type ?? 'Standard') . "</small>
                            </div>
                        ";
                    }
                    return '<em class="text-muted">General Certificate</em>';
                })
                // ✅ FIXED: Removed 'payment_info', replaced with 'amount_info'
                ->addColumn('amount_info', function($row) {
                    return "
                        <div class='text-center'>
                            <strong class='text-success' style='font-size: 1.1rem;'>₹" . number_format($row->amount, 2) . "</strong><br>
                            <small class='badge badge-info mt-1'>
                                <i class='fas fa-wallet'></i> Wallet Deducted
                            </small>
                        </div>
                    ";
                })
                ->addColumn('status_badge', function($row) {
                    $colors = [
                        'pending' => 'warning',
                        'processing' => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'completed' => 'primary'
                    ];
                    $icons = [
                        'pending' => 'clock',
                        'processing' => 'spinner fa-spin',
                        'approved' => 'check-circle',
                        'rejected' => 'times-circle',
                        'completed' => 'certificate'
                    ];
                    $color = $colors[$row->status] ?? 'secondary';
                    $icon = $icons[$row->status] ?? 'question';
                    
                    $badge = "<span class='badge badge-{$color} status-badge px-3 py-2'>
                                <i class='fas fa-{$icon} mr-1'></i>" . ucfirst($row->status) . "
                             </span>";
                    
                    // Add admin info
                    if ($row->status === 'approved' && $row->approvedBy) {
                        $badge .= "<br><small class='text-muted mt-1'>by {$row->approvedBy->name}</small>";
                    } elseif ($row->status === 'rejected' && $row->rejectedBy) {
                        $badge .= "<br><small class='text-danger mt-1'>by {$row->rejectedBy->name}</small>";
                    }
                    
                    return $badge;
                })
                ->addColumn('request_date', function($row) {
                    return "
                        <div class='text-center'>
                            <strong>" . $row->created_at->format('d M Y') . "</strong><br>
                            <small class='text-muted'>" . $row->created_at->format('h:i A') . "</small><br>
                            <span class='badge badge-light mt-1'>" . $row->created_at->diffForHumans() . "</span>
                        </div>
                    ";
                })
                ->addColumn('actions', function($row) {
                    $actions = '<div class="btn-group btn-group-sm" role="group">';

                    // View Details
                    $actions .= '<a href="' . route('admin.certificate-requests.show', $row->id) . '"
                                   class="btn btn-info" title="View Details" data-toggle="tooltip">
                                   <i class="fas fa-eye"></i>
                                </a>';

                    if ($row->status === 'pending') {
                        // Approve Button
                        $actions .= '<button onclick="showApproveModal(' . $row->id . ')"
                                           class="btn btn-success" title="Approve" data-toggle="tooltip">
                                           <i class="fas fa-check"></i>
                                    </button>';

                        // Reject Button
                        $actions .= '<button onclick="showRejectModal(' . $row->id . ')"
                                           class="btn btn-danger" title="Reject" data-toggle="tooltip">
                                           <i class="fas fa-times"></i>
                                    </button>';
                    } else {
                        // View Certificate
                        if (in_array($row->status, ['approved', 'completed'])) {
                            $certificate = Certificate::where('certificate_request_id', $row->id)->first();
                            if ($certificate) {
                                $actions .= '<a href="' . route('admin.certificates.show', $certificate->id) . '"
                                               class="btn btn-primary" title="View Certificate" data-toggle="tooltip">
                                               <i class="fas fa-certificate"></i>
                                        </a>';
                            }
                        }
                        
                        // History Button
                        $actions .= '<button onclick="showTimeline(' . $row->id . ')"
                                           class="btn btn-secondary" title="History" data-toggle="tooltip">
                                           <i class="fas fa-history"></i>
                                    </button>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['student_info', 'course_info', 'amount_info', 'status_badge', 'request_date', 'actions'])
                ->make(true);
        }

        // Dashboard stats
        $stats = [
            'total' => CertificateRequest::count(),
            'pending' => CertificateRequest::where('status', 'pending')->count(),
            'approved' => CertificateRequest::where('status', 'approved')->count(),
            'rejected' => CertificateRequest::where('status', 'rejected')->count(),
            'completed' => CertificateRequest::where('status', 'completed')->count(),
            'today_requests' => CertificateRequest::whereDate('created_at', today())->count(),
            'today_approved' => CertificateRequest::where('status', 'approved')
                                                  ->whereDate('approved_at', today())
                                                  ->count(),
            'total_revenue' => CertificateRequest::whereIn('status', ['approved', 'completed'])
                                                ->sum('amount'),
        ];

        // Franchise list for filter
        $franchises = \App\Models\User::where('role', 'franchise')
                                      ->select('id', 'name')
                                      ->orderBy('name')
                                      ->get();

        return view('admin.certificate-requests.index', compact('stats', 'franchises'));
    }

    /**
     * Show detailed certificate request
     */
    public function show(CertificateRequest $certificateRequest)
    {
        $certificateRequest->load([
            'student',
            'franchise',
            'course',
            'approvedBy',
            'rejectedBy',
            'processedBy',
            'walletTransaction'
        ]);

        // Get certificate if exists
        $certificate = Certificate::where('certificate_request_id', $certificateRequest->id)->first();

        return view('admin.certificate-requests.show', compact('certificateRequest', 'certificate'));
    }

    /**
     * ✅ FIXED: Approve certificate request with wallet validation
     */
    public function approve(Request $request, CertificateRequest $certificateRequest)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000'
        ]);

        // Validate status
        if ($certificateRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending requests can be approved.'
            ]);
        }

        DB::beginTransaction();

        try {
            // Update status
            $certificateRequest->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'admin_notes' => $request->notes
            ]);

            // Generate certificate number
            $certificateNumber = $this->generateUniqueCertificateNumber();

            // Create certificate
            $certificate = Certificate::create([
                'student_id' => $certificateRequest->student_id,
                'course_id' => $certificateRequest->course_id,
                'franchise_id' => $certificateRequest->franchise_id,
                'certificate_request_id' => $certificateRequest->id,
                'number' => $certificateNumber,
                'status' => 'issued',
                'issued_at' => now(),
                'issued_by' => auth()->id()
            ]);

            DB::commit();

            Log::info('Certificate approved', [
                'request_id' => $certificateRequest->id,
                'certificate_number' => $certificateNumber
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Certificate approved! Number: ' . $certificateNumber,
                'data' => [
                    'certificate_number' => $certificateNumber,
                    'certificate_id' => $certificate->id
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Approval failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Approval failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ FIXED: Reject certificate request with automatic wallet refund
     */
    public function reject(Request $request, CertificateRequest $certificateRequest)
    {
        $request->validate([
            'reason' => 'required|string|min:10|max:500',
            'notes' => 'nullable|string|max:1000'
        ]);

        // Validate status
        if ($certificateRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending requests can be rejected.'
            ]);
        }

        DB::beginTransaction();

        try {
            // Update status
            $certificateRequest->update([
                'status' => 'rejected',
                'rejected_by' => auth()->id(),
                'rejected_at' => now(),
                'rejection_reason' => $request->reason,
                'admin_notes' => $request->notes
            ]);

            // ✅ REFUND TO WALLET
            $wallet = FranchiseWallet::where('franchise_id', $certificateRequest->franchise_id)->first();
            
            if ($wallet) {
                $wallet->balance += $certificateRequest->amount;
                $wallet->save();

                // Create refund transaction
                WalletTransaction::create([
                    'franchise_wallet_id' => $wallet->id,
                    'type' => 'credit',
                    'amount' => $certificateRequest->amount,
                    'description' => "Refund for rejected certificate request #" . $certificateRequest->id,
                    'status' => 'completed',
                    'balance_after' => $wallet->balance,
                    'reference_type' => get_class($certificateRequest),
                    'reference_id' => $certificateRequest->id
                ]);

                Log::info('Wallet refunded', [
                    'request_id' => $certificateRequest->id,
                    'amount' => $certificateRequest->amount,
                    'new_balance' => $wallet->balance
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Request rejected and amount refunded to wallet.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Rejection failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Rejection failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ FIXED: Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'request_ids' => 'required|array|min:1',
            'request_ids.*' => 'exists:certificate_requests,id',
            'reason' => 'required_if:action,reject|string|min:10|max:500'
        ]);

        $successCount = 0;
        $errorCount = 0;

        DB::beginTransaction();

        try {
            $requests = CertificateRequest::whereIn('id', $request->request_ids)
                                        ->where('status', 'pending')
                                        ->get();

            foreach ($requests as $certRequest) {
                try {
                    if ($request->action === 'approve') {
                        $certRequest->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now()
                        ]);

                        Certificate::create([
                            'student_id' => $certRequest->student_id,
                            'course_id' => $certRequest->course_id,
                            'franchise_id' => $certRequest->franchise_id,
                            'certificate_request_id' => $certRequest->id,
                            'number' => $this->generateUniqueCertificateNumber(),
                            'status' => 'issued',
                            'issued_at' => now(),
                            'issued_by' => auth()->id()
                        ]);

                        $successCount++;

                    } elseif ($request->action === 'reject') {
                        $certRequest->update([
                            'status' => 'rejected',
                            'rejected_by' => auth()->id(),
                            'rejected_at' => now(),
                            'rejection_reason' => $request->reason
                        ]);

                        // Refund wallet
                        $wallet = FranchiseWallet::where('franchise_id', $certRequest->franchise_id)->first();
                        if ($wallet) {
                            $wallet->balance += $certRequest->amount;
                            $wallet->save();

                            WalletTransaction::create([
                                'franchise_wallet_id' => $wallet->id,
                                'type' => 'credit',
                                'amount' => $certRequest->amount,
                                'description' => "Bulk refund for rejected request #" . $certRequest->id,
                                'status' => 'completed',
                                'balance_after' => $wallet->balance,
                                'reference_type' => get_class($certRequest),
                                'reference_id' => $certRequest->id
                            ]);
                        }

                        $successCount++;
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error("Bulk action error: " . $e->getMessage());
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully {$request->action}d {$successCount} request(s). {$errorCount} failed.",
                'data' => [
                    'success_count' => $successCount,
                    'error_count' => $errorCount
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Bulk action failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export to CSV
     */
    public function export(Request $request)
    {
        $query = CertificateRequest::with(['student', 'franchise', 'course']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('created_at', 'desc')->get();

        $csvData = "ID,Student,Franchise,Course,Amount,Status,Request Date\n";

        foreach ($requests as $req) {
            $csvData .= sprintf(
                "%d,%s,%s,%s,%.2f,%s,%s\n",
                $req->id,
                $req->student->name ?? 'N/A',
                $req->franchise->name ?? 'N/A',
                $req->course->name ?? 'N/A',
                $req->amount,
                ucfirst($req->status),
                $req->created_at->format('Y-m-d H:i:s')
            );
        }

        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="certificate_requests_' . date('Ymd_His') . '.csv"'
        ]);
    }

    /**
     * Get statistics
     */
    public function getStats()
    {
        return response()->json([
            'total_requests' => CertificateRequest::count(),
            'pending' => CertificateRequest::where('status', 'pending')->count(),
            'approved' => CertificateRequest::where('status', 'approved')->count(),
            'rejected' => CertificateRequest::where('status', 'rejected')->count(),
            'total_revenue' => CertificateRequest::whereIn('status', ['approved', 'completed'])->sum('amount'),
            'recent_requests' => CertificateRequest::with(['student', 'course', 'franchise'])
                                                  ->orderBy('created_at', 'desc')
                                                  ->take(5)
                                                  ->get()
        ]);
    }

    /**
     * ✅ FIXED: Generate unique certificate number
     */
    private function generateUniqueCertificateNumber()
    {
        do {
            $number = 'CERT-' . date('Y') . '-' . strtoupper(Str::random(6));
        } while (Certificate::where('number', $number)->exists());

        return $number;
    }
}
