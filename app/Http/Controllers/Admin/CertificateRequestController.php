<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateRequest;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class CertificateRequestController extends Controller
{
    /**
     * Display certificate requests dashboard
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $requests = CertificateRequest::with(['student', 'franchise', 'course', 'payment'])
                ->orderBy('created_at', 'desc');

            return DataTables::of($requests)
                ->addIndexColumn()
                ->addColumn('student_info', function($row) {
                    $franchiseName = $row->franchise->name ?? 'N/A';
                    return "
                        <div class='student-info'>
                            <strong>{$row->student->name}</strong><br>
                            <small class='text-muted'><i class='fas fa-building'></i> {$franchiseName}</small>
                        </div>
                    ";
                })
                ->addColumn('course_info', function($row) {
                    return $row->course ? $row->course->name : '<em class="text-muted">General Certificate</em>';
                })
                ->addColumn('payment_info', function($row) {
                    if ($row->payment) {
                        $badge = $row->payment->status === 'completed' ? 'success' : 'warning';
                        $icon = $row->payment->status === 'completed' ? 'check-circle' : 'clock';
                        return "
                            <div class='text-center'>
                                <span class='badge badge-{$badge}'>
                                    <i class='fas fa-{$icon} mr-1'></i>₹{$row->payment->amount}
                                </span><br>
                                <small class='text-muted'>{$row->payment->status}</small>
                            </div>
                        ";
                    }
                    return "<div class='text-center'><span class='badge badge-secondary'><i class='fas fa-times'></i> No Payment</span></div>";
                })
                ->addColumn('status_badge', function($row) {
                    $colors = [
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'issued' => 'info'
                    ];
                    $icons = [
                        'pending' => 'clock',
                        'approved' => 'check-circle',
                        'rejected' => 'times-circle',
                        'issued' => 'certificate'
                    ];
                    $color = $colors[$row->status] ?? 'secondary';
                    $icon = $icons[$row->status] ?? 'question';
                    return "<span class='badge badge-{$color} status-badge'>
                                <i class='fas fa-{$icon} mr-1'></i>" . ucfirst($row->status) . "
                            </span>";
                })
                ->addColumn('request_date', function($row) {
                    return "<div class='text-center'>
                                <strong>" . $row->created_at->format('d M Y') . "</strong><br>
                                <small class='text-muted'>" . $row->created_at->format('h:i A') . "</small>
                            </div>";
                })
                ->addColumn('actions', function($row) {
                    $actions = '<div class="btn-group-actions">';

                    // View button
                    $actions .= '<a href="' . route('admin.certificate-requests.show', $row->id) . '"
                                   class="btn btn-info btn-sm" title="View Details">
                                   <i class="fas fa-eye"></i>
                                </a>';

                    if ($row->status === 'pending') {
                        // Approve button - only if payment is completed
                        if ($row->payment && $row->payment->status === 'completed') {
                            $actions .= '<button onclick="approveRequest(' . $row->id . ')"
                                               class="btn btn-success btn-sm" title="Approve">
                                               <i class="fas fa-check"></i>
                                        </button>';
                        } else {
                            $actions .= '<button class="btn btn-success btn-sm" disabled title="Payment Required">
                                               <i class="fas fa-check"></i>
                                        </button>';
                        }

                        // Reject button
                        $actions .= '<button onclick="showRejectModal(' . $row->id . ')"
                                           class="btn btn-danger btn-sm" title="Reject">
                                           <i class="fas fa-times"></i>
                                    </button>';
                    } else {
                        // View Certificate button if approved and certificate exists
                        if ($row->status === 'approved') {
                            $certificate = Certificate::where('certificate_request_id', $row->id)->first();
                            if ($certificate) {
                                $actions .= '<a href="' . route('admin.certificates.show', $certificate->id) . '"
                                                   class="btn btn-primary btn-sm" title="View Certificate">
                                                   <i class="fas fa-certificate"></i>
                                            </a>';
                            }
                        }
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['student_info', 'course_info', 'payment_info', 'status_badge', 'request_date', 'actions'])
                ->make(true);
        }

        // Get stats for dashboard cards
        $stats = [
            'total' => CertificateRequest::count(),
            'pending' => CertificateRequest::where('status', 'pending')->count(),
            'approved' => CertificateRequest::where('status', 'approved')->count(),
            'rejected' => CertificateRequest::where('status', 'rejected')->count(),
        ];

        return view('admin.certificate-requests.index', compact('stats'));
    }

    /**
     * Show certificate request details
     */
    public function show(CertificateRequest $certificateRequest)
    {
        $certificateRequest->load(['student', 'franchise', 'course', 'payment', 'approvedBy', 'rejectedBy']);

        // Get the certificate if it exists (check by certificate_request_id)
        $certificate = Certificate::where('certificate_request_id', $certificateRequest->id)->first();
        $certificateRequest->certificate = $certificate;

        return view('admin.certificate-requests.show', compact('certificateRequest'));
    }

    /**
     * Approve certificate request
     */
    public function approve(Request $request, CertificateRequest $certificateRequest)
    {
        try {
            if ($certificateRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request is not in pending status.'
                ]);
            }

            // Check if payment is completed
            if (!$certificateRequest->payment || $certificateRequest->payment->status !== 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not completed. Cannot approve request.'
                ]);
            }

            // Update status to approved
            $certificateRequest->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'admin_notes' => $request->notes ?? null
            ]);

            // Create actual certificate record using YOUR existing Certificate model structure
            $certificate = Certificate::create([
                'student_id' => $certificateRequest->student_id,
                'course_id' => $certificateRequest->course_id,
                'certificate_request_id' => $certificateRequest->id, // Link to request
                'number' => Certificate::generateCertificateNumber($certificateRequest->student_id), // Use your 'number' field
                'status' => 'issued', // Set as issued
                'issued_at' => now(), // Use your 'issued_at' field
                'issued_by' => auth()->id() // Who issued it
            ]);

            // Log the approval
            Log::info('Certificate request approved', [
                'request_id' => $certificateRequest->id,
                'approved_by' => auth()->id(),
                'certificate_id' => $certificate->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Certificate request approved successfully! Certificate created.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error approving certificate request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error approving request. Please try again.'
            ]);
        }
    }

    /**
     * Reject certificate request
     */
    public function reject(Request $request, CertificateRequest $certificateRequest)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            if ($certificateRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request is not in pending status.'
                ]);
            }

            // Update status to rejected
            $certificateRequest->update([
                'status' => 'rejected',
                'rejected_by' => auth()->id(),
                'rejected_at' => now(),
                'rejection_reason' => $request->reason,
                'admin_notes' => $request->notes ?? null
            ]);

            // Log the rejection
            Log::info('Certificate request rejected', [
                'request_id' => $certificateRequest->id,
                'rejected_by' => auth()->id(),
                'reason' => $request->reason
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Certificate request rejected successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error rejecting certificate request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting request. Please try again.'
            ]);
        }
    }

    /**
     * Handle bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:certificate_requests,id',
            'reason' => 'required_if:action,reject|string|max:500'
        ]);

        try {
            $updated = 0;
            $skipped = 0;

            $requests = CertificateRequest::with('payment')
                                        ->whereIn('id', $request->request_ids)
                                        ->where('status', 'pending')
                                        ->get();

            foreach ($requests as $certificateRequest) {
                if ($request->action === 'approve') {
                    // Check payment status before approving
                    if ($certificateRequest->payment && $certificateRequest->payment->status === 'completed') {
                        $certificateRequest->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now()
                        ]);

                        // Create certificate using YOUR Certificate model structure
                        Certificate::create([
                            'student_id' => $certificateRequest->student_id,
                            'course_id' => $certificateRequest->course_id,
                            'certificate_request_id' => $certificateRequest->id,
                            'number' => Certificate::generateCertificateNumber($certificateRequest->student_id),
                            'status' => 'issued',
                            'issued_at' => now(),
                            'issued_by' => auth()->id()
                        ]);

                        $updated++;
                    } else {
                        $skipped++;
                    }
                } elseif ($request->action === 'reject') {
                    $certificateRequest->update([
                        'status' => 'rejected',
                        'rejected_by' => auth()->id(),
                        'rejected_at' => now(),
                        'rejection_reason' => $request->reason
                    ]);
                    $updated++;
                }
            }

            $message = "Successfully {$request->action}d {$updated} requests.";
            if ($skipped > 0) {
                $message .= " {$skipped} requests skipped due to incomplete payments.";
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            Log::error('Error in bulk action: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing bulk action. Please try again.'
            ]);
        }
    }

    /**
     * Export certificate requests
     */
    public function export()
    {
        $requests = CertificateRequest::with(['student', 'franchise', 'course', 'payment'])
            ->orderBy('created_at', 'desc')
            ->get();

        $csvData = "ID,Student Name,Franchise,Course,Payment Status,Request Status,Request Date\n";

        foreach ($requests as $request) {
            $csvData .= sprintf(
                "%d,%s,%s,%s,%s,%s,%s\n",
                $request->id,
                $request->student->name,
                $request->franchise->name ?? 'N/A',
                $request->course->name ?? 'General',
                $request->payment->status ?? 'N/A',
                $request->status,
                $request->created_at->format('Y-m-d H:i:s')
            );
        }

        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="certificate_requests_' . date('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Get stats for dashboard
     */
    public function getStats()
    {
        $stats = [
            'total_requests' => CertificateRequest::count(),
            'pending_requests' => CertificateRequest::where('status', 'pending')->count(),
            'approved_today' => CertificateRequest::where('status', 'approved')
                                               ->whereDate('approved_at', today())
                                               ->count(),
            'total_certificates' => Certificate::count(),
            'recent_requests' => CertificateRequest::with(['student', 'course', 'franchise'])
                                                  ->orderBy('created_at', 'desc')
                                                  ->take(5)
                                                  ->get()
        ];

        return response()->json($stats);
    }
}
