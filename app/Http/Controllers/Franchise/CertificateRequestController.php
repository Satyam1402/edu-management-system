<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\CertificateRequest;
use App\Models\Student;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\FranchiseWallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Cache;
class CertificateRequestController extends Controller
{
    /**
     * Display certificate requests with DataTables
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $franchiseId = Auth::user()->franchise_id;

            $query = CertificateRequest::with(['student', 'course', 'enrollment'])
                ->where('franchise_id', $franchiseId)
                ->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('student_name', function ($row) {
                    return $row->student ? $row->student->full_name : 'N/A';
                })
                ->addColumn('course_name', function ($row) {
                    return $row->course ? $row->course->name : 'N/A';
                })
                ->addColumn('amount_formatted', function ($row) {
                    return '₹' . number_format($row->amount, 2);
                })
                ->addColumn('status_badge', function ($row) {
                    return $this->getStatusBadge($row);
                })
                ->addColumn('payment_badge', function ($row) {
                    return $this->getPaymentBadge($row);
                })
                ->addColumn('requested_date', function ($row) {
                    return $row->created_at ? $row->created_at->format('d M Y, h:i A') : 'N/A';
                })
                ->addColumn('action', function ($row) {
                    return $this->getActionButtons($row);
                })
                ->rawColumns(['status_badge', 'payment_badge', 'action'])
                ->make(true);
        }

        $franchiseId = Auth::user()->franchise_id;

        // Get stats
        $stats = [
            'pending' => CertificateRequest::where('franchise_id', $franchiseId)->where('status', 'pending')->count(),
            'approved' => CertificateRequest::where('franchise_id', $franchiseId)->where('status', 'approved')->where('payment_status', 'pending')->count(),
            'completed' => CertificateRequest::where('franchise_id', $franchiseId)->where('status', 'completed')->count(),
        ];

        // Get wallet balance
        $wallet = FranchiseWallet::where('franchise_id', $franchiseId)->first();
        $walletBalance = $wallet ? $wallet->balance : 0;

        return view('franchise.certificate-requests.index', compact('stats', 'walletBalance'));
    }

    public function create()
    {
        $franchiseId = Auth::user()->franchise_id;

        if (!$franchiseId) {
            return redirect()->back()->with('error', 'Your account is not associated with any franchise.');
        }

        // Get ONLY eligible students
        $eligibleStudents = $this->getEligibleStudents($franchiseId);

        // Get wallet balance
        $wallet = FranchiseWallet::where('franchise_id', $franchiseId)->first();
        $walletBalance = $wallet ? $wallet->balance : 0;

        return view('franchise.certificate-requests.create', compact('eligibleStudents', 'walletBalance'));
    }

    /**
     * Store certificate request (NO PAYMENT YET)
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'student_id' => 'required|exists:students,id',
                'certificate_type' => 'nullable|string|max:255',
                'notes' => 'nullable|string'
            ]);

            $franchiseId = Auth::user()->franchise_id;

            if (!$franchiseId) {
                return redirect()->back()->with('error', 'Your account is not associated with any franchise.');
            }

            // Get student and verify they belong to this franchise
            $student = Student::where('id', $request->student_id)
                ->where('franchise_id', $franchiseId)
                ->with('course') // ✅ Added: Load course relationship
                ->firstOrFail();

            // ✅ Changed: Check if student has a course (no enrollment table)
            if (!$student->course_id || !$student->course) {
                return redirect()->back()
                    ->with('error', 'Student is not enrolled in any course.')
                    ->withInput();
            }

            // ❌ Removed: Strict eligibility check
            // if (!$student->isEligibleForCertificate()) {
            //     return redirect()->back()
            //         ->with('error', 'Student has not completed all required exams for certificate eligibility.')
            //         ->withInput();
            // }

            // ✅ Changed: Use student's course_id directly
            $existingRequest = CertificateRequest::where('student_id', $student->id)
                ->where('course_id', $student->course_id)
                ->whereIn('status', ['pending', 'approved', 'paid', 'completed'])
                ->exists();

            if ($existingRequest) {
                return redirect()->back()
                    ->with('error', 'A certificate request already exists for this student and course.')
                    ->withInput();
            }

            // ✅ Changed: Get fee from student's course
            $certificateFee = $student->course->certificate_fee ?? 500.00;

            // ✅ Changed: CREATE REQUEST WITHOUT PAYMENT
            $certificateRequest = CertificateRequest::create([
                'franchise_id' => $franchiseId,
                'student_id' => $student->id,
                'course_id' => $student->course_id, // ✅ Changed: Use student's course_id
                'enrollment_id' => null, // ✅ Changed: Not using enrollments
                'certificate_type' => $request->certificate_type ?? 'Course Completion Certificate',
                'amount' => $certificateFee,
                'status' => 'pending',
                'payment_status' => 'pending',
                'notes' => $request->notes,
                'requested_at' => now()
            ]);

            return redirect()->route('franchise.certificate-requests.index')
                ->with('success', 'Certificate request submitted successfully! It will be reviewed by the admin.');

        } catch (\Exception $e) {
            Log::error('Certificate Request Error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to create certificate request: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show certificate request details
     */
    public function show(CertificateRequest $certificateRequest)
    {
        $franchiseId = Auth::user()->franchise_id;

        if ($certificateRequest->franchise_id !== $franchiseId) {
            abort(403, 'Unauthorized access.');
        }

        $certificateRequest->load(['student', 'course', 'enrollment', 'approvedBy', 'rejectedBy', 'walletTransaction']);

        return view('franchise.certificate-requests.show', compact('certificateRequest'));
    }

    /**
     * ✅ NEW: Cancel/Delete a pending certificate request
     */
    public function destroy(CertificateRequest $certificateRequest)
    {
        $franchiseId = Auth::user()->franchise_id;

        // Verify ownership
        if ($certificateRequest->franchise_id !== $franchiseId) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        // Only allow deletion if pending
        if ($certificateRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending requests can be cancelled.');
        }

        // Get student and course names for success message
        $studentName = $certificateRequest->student->full_name ?? 'Student';
        $courseName = $certificateRequest->course->name ?? 'Course';

        // Delete the request
        $certificateRequest->delete();

        return redirect()->route('franchise.certificate-requests.index')
            ->with('success', "Certificate request for {$studentName} - {$courseName} has been cancelled successfully.");
    }

    /**
     * Pay for approved certificate request
     */
    public function pay(CertificateRequest $certificateRequest)
    {
        $franchiseId = Auth::user()->franchise_id;

        // Verify ownership
        if ($certificateRequest->franchise_id !== $franchiseId) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        // Check if can be paid
        if (!$certificateRequest->canBePaid()) {
            return redirect()->back()->with('error', 'This request cannot be paid at this time.');
        }

        DB::beginTransaction();

        try {
            // Get wallet
            $wallet = FranchiseWallet::where('franchise_id', $franchiseId)->firstOrFail();

            // Check balance
            if (!$wallet->hasSufficientBalance($certificateRequest->amount)) {
                return redirect()->route('franchise.wallet.index')
                    ->with('error', 'Insufficient wallet balance. Please add funds first. Required: ₹' . number_format($certificateRequest->amount, 2));
            }

            // Deduct from wallet
            $transaction = $wallet->debit(
                $certificateRequest->amount,
                "Payment for certificate - {$certificateRequest->student->full_name} - {$certificateRequest->course->name}"
            );

            // Update certificate request
            $certificateRequest->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'wallet_transaction_id' => $transaction->id,
                'status' => 'paid' // Move to processing
            ]);

            DB::commit();

            return redirect()->route('franchise.certificate-requests.show', $certificateRequest)
                ->with('success', 'Payment successful! Your certificate is now being processed.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Certificate Payment Error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Get eligible students for certificate
     */
    private function getEligibleStudents($franchiseId)
    {
        return Student::where('franchise_id', $franchiseId)
            ->where('status', 'active')
            ->whereNotNull('course_id')
            ->with('course:id,name,certificate_fee')
            ->select('id', 'name', 'middle_name', 'last_name', 'email', 'course_id')
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->full_name,
                    'email' => $student->email,
                    'course_id' => $student->course_id,
                    'course_name' => $student->course->name ?? 'N/A',
                    'enrollment_id' => null,
                    'is_eligible' => true,
                    'certificate_fee' => $student->course->certificate_fee ?? 500
                ];
            })
            ->values();
    }


    /**
     * Get status badge HTML
     */
    private function getStatusBadge($request)
    {
        $badges = [
            'pending' => '<span class="badge badge-warning"><i class="fas fa-clock"></i> Pending Review</span>',
            'approved' => '<span class="badge badge-info"><i class="fas fa-check-circle"></i> Approved</span>',
            'paid' => '<span class="badge badge-success"><i class="fas fa-money-bill"></i> Processing</span>',
            'completed' => '<span class="badge badge-primary"><i class="fas fa-certificate"></i> Completed</span>',
            'rejected' => '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Rejected</span>',
        ];

        return $badges[$request->status] ?? '<span class="badge badge-secondary">Unknown</span>';
    }

    /**
     * Get payment status badge HTML
     */
    private function getPaymentBadge($request)
    {
        if ($request->payment_status === 'paid') {
            return '<span class="badge badge-success"><i class="fas fa-check"></i> Paid</span>';
        }

        if ($request->status === 'approved' && $request->payment_status === 'pending') {
            return '<span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>';
        }

        return '<span class="badge badge-secondary">N/A</span>';
    }

    /**
     * Get action buttons HTML
     */
    private function getActionButtons($request)
    {
        $buttons = '<div class="btn-group" role="group">';

        // View button (always available)
        $buttons .= '<a href="' . route('franchise.certificate-requests.show', $request->id) . '"
                        class="btn btn-sm btn-info"
                        data-toggle="tooltip"
                        title="View Details">
                        <i class="fas fa-eye"></i>
                    </a>';

        // Cancel button (only for pending)
        if ($request->status === 'pending') {
            $buttons .= '<button class="btn btn-sm btn-danger"
                                onclick="cancelRequest(' . $request->id . ')"
                                data-toggle="tooltip"
                                title="Cancel Request">
                                <i class="fas fa-times"></i>
                        </button>';
        }

        // Pay button (if approved and not paid)
        if ($request->canBePaid()) {
            $buttons .= '<button class="btn btn-sm btn-success pay-now-btn"
                                data-id="' . $request->id . '"
                                data-student="' . htmlspecialchars($request->student->full_name ?? '') . '"
                                data-course="' . htmlspecialchars($request->course->name ?? '') . '"
                                data-amount="' . $request->amount . '"
                                data-toggle="tooltip"
                                title="Pay Now">
                                <i class="fas fa-money-bill-wave"></i>
                        </button>';
        }

        // Download button (if completed)
        if ($request->status === 'completed' && $request->certificate_number) {
            $buttons .= '<a href="' . route('franchise.certificates.download', $request->id) . '"
                            class="btn btn-sm btn-primary"
                            data-toggle="tooltip"
                            title="Download Certificate">
                            <i class="fas fa-download"></i>
                        </a>';
        }

        $buttons .= '</div>';

        return $buttons;
    }

    // ========================================
    // OPTIONAL AJAX METHODS (If routes exist)
    // ========================================

    /**
     * Get wallet balance (AJAX)
     */
    public function getWalletBalance()
    {
        $franchiseId = Auth::user()->franchise_id;
        $wallet = FranchiseWallet::where('franchise_id', $franchiseId)->first();

        return response()->json([
            'success' => true,
            'balance' => $wallet ? $wallet->balance : 0,
            'formatted_balance' => '₹' . number_format($wallet ? $wallet->balance : 0, 2)
        ]);
    }

    /**
     * Get course certificate fee (AJAX)
     */
    public function getCourseFee(Course $course)
    {
        return response()->json([
            'success' => true,
            'fee' => $course->certificate_fee ?? 500,
            'course_name' => $course->name,
            'formatted_fee' => '₹' . number_format($course->certificate_fee ?? 500, 2)
        ]);
    }

    /**
     * Calculate total cost (AJAX)
     */
    public function calculateCost(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'student_count' => 'required|integer|min:1'
        ]);

        $course = Course::findOrFail($request->course_id);
        $fee = $course->certificate_fee ?? 500;
        $total = $fee * $request->student_count;

        return response()->json([
            'success' => true,
            'fee_per_certificate' => $fee,
            'total_amount' => $total,
            'formatted_fee' => '₹' . number_format($fee, 2),
            'formatted_total' => '₹' . number_format($total, 2),
            'course_name' => $course->name
        ]);
    }

    /**
     * Download certificate (placeholder - implement PDF generation)
     */
    public function download(CertificateRequest $certificateRequest)
    {
        $franchiseId = Auth::user()->franchise_id;

        // Verify ownership
        if ($certificateRequest->franchise_id !== $franchiseId) {
            abort(403, 'Unauthorized access.');
        }

        // Check if certificate is completed
        if ($certificateRequest->status !== 'completed') {
            return redirect()->back()->with('error', 'Certificate is not yet ready for download.');
        }

        // TODO: Implement PDF generation
        // For now, redirect with message
        return redirect()->back()->with('info', 'Certificate download will be available soon. Certificate Number: ' . $certificateRequest->certificate_number);
    }
}
