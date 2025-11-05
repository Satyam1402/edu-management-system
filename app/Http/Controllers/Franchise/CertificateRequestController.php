<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\CertificateRequest;
use App\Models\Student;
use App\Models\Course;
use App\Models\FranchiseWallet;
use App\Models\Franchise;
use App\Models\WalletTransaction;
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
    // If AJAX request (DataTables)
    if ($request->ajax()) {
        $franchiseId = Auth::user()->franchise_id;
        
        $query = CertificateRequest::with(['student', 'course', 'franchise'])
            ->where('franchise_id', $franchiseId);

        // Apply filters
        if ($request->has('status_filter') && $request->status_filter != '') {
            $query->where('status', $request->status_filter);
        }

        if ($request->has('course_filter') && $request->course_filter != '') {
            $query->where('course_id', $request->course_filter);
        }

        // Date range filter
        if ($request->has('date_range') && $request->date_range != '') {
            $dateRange = $request->date_range;
            $now = now();
            
            switch ($dateRange) {
                case 'today':
                    $query->whereDate('created_at', $now->toDateString());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', $now->month)
                          ->whereYear('created_at', $now->year);
                    break;
                case 'quarter':
                    $query->whereBetween('created_at', [$now->startOfQuarter(), $now->endOfQuarter()]);
                    break;
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('student_name', function ($row) {
                return $row->student ? $row->student->name : 'N/A';
            })
            ->addColumn('course_name', function ($row) {
                return $row->course ? $row->course->name : 'N/A';
            })
            ->addColumn('amount_formatted', function ($row) {
                return '₹' . number_format($row->amount, 2);
            })
            ->addColumn('status_badge', function ($row) {
                $badges = [
                    'pending' => '<span class="status-badge status-pending"><i class="fas fa-clock"></i> Pending</span>',
                    'processing' => '<span class="status-badge status-processing"><i class="fas fa-spinner"></i> Processing</span>',
                    'approved' => '<span class="status-badge status-approved"><i class="fas fa-check"></i> Approved</span>',
                    'rejected' => '<span class="status-badge status-rejected"><i class="fas fa-times"></i> Rejected</span>',
                    'completed' => '<span class="status-badge status-completed"><i class="fas fa-certificate"></i> Completed</span>',
                ];
                
                return $badges[$row->status] ?? '<span class="badge badge-secondary">Unknown</span>';
            })
            ->addColumn('requested_date', function ($row) {
                return $row->created_at ? $row->created_at->format('d M Y, h:i A') : 'N/A';
            })
            ->addColumn('action', function ($row) {
                $buttons = '<div class="action-buttons">';
                
                // View Button (Always available)
                $buttons .= '<a href="' . route('franchise.certificate-requests.show', $row->id) . '" 
                            class="btn btn-info btn-sm" 
                            data-bs-toggle="tooltip" 
                            title="View Details">
                            <i class="fas fa-eye"></i>
                        </a> ';
                
                // Edit Button (Only for pending requests) ← THIS WAS MISSING!
                if ($row->status === 'pending') {
                    $buttons .= '<a href="' . route('franchise.certificate-requests.edit', $row->id) . '" 
                                class="btn btn-warning btn-sm" 
                                data-bs-toggle="tooltip" 
                                title="Edit Request">
                                <i class="fas fa-edit"></i>
                            </a> ';
                }
                
                // Download Button (Only for completed)
                if ($row->status === 'completed' && $row->certificate_number) {
                    $buttons .= '<a href="' . route('franchise.certificate-requests.download', $row->id) . '" 
                                class="btn btn-success btn-sm" 
                                data-bs-toggle="tooltip" 
                                title="Download Certificate">
                                <i class="fas fa-download"></i>
                            </a> ';
                }
                
                $buttons .= '</div>';
                
                return $buttons;
            })
            ->rawColumns(['status_badge', 'action'])
            ->with([
                'stats' => [
                    'pending' => CertificateRequest::where('franchise_id', $franchiseId)->where('status', 'pending')->count(),
                    'approved' => CertificateRequest::where('franchise_id', $franchiseId)->where('status', 'approved')->count(),
                    'completed' => CertificateRequest::where('franchise_id', $franchiseId)->where('status', 'completed')->count(),
                ]
            ])
            ->make(true);
    }

    // Regular page load
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

    public function store(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'course_id' => 'required|exists:courses,id',
                'student_ids' => 'required|array|min:1',
                'student_ids.*' => 'exists:students,id',
                'certificate_type' => 'nullable|string|max:255',
                'notes' => 'nullable|string'
            ]);

            // Get authenticated user's franchise
            $franchiseId = Auth::user()->franchise_id;
            
            if (!$franchiseId) {
                return redirect()->back()->with('error', 'Your account is not associated with any franchise.')->withInput();
            }

            // Get the course with certificate fee
            $course = Course::findOrFail($request->course_id);
            
            // Get certificate fee (default to 100 if not set)
            $certificateFee = $course->certificate_fee ?? 100.00;
            
            // Calculate total amount
            $totalAmount = $certificateFee * count($request->student_ids);

            // Get the franchise
            $franchise = Franchise::findOrFail($franchiseId);
            
            // 🔧 UPDATED: Get or create wallet (prevents duplicate error)
            $wallet = FranchiseWallet::firstOrCreate(
                ['franchise_id' => $franchiseId],
                ['balance' => 0]
            );

            // Check wallet balance
            if ($wallet->balance < $totalAmount) {
                return redirect()->back()
                    ->with('error', 'Insufficient wallet balance. Required: ₹' . number_format($totalAmount, 2) . ', Available: ₹' . number_format($wallet->balance, 2))
                    ->withInput();
            }

            DB::beginTransaction();

            try {
                // Process each student
                $createdRequests = [];
                
                foreach ($request->student_ids as $studentId) {
                    // Verify student belongs to this franchise
                    $student = Student::where('id', $studentId)
                        ->where('franchise_id', $franchiseId)
                        ->first();
                        
                    if (!$student) {
                        throw new \Exception("Student #{$studentId} not found or doesn't belong to your franchise");
                    }

                    // Deduct from wallet
                    $wallet->balance -= $certificateFee;
                    $wallet->save();

                    // Create wallet transaction
                    $transaction = WalletTransaction::create([
                        'franchise_wallet_id' => $wallet->id,
                        'type' => 'debit',
                        'amount' => $certificateFee,
                        'description' => "Certificate request for {$student->name} - {$course->name}",
                        'status' => 'completed',
                        'balance_after' => $wallet->balance
                    ]);

                    // Create certificate request
                    $certificateRequest = CertificateRequest::create([
                        'franchise_id' => $franchiseId,
                        'student_id' => $studentId,
                        'course_id' => $request->course_id,
                        'certificate_type' => $request->certificate_type ?? 'Course Completion Certificate',
                        'amount' => $certificateFee,
                        'status' => 'pending',
                        'notes' => $request->notes,
                        'wallet_transaction_id' => $transaction->id
                    ]);

                    $createdRequests[] = $certificateRequest;
                }

                DB::commit();

                // Success message
                $count = count($createdRequests);
                $message = $count === 1 
                    ? 'Certificate request submitted successfully!' 
                    : "{$count} certificate requests submitted successfully!";

                return redirect()->route('franchise.certificate-requests.index')
                    ->with('success', $message . ' Total amount deducted: ₹' . number_format($totalAmount, 2));

            } catch (\Exception $e) {
                DB::rollBack();
                
                \Log::error('Certificate Request Store Error: ' . $e->getMessage());
                
                return redirect()->back()
                    ->with('error', 'Failed to create certificate request: ' . $e->getMessage())
                    ->withInput();
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->with('error', 'Please correct the form errors.')
                ->withInput();
                
        } catch (\Exception $e) {
            \Log::error('Certificate Request Validation Error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
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
 * Show the form for editing certificate request
 */
public function edit(CertificateRequest $certificateRequest)
{
    try {
        $franchiseId = Auth::user()->franchise_id;
        
        // Verify this request belongs to the franchise
        if ($certificateRequest->franchise_id !== $franchiseId) {
            return redirect()->route('franchise.certificate-requests.index')
                ->with('error', 'Unauthorized access.');
        }
        
        // Only allow editing if status is pending
        if ($certificateRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending requests can be edited.');
        }
        
        // Load relationships
        $certificateRequest->load(['student', 'course']);
        
        // Get students for this franchise
        $students = Student::where('franchise_id', $franchiseId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        
        // Get courses
        $courses = Course::select('id', 'name', 'description', 'certificate_fee')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        
        // Get wallet balance
        $walletBalance = 0;
        if (Auth::user()->franchise && Auth::user()->franchise->wallet) {
            $walletBalance = Auth::user()->franchise->wallet->balance ?? 0;
        }
        
        return view('franchise.certificate-requests.edit', [
            'request' => $certificateRequest, // Rename to avoid conflict with Request
            'students' => $students,
            'courses' => $courses,
            'walletBalance' => $walletBalance
        ]);
        
    } catch (\Exception $e) {
        return redirect()->route('franchise.certificate-requests.index')
            ->with('error', 'Certificate request not found.');
    }
}

    /**
     * Update the certificate request
     */
    public function update(Request $request, CertificateRequest $certificateRequest)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'student_id' => 'required|exists:students,id',
                'course_id' => 'required|exists:courses,id',
                'certificate_type' => 'nullable|string|max:255',
                'notes' => 'nullable|string'
            ]);

            $franchiseId = Auth::user()->franchise_id;
            
            // Verify this request belongs to the franchise
            if ($certificateRequest->franchise_id !== $franchiseId) {
                return redirect()->route('franchise.certificate-requests.index')
                    ->with('error', 'Unauthorized access.');
            }
            
            // Only allow editing if status is pending
            if ($certificateRequest->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending requests can be edited.');
            }
            
            // Get the new course details
            $newCourse = Course::findOrFail($request->course_id);
            $newCertificateFee = $newCourse->certificate_fee ?? 100.00;
            
            // Get old amount
            $oldAmount = $certificateRequest->amount;
            
            // Calculate difference
            $amountDifference = $newCertificateFee - $oldAmount;
            
            DB::beginTransaction();
            
            try {
                // If amount changed, handle wallet adjustment
                if ($amountDifference != 0) {
                    $wallet = FranchiseWallet::where('franchise_id', $franchiseId)->firstOrFail();
                    
                    if ($amountDifference > 0) {
                        // New course is more expensive - need more money
                        if ($wallet->balance < $amountDifference) {
                            throw new \Exception('Insufficient wallet balance for this change. Additional required: ₹' . number_format($amountDifference, 2));
                        }
                        
                        // Deduct additional amount
                        $wallet->balance -= $amountDifference;
                        $wallet->save();
                        
                        // Create debit transaction
                        WalletTransaction::create([
                            'franchise_wallet_id' => $wallet->id,
                            'type' => 'debit',
                            'amount' => $amountDifference,
                            'description' => "Additional charge for certificate request update (Request #{$certificateRequest->id})",
                            'status' => 'completed',
                            'balance_after' => $wallet->balance
                        ]);
                        
                    } else {
                        // New course is cheaper - refund difference
                        $refundAmount = abs($amountDifference);
                        $wallet->balance += $refundAmount;
                        $wallet->save();
                        
                        // Create credit transaction
                        WalletTransaction::create([
                            'franchise_wallet_id' => $wallet->id,
                            'type' => 'credit',
                            'amount' => $refundAmount,
                            'description' => "Refund for certificate request update (Request #{$certificateRequest->id})",
                            'status' => 'completed',
                            'balance_after' => $wallet->balance
                        ]);
                    }
                }
                
                // Update the certificate request
                $certificateRequest->update([
                    'student_id' => $request->student_id,
                    'course_id' => $request->course_id,
                    'certificate_type' => $request->certificate_type ?? 'Course Completion Certificate',
                    'amount' => $newCertificateFee,
                    'notes' => $request->notes
                ]);
                
                DB::commit();
                
                return redirect()->route('franchise.certificate-requests.index')
                    ->with('success', 'Certificate request updated successfully!');
                    
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
            \Log::error('Certificate Request Update Error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to update certificate request: ' . $e->getMessage())
                ->withInput();
        }
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
