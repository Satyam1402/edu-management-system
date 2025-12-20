<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateRequest;
use App\Models\Certificate;
use App\Models\FranchiseWallet;
use App\Models\WalletTransaction;
use App\Models\Franchise;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\{DB, Log, Response};
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CertificateRequestController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = CertificateRequest::with([
                'student',
                'franchise',
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
                    $studentName = $row->student ? $row->student->full_name : 'N/A';
                    $studentEmail = $row->student ? $row->student->email : '';
                    $franchiseName = $row->franchise ? $row->franchise->name : 'N/A';

                    return "
                        <div class='student-info'>
                            <strong class='text-primary'>{$studentName}</strong><br>
                            <small class='text-muted'>
                                <i class='fas fa-envelope'></i> {$studentEmail}
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
                ->addColumn('amount_info', function($row) {
                    $paymentBadge = '';
                    if ($row->payment_status === 'paid') {
                        $paymentBadge = "<small class='badge badge-success mt-1'>
                            <i class='fas fa-check'></i> Paid
                        </small>";
                    } else {
                        $paymentBadge = "<small class='badge badge-warning mt-1'>
                            <i class='fas fa-clock'></i> Unpaid
                        </small>";
                    }

                    return "
                        <div class='text-center'>
                            <strong class='text-success' style='font-size: 1.1rem;'>₹" . number_format($row->amount, 2) . "</strong><br>
                            {$paymentBadge}
                        </div>
                    ";
                })
                ->addColumn('status_badge', function($row) {
                    $colors = [
                        'pending' => 'warning',
                        'processing' => 'info',
                        'approved' => 'success',
                        'paid' => 'info',
                        'rejected' => 'danger',
                        'completed' => 'primary'
                    ];
                    $icons = [
                        'pending' => 'clock',
                        'processing' => 'spinner fa-spin',
                        'approved' => 'check-circle',
                        'paid' => 'money-check-alt',
                        'rejected' => 'times-circle',
                        'completed' => 'certificate'
                    ];
                    $color = $colors[$row->status] ?? 'secondary';
                    $icon = $icons[$row->status] ?? 'question';

                    $statusText = ucfirst($row->status);
                    if ($row->status === 'paid') {
                        $statusText = 'Processing';
                    }

                    $badge = "<span class='badge badge-{$color} status-badge px-3 py-2'>
                                <i class='fas fa-{$icon} mr-1'></i>{$statusText}
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
                    }
                    // ✅ NEW: Mark as Completed button for paid requests
                    elseif ($row->status === 'paid' && $row->payment_status === 'paid') {
                        $actions .= '<button onclick="markAsCompleted(' . $row->id . ')"
                                           class="btn btn-primary" title="Mark as Completed" data-toggle="tooltip">
                                           <i class="fas fa-certificate"></i> Complete
                                    </button>';
                    }
                    // View Certificate (if completed)
                    elseif ($row->status === 'completed') {
                        $certificate = Certificate::where('certificate_request_id', $row->id)->first();
                        if ($certificate) {
                            $actions .= '<a href="' . route('admin.certificates.show', $certificate->id) . '"
                                           class="btn btn-primary" title="View Certificate" data-toggle="tooltip">
                                           <i class="fas fa-certificate"></i>
                                    </a>';
                        }
                    }

                    // History Button (for non-pending requests)
                    if (!in_array($row->status, ['pending'])) {
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
            'paid' => CertificateRequest::where('status', 'paid')->count(), // ✅ NEW
            'rejected' => CertificateRequest::where('status', 'rejected')->count(),
            'completed' => CertificateRequest::where('status', 'completed')->count(),
            'today_requests' => CertificateRequest::whereDate('created_at', today())->count(),
            'today_approved' => CertificateRequest::where('status', 'approved')
                                                  ->whereDate('approved_at', today())
                                                  ->count(),
            'total_revenue' => CertificateRequest::whereIn('status', ['approved', 'paid', 'completed'])
                                                ->where('payment_status', 'paid')
                                                ->sum('amount'),
        ];

        $franchises = Franchise::select('id', 'name')
                              ->orderBy('name')
                              ->get();

        return view('admin.certificate-requests.index', compact('stats', 'franchises'));
    }

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
     * Approve certificate request
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
     * Reject certificate request
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

            // REFUND TO WALLET (if payment was made)
            if ($certificateRequest->payment_status === 'paid' && $certificateRequest->wallet_transaction_id) {
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
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Request rejected successfully.'
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
     * ✅ NEW: Mark certificate request as completed
     */
    public function markAsCompleted(Request $request, CertificateRequest $certificateRequest)
    {
        // Validate status
        if ($certificateRequest->status !== 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Only paid requests can be marked as completed.'
            ]);
        }

        if ($certificateRequest->payment_status !== 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Payment must be completed before marking as completed.'
            ]);
        }

        DB::beginTransaction();

        try {
            // Update certificate request status
            $certificateRequest->update([
                'status' => 'completed',
                'processed_by' => auth()->id(),
                'processed_at' => now()
            ]);

            // Update certificate status
            $certificate = Certificate::where('certificate_request_id', $certificateRequest->id)->first();

            if ($certificate) {
                $certificate->update([
                    'status' => 'completed',
                    'completed_at' => now()
                ]);
            }

            DB::commit();

            Log::info('Certificate marked as completed', [
                'request_id' => $certificateRequest->id,
                'certificate_number' => $certificate->number ?? 'N/A',
                'processed_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Certificate marked as completed! Franchise can now download it.',
                'data' => [
                    'certificate_number' => $certificate->number ?? null
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Mark as completed failed', [
                'request_id' => $certificateRequest->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark as completed: ' . $e->getMessage()
            ], 500);
        }
    }

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

                        // Refund wallet if paid
                        if ($certRequest->payment_status === 'paid') {
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

        $csvData = "ID,Student,Franchise,Course,Amount,Status,Payment Status,Request Date\n";

        foreach ($requests as $req) {
            $csvData .= sprintf(
                "%d,%s,%s,%s,%.2f,%s,%s,%s\n",
                $req->id,
                $req->student ? $req->student->full_name : 'N/A',
                $req->franchise ? $req->franchise->name : 'N/A',
                $req->course ? $req->course->name : 'N/A',
                $req->amount,
                ucfirst($req->status),
                ucfirst($req->payment_status),
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
            'paid' => CertificateRequest::where('status', 'paid')->count(),
            'rejected' => CertificateRequest::where('status', 'rejected')->count(),
            'completed' => CertificateRequest::where('status', 'completed')->count(),
            'total_revenue' => CertificateRequest::whereIn('status', ['approved', 'paid', 'completed'])
                                                ->where('payment_status', 'paid')
                                                ->sum('amount'),
            'recent_requests' => CertificateRequest::with(['student', 'course', 'franchise'])
                                                  ->orderBy('created_at', 'desc')
                                                  ->take(5)
                                                  ->get()
        ]);
    }

    /**
     * Generate unique certificate number
     */
    private function generateUniqueCertificateNumber()
    {
        do {
            $number = 'CERT-' . date('Y') . '-' . strtoupper(Str::random(6));
        } while (Certificate::where('number', $number)->exists());

        return $number;
    }

    /**
     * Mark certificate as completed
     */
    public function complete(CertificateRequest $certificateRequest)
    {
        // Validate status - can only complete if paid
        if ($certificateRequest->status !== 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Only paid requests can be marked as completed. Current status: ' . $certificateRequest->status
            ]);
        }

        DB::beginTransaction();

        try {
            // Generate certificate number if not exists
            if (!$certificateRequest->certificate_number) {
                $certificateNumber = $this->generateUniqueCertificateNumber();
            } else {
                $certificateNumber = $certificateRequest->certificate_number;
            }

            // Update certificate request to completed
            $certificateRequest->update([
                'status' => 'completed',
                'certificate_number' => $certificateNumber,
                'completed_at' => now(),
                'completed_by' => Auth::id()
            ]);

            // Create or update certificate record (if you have a separate certificates table)
            $certificate = Certificate::updateOrCreate(
                ['certificate_request_id' => $certificateRequest->id],
                [
                    'student_id' => $certificateRequest->student_id,
                    'course_id' => $certificateRequest->course_id,
                    'franchise_id' => $certificateRequest->franchise_id,
                    'number' => $certificateNumber,
                    'status' => 'issued',
                    'issued_at' => now(),
                    'issued_by' => Auth::id()
                ]
            );

            DB::commit();

            Log::info('Certificate marked as completed', [
                'request_id' => $certificateRequest->id,
                'certificate_number' => $certificateNumber,
                'completed_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Certificate marked as completed! Certificate Number: ' . $certificateNumber,
                'data' => [
                    'certificate_number' => $certificateNumber,
                    'certificate_id' => $certificate->id ?? null,
                    'status' => 'completed'
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Certificate completion failed', [
                'error' => $e->getMessage(),
                'request_id' => $certificateRequest->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to complete certificate: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Undo completion (revert to paid status)
     */
    public function undoComplete(CertificateRequest $certificateRequest)
    {
        if ($certificateRequest->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Only completed requests can be reverted.'
            ]);
        }

        DB::beginTransaction();

        try {
            $certificateRequest->update([
                'status' => 'paid',
                'completed_at' => null,
                'completed_by' => null
            ]);

            // Optionally delete the certificate record if it shouldn't exist for non-completed requests
            // Certificate::where('certificate_request_id', $certificateRequest->id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Certificate completion has been reverted to processing status.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to revert: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getActionButtons($request)
    {
        $buttons = '<div class="btn-group btn-group-sm" role="group">';

        // View Details Button
        $buttons .= '<a href="' . route('admin.certificate-requests.show', $request->id) . '"
                        class="btn btn-info"
                        data-toggle="tooltip"
                        title="View Details">
                        <i class="fas fa-eye"></i>
                    </a>';

        // Approve/Reject Buttons (if pending)
        if ($request->status === 'pending') {
            $buttons .= '<button onclick="showApproveModal(' . $request->id . ')"
                                class="btn btn-success"
                                title="Approve"
                                data-toggle="tooltip">
                                <i class="fas fa-check"></i>
                        </button>';

            $buttons .= '<button onclick="showRejectModal(' . $request->id . ')"
                                class="btn btn-danger"
                                title="Reject"
                                data-toggle="tooltip">
                                <i class="fas fa-times"></i>
                        </button>';
        }

        // ✅ NEW: Complete Button (if paid)
        if ($request->status === 'paid') {
            $buttons .= '<button onclick="markAsCompleted(' . $request->id . ')"
                                class="btn btn-primary"
                                title="Mark as Completed"
                                data-toggle="tooltip">
                                <i class="fas fa-certificate"></i> Complete
                        </button>';
        }

        // ✅ NEW: Undo Button (if completed)
        if ($request->status === 'completed') {
            $buttons .= '<button onclick="undoComplete(' . $request->id . ')"
                                class="btn btn-warning"
                                title="Undo Completion"
                                data-toggle="tooltip">
                                <i class="fas fa-undo"></i>
                        </button>';
        }

        // History Button
        $buttons .= '<button onclick="showTimeline(' . $request->id . ')"
                            class="btn btn-secondary"
                            title="History"
                            data-toggle="tooltip">
                            <i class="fas fa-history"></i>
                    </button>';

        $buttons .= '</div>';

        return $buttons;
    }


}
