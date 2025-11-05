<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\CertificateRequest;
use App\Models\Student;
use App\Models\Course;
use App\Models\FranchiseWallet;
use App\Models\FranchiseWalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class CertificateRequestController extends Controller
{
    /**
     * Display certificate requests index with DataTables
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $userFranchiseId = Auth::user()->franchise_id;

            $requests = CertificateRequest::with(['student', 'course'])
                ->where('franchise_id', $userFranchiseId)
                ->orderBy('created_at', 'desc')
                ->select('certificate_requests.*');

            return DataTables::of($requests)
                ->addIndexColumn()
                ->editColumn('student_name', function($row) {
                    return $row->student ?
                        '<div><strong>' . e($row->student->name) . '</strong><br><small class="text-muted">' . e($row->student->email) . '</small></div>' :
                        '<span class="text-muted">N/A</span>';
                })
                ->editColumn('course_name', function($row) {
                    return $row->course ? e($row->course->name) : '<em class="text-muted">No Course</em>';
                })
                ->editColumn('amount_formatted', function($row) {
                    return '<strong class="text-success">₹' . number_format($row->amount, 2) . '</strong>';
                })
                ->editColumn('status_badge', function($row) {
                    $badgeClasses = [
                        'pending' => 'warning',
                        'processing' => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'completed' => 'primary'
                    ];
                    $class = $badgeClasses[$row->status] ?? 'secondary';
                    return "<span class='badge badge-{$class}'><i class='fas fa-" . $this->getStatusIcon($row->status) . "'></i> " . ucfirst($row->status) . "</span>";
                })
                ->editColumn('requested_date', function($row) {
                    return '<div><strong>' . $row->created_at->format('M d, Y') . '</strong><br><small class="text-muted">' . $row->created_at->format('h:i A') . '</small></div>';
                })
                ->addColumn('action', function($row) {
                    $actions = '<div class="btn-group btn-group-sm" role="group">';
                    $actions .= '<a href="' . route('franchise.certificate-requests.show', $row->id) . '" class="btn btn-info btn-sm" title="View Details"><i class="fas fa-eye"></i></a>';

                    if ($row->status === 'pending') {
                        $actions .= '<button class="btn btn-warning btn-sm" onclick="editRequest(' . $row->id . ')" title="Edit"><i class="fas fa-edit"></i></button>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['student_name', 'course_name', 'amount_formatted', 'status_badge', 'requested_date', 'action'])
                ->make(true);
        }

        return view('franchise.certificate-requests.index');
    }

    /**
     * Show form for creating new certificate requests
     */
    public function create()
    {
        $userFranchiseId = Auth::user()->franchise_id;

        if (!$userFranchiseId) {
            return redirect()->back()->with('error', 'Your account is not associated with any franchise.');
        }

        // Get active students for this franchise
        $students = Student::where('franchise_id', $userFranchiseId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone']);

        // Get active courses with certificate fees
        $courses = Course::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'certificate_fee', 'description']);

        // Get current wallet balance
        $wallet = FranchiseWallet::where('franchise_id', $userFranchiseId)->first();
        $walletBalance = $wallet ? $wallet->balance : 0;

        return view('franchise.certificate-requests.create', compact('students', 'courses', 'walletBalance'));
    }

    /**
     * Store new certificate requests (bulk processing)
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'certificate_type' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500'
        ]);

        $userFranchiseId = Auth::user()->franchise_id;

        DB::beginTransaction();

        try {
            // 1. Get course details
            $course = Course::findOrFail($request->course_id);
            $certificateFee = $course->certificate_fee ?? 500; // Default fee

            // 2. Validate students belong to franchise
            $students = Student::whereIn('id', $request->student_ids)
                ->where('franchise_id', $userFranchiseId)
                ->where('status', 'active')
                ->get();

            if ($students->count() !== count($request->student_ids)) {
                return redirect()->back()->with('error', 'Some selected students are invalid or inactive.');
            }

            // 3. Calculate total cost
            $totalAmount = $certificateFee * $students->count();

            // 4. Check wallet balance
            $wallet = FranchiseWallet::where('franchise_id', $userFranchiseId)->first();
            if (!$wallet || $wallet->balance < $totalAmount) {
                return redirect()->back()->with('error',
                    'Insufficient wallet balance. Required: ₹' . number_format($totalAmount, 2) .
                    ', Available: ₹' . number_format($wallet ? $wallet->balance : 0, 2)
                );
            }

            // 5. Check for duplicate requests
            $existingRequests = CertificateRequest::where('course_id', $request->course_id)
                ->whereIn('student_id', $request->student_ids)
                ->whereNotIn('status', ['rejected', 'cancelled'])
                ->exists();

            if ($existingRequests) {
                return redirect()->back()->with('error', 'Some students already have pending/approved certificate requests for this course.');
            }

            // 6. Create certificate requests
            $requestIds = [];
            foreach ($students as $student) {
                $certificateRequest = CertificateRequest::create([
                    'franchise_id' => $userFranchiseId,
                    'student_id' => $student->id,
                    'course_id' => $request->course_id,
                    'amount' => $certificateFee,
                    'certificate_type' => $request->certificate_type ?: 'Standard Certificate',
                    'status' => 'pending',
                    'notes' => $request->notes,
                    'requested_at' => now(),
                ]);

                $requestIds[] = $certificateRequest->id;
            }

            // 7. Deduct from wallet
            $wallet->decrement('balance', $totalAmount);

            // 8. Create wallet transaction
            FranchiseWalletTransaction::create([
                'franchise_id' => $userFranchiseId,
                'type' => 'debit',
                'amount' => $totalAmount,
                'source' => 'certificate_request',
                'reference_id' => implode(',', $requestIds), // Store all request IDs
                'description' => "Certificate requests for {$students->count()} students - {$course->name}",
                'meta' => json_encode([
                    'course_id' => $request->course_id,
                    'course_name' => $course->name,
                    'student_count' => $students->count(),
                    'fee_per_certificate' => $certificateFee,
                    'certificate_requests' => $requestIds
                ])
            ]);

            DB::commit();

            return redirect()->route('franchise.certificate-requests.index')
                ->with('success', "🎉 {$students->count()} certificate request(s) submitted successfully! ₹" . number_format($totalAmount, 2) . " deducted from wallet.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Certificate Request Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'franchise_id' => $userFranchiseId,
                'request_data' => $request->all()
            ]);

            return redirect()->back()->with('error', 'Failed to process certificate requests. Please try again.');
        }
    }

    /**
     * Show specific certificate request details
     */
    public function show(CertificateRequest $certificateRequest)
    {
        $userFranchiseId = Auth::user()->franchise_id;

        if ($certificateRequest->franchise_id !== $userFranchiseId) {
            abort(403, 'Unauthorized access to this certificate request.');
        }

        $certificateRequest->load(['student', 'course', 'approvedBy', 'rejectedBy']);

        return view('franchise.certificate-requests.show', compact('certificateRequest'));
    }

    /**
     * Get course certificate fee (AJAX)
     */
    public function getCourseFee(Course $course)
    {
        return response()->json([
            'success' => true,
            'fee' => $course->certificate_fee ?? 500,
            'course_name' => $course->name
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
            'formatted_total' => '₹' . number_format($total, 2),
            'course_name' => $course->name
        ]);
    }

    /**
     * Get wallet balance (AJAX)
     */
    public function getWalletBalance()
    {
        $userFranchiseId = Auth::user()->franchise_id;
        $wallet = FranchiseWallet::where('franchise_id', $userFranchiseId)->first();

        return response()->json([
            'success' => true,
            'balance' => $wallet ? $wallet->balance : 0,
            'formatted_balance' => '₹' . number_format($wallet ? $wallet->balance : 0, 2)
        ]);
    }

    /**
     * Get status icon for badges
     */
    private function getStatusIcon($status)
    {
        return match($status) {
            'pending' => 'clock',
            'processing' => 'spinner',
            'approved' => 'check-circle',
            'rejected' => 'times-circle',
            'completed' => 'certificate',
            default => 'question-circle'
        };
    }
}
