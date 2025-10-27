<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateRequest;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CertificateRequestController extends Controller
{
    // List all certificate requests from all franchises
    public function index(Request $request)
    {
        $query = CertificateRequest::with(['franchise', 'student', 'course', 'payment'])
            ->orderBy('created_at', 'desc');

        // Filter by status if provided
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by franchise if provided
        if ($request->has('franchise_id') && $request->franchise_id != '') {
            $query->where('franchise_id', $request->franchise_id);
        }

        // Search by student name
        if ($request->has('search') && $request->search != '') {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->ajax()) {
            return datatables($query)
                ->addIndexColumn()
                ->addColumn('franchise_name', function($row) {
                    return $row->franchise->name ?? 'N/A';
                })
                ->addColumn('student_info', function($row) {
                    return '<div><strong>' . $row->student->name . '</strong><br><small class="text-muted">' . $row->student->email . '</small></div>';
                })
                ->addColumn('payment_status', function($row) {
                    if ($row->payment) {
                        return '<span class="badge badge-success">Paid - ₹' . number_format($row->payment->amount, 2) . '</span>';
                    }
                    return '<span class="badge badge-warning">No Payment</span>';
                })
                ->addColumn('status_badge', function($row) {
                    $class = $row->status === 'approved' ? 'success' :
                            ($row->status === 'rejected' ? 'danger' : 'warning');
                    return '<span class="badge badge-' . $class . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function($row) {
                    $actions = '<a href="' . route('admin.certificate-requests.show', $row) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';

                    if ($row->status === 'pending') {
                        $actions .= '<button class="btn btn-sm btn-success" onclick="approveRequest(' . $row->id . ')"><i class="fas fa-check"></i></button> ';
                        $actions .= '<button class="btn btn-sm btn-danger" onclick="rejectRequest(' . $row->id . ')"><i class="fas fa-times"></i></button>';
                    }

                    return $actions;
                })
                ->rawColumns(['student_info', 'payment_status', 'status_badge', 'action'])
                ->make(true);
        }

        $requests = $query->paginate(20);

        // Get franchises for filter dropdown
        $franchises = \App\Models\User::where('role', 'franchise')->select('id', 'name')->get();

        return view('admin.certificate-requests.index', compact('requests', 'franchises'));
    }

    // Show specific certificate request details
    public function show(CertificateRequest $certificateRequest)
    {
        $certificateRequest->load(['franchise', 'student', 'course', 'payment']);
        return view('admin.certificate-requests.show', compact('certificateRequest'));
    }

    // Approve certificate request and create certificate
    public function approve(Request $request, CertificateRequest $certificateRequest)
    {
        try {
            DB::beginTransaction();

            // Check if request is still pending
            if ($certificateRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This request has already been processed.'
                ]);
            }

            // Update request status
            $certificateRequest->update(['status' => 'approved']);

            // Create the actual certificate
            Certificate::create([
                'student_id' => $certificateRequest->student_id,
                'course_id' => $certificateRequest->course_id,
                'title' => $request->get('title', 'Certificate of Completion'),
                'description' => $request->get('description', 'This certifies that the student has successfully completed the requirements.'),
                'issued_date' => now(),
                'number' => $this->generateCertificateNumber(),
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Certificate request approved and certificate issued successfully!'
                ]);
            }

            return redirect()->back()->with('success', 'Certificate request approved and certificate issued successfully!');

        } catch (\Exception $e) {
            DB::rollback();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while processing the request.'
                ]);
            }

            return redirect()->back()->with('error', 'An error occurred while processing the request.');
        }
    }

    // Reject certificate request
    public function reject(Request $request, CertificateRequest $certificateRequest)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500'
        ]);

        // Check if request is still pending
        if ($certificateRequest->status !== 'pending') {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This request has already been processed.'
                ]);
            }
            return redirect()->back()->with('error', 'This request has already been processed.');
        }

        // Update request status
        $certificateRequest->update([
            'status' => 'rejected',
            'note' => $request->rejection_reason ? 'Rejected: ' . $request->rejection_reason : 'Request rejected by admin'
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Certificate request rejected successfully!'
            ]);
        }

        return redirect()->back()->with('success', 'Certificate request rejected successfully!');
    }

    // Bulk actions for multiple requests
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'requests' => 'required|array',
            'requests.*' => 'exists:certificate_requests,id'
        ]);

        try {
            DB::beginTransaction();

            $requests = CertificateRequest::whereIn('id', $request->requests)
                ->where('status', 'pending')
                ->get();

            $processed = 0;

            foreach ($requests as $certRequest) {
                if ($request->action === 'approve') {
                    $certRequest->update(['status' => 'approved']);

                    // Create certificate
                    Certificate::create([
                        'student_id' => $certRequest->student_id,
                        'course_id' => $certRequest->course_id,
                        'title' => 'Certificate of Completion',
                        'description' => 'This certifies that the student has successfully completed the requirements.',
                        'issued_date' => now(),
                        'number' => $this->generateCertificateNumber(),
                    ]);
                } else {
                    $certRequest->update([
                        'status' => 'rejected',
                        'note' => 'Bulk rejected by admin'
                    ]);
                }
                $processed++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully {$request->action}ed {$processed} certificate requests!"
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the requests.'
            ]);
        }
    }

    // Generate unique certificate number
    private function generateCertificateNumber()
    {
        do {
            $number = 'CERT-' . strtoupper(uniqid());
        } while (Certificate::where('number', $number)->exists());

        return $number;
    }
}
