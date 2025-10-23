<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Course;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PaymentController extends Controller
{
    private $paymentService;

    public function __construct(PaymentGatewayService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of payments (KEEP YOUR DATATABLE LOGIC)
     */
    public function index(Request $request)
    {
        // Handle statistics request
        if ($request->has('get_stats')) {
            $stats = [
                'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
                'pending_payments' => Payment::where('status', 'pending')->count(),
                'this_month_revenue' => Payment::where('status', 'completed')
                    ->whereMonth('paid_at', now()->month)
                    ->whereYear('paid_at', now()->year)
                    ->sum('amount'),
                'success_rate' => $this->calculateSuccessRate(),
                'completed_count' => Payment::where('status', 'completed')->count(),
                'pending_count' => Payment::where('status', 'pending')->count(),
                'failed_count' => Payment::where('status', 'failed')->count(),
                'total_count' => Payment::count()
            ];
            
            return response()->json(['stats' => $stats]);
        }

        if ($request->ajax()) {
            $query = Payment::with(['student', 'course']);
            
            // Apply status filter if provided
            if ($request->has('status_filter') && !empty($request->status_filter)) {
                $query->where('status', $request->status_filter);
            }
            
            // Apply date filter if provided
            if ($request->has('date_filter') && !empty($request->date_filter)) {
                switch($request->date_filter) {
                    case 'today':
                        $query->whereDate('created_at', today());
                        break;
                    case 'week':
                        $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'month':
                        $query->whereMonth('created_at', now()->month);
                        break;
                }
            }
            
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('order_info', function ($payment) {
                    return '<div>
                                <h6 class="mb-1 font-weight-bold text-primary">' . $payment->order_id . '</h6>
                                <small class="text-muted">' . $payment->created_at->format('M d, Y g:i A') . '</small>
                            </div>';
                })
                ->addColumn('student_info', function ($payment) {
                    return $payment->student ? 
                        '<div>
                            <span class="font-weight-bold">' . e($payment->student->name) . '</span><br>
                            <small class="text-muted">' . e($payment->student->email ?? 'No email') . '</small>
                        </div>' : 
                        '<span class="text-muted">No Student</span>';
                })
                ->addColumn('payment_details', function ($payment) {
                    $course = $payment->course ? '<small class="text-muted d-block">For: ' . e($payment->course->name) . '</small>' : '';
                    return '<div>
                                <h6 class="mb-1 text-success">₹' . number_format($payment->amount, 2) . '</h6>
                                <small class="text-muted">' . strtoupper($payment->currency) . '</small>
                                ' . $course . '
                            </div>';
                })
                ->addColumn('gateway_info', function ($payment) {
                    if (!$payment->gateway || $payment->gateway === 'manual') {
                        return '<span class="badge badge-secondary">Manual</span>';
                    }
                    
                    $gatewayColors = [
                        'razorpay' => 'primary',
                        'upi' => 'success',
                        'paytm' => 'info'
                    ];
                    
                    $color = $gatewayColors[$payment->gateway] ?? 'secondary';
                    
                    return '<div>
                                <span class="badge badge-' . $color . '">' . ucfirst($payment->gateway) . '</span>
                                ' . ($payment->gateway_payment_id ? '<br><small class="text-muted">ID: ' . substr($payment->gateway_payment_id, 0, 15) . '...</small>' : '') . '
                            </div>';
                })
                ->addColumn('status', function ($payment) {
                    $colors = [
                        'pending' => 'warning',
                        'completed' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'info'
                    ];
                    $color = $colors[$payment->status] ?? 'secondary';
                    
                    $paidInfo = '';
                    if ($payment->status === 'completed' && $payment->paid_at) {
                        $paidInfo = '<br><small class="text-muted">Paid: ' . $payment->paid_at->format('M d, Y') . '</small>';
                    }
                    
                    return '<span class="badge badge-' . $color . '">' . ucfirst($payment->status) . '</span>' . $paidInfo;
                })
                ->addColumn('actions', function ($payment) {
                    $actions = '<div class="btn-group" role="group">';
                    
                    // View button
                    $actions .= '<a href="' . route('admin.payments.show', $payment) . '" class="btn btn-outline-info btn-sm" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>';
                    
                    // Status action buttons
                    if ($payment->status === 'pending') {
                        $actions .= '<button class="btn btn-outline-success btn-sm" onclick="markAsCompleted(' . $payment->id . ')" title="Mark as Paid">
                                        <i class="fas fa-check"></i>
                                    </button>';
                        $actions .= '<button class="btn btn-outline-danger btn-sm" onclick="markAsFailed(' . $payment->id . ')" title="Mark as Failed">
                                        <i class="fas fa-times"></i>
                                    </button>';
                    }
                    
                    if ($payment->status === 'completed') {
                        $actions .= '<button class="btn btn-outline-warning btn-sm" onclick="processRefund(' . $payment->id . ')" title="Process Refund">
                                        <i class="fas fa-undo"></i>
                                    </button>';
                    }
                    
                    // Delete button (only for pending payments)
                    if ($payment->status === 'pending') {
                        $actions .= '<button class="btn btn-outline-danger btn-sm" onclick="deletePayment(' . $payment->id . ')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>';
                    }
                    
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['order_info', 'student_info', 'payment_details', 'gateway_info', 'status', 'actions'])
                ->make(true);
        }

        return view('admin.payments.index');
    }

    /**
     * Show the form for creating a new payment
     */
    public function create()
    {
        $students = Student::orderBy('name')->get();
        $courses = Course::orderBy('name')->get();
        
        return view('admin.payments.create', compact('students', 'courses'));
    }

    /**
     * Store a newly created payment (WITH GATEWAY SUPPORT)
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:1',
            'gateway' => 'required|in:manual,razorpay,upi',
            'course_id' => 'nullable|exists:courses,id'
        ]);

        $payment = Payment::create([
            'student_id' => $request->student_id,
            'course_id' => $request->course_id,
            'amount' => $request->amount,
            'currency' => 'INR',
            'gateway' => $request->gateway,
            'status' => 'pending'
        ]);

        // Handle different gateways
        if ($request->gateway === 'razorpay') {
            return $this->handleRazorpayPayment($payment);
        } elseif ($request->gateway === 'upi') {
            return $this->handleUpiPayment($payment);
        }

        // Manual payment
        return redirect()->route('admin.payments.show', $payment)
                        ->with('success', 'Manual payment created successfully!');
    }

    /**
     * Handle Razorpay Payment
     */
    private function handleRazorpayPayment($payment)
    {
        $order = $this->paymentService->createRazorpayOrder($payment->amount);
        
        if (!$order['success']) {
            return back()->with('error', 'Failed to create Razorpay order: ' . $order['message']);
        }
        
        $payment->update(['gateway_order_id' => $order['order_id']]);
        
        return view('admin.payments.razorpay', compact('payment', 'order'));
    }

    /**
     * Handle UPI Payment
     */
    public function handleUpiPayment(Payment $payment)
    {
        try {
            // Generate UPI QR Code
            $qrData = $this->paymentService->generateUpiQrCode(
                $payment->amount,
                $payment->student->name,
                "Payment for " . ($payment->course->name ?? 'Course')
            );

            // Pass $qrData to the view (This was missing!)
            return view('admin.payments.upi', compact('payment', 'qrData'));
            
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.payments.show', $payment)
                ->with('error', 'Failed to generate UPI QR code: ' . $e->getMessage());
        }
    }

    /**
     * Verify Razorpay Payment
     */
    public function verifyRazorpay(Request $request)
    {
        $payment = Payment::where('gateway_order_id', $request->razorpay_order_id)->first();
        
        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Payment not found']);
        }
        
        $verified = $this->paymentService->verifyRazorpayPayment(
            $request->razorpay_payment_id,
            $request->razorpay_order_id,
            $request->razorpay_signature
        );
        
        if ($verified) {
            $payment->update([
                'status' => 'completed',
                'gateway_payment_id' => $request->razorpay_payment_id,
                'gateway_response' => $request->all(),
                'paid_at' => now()
            ]);
            
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false, 'message' => 'Payment verification failed']);
    }

    /**
     * Show payment details
     */
    public function show(Payment $payment)
    {
        $payment->load(['student', 'course']);
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Mark payment as completed (KEEP YOUR EXISTING LOGIC)
     */
    public function markAsCompleted(Request $request, Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending payments can be marked as completed.'
            ], 400);
        }

        $payment->update([
            'status' => 'completed',
            'gateway_payment_id' => $request->gateway_payment_id ?: 'MANUAL_' . time(),
            'gateway_response' => ['manual_completion' => true, 'completed_by' => auth()->id()],
            'paid_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment marked as completed successfully!'
        ]);
    }

    /**
     * Mark payment as failed (KEEP YOUR EXISTING LOGIC)
     */
    public function markAsFailed(Request $request, Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending payments can be marked as failed.'
            ], 400);
        }

        $payment->update([
            'status' => 'failed',
            'gateway_response' => [
                'manual_failure' => true,
                'failed_by' => auth()->id(),
                'reason' => $request->reason ?: 'Manually marked as failed'
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment marked as failed successfully!'
        ]);
    }

    /**
     * Process refund (KEEP YOUR EXISTING LOGIC)
     */
    public function processRefund(Request $request, Payment $payment)
    {
        if ($payment->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Only completed payments can be refunded.'
            ], 400);
        }

        $payment->update([
            'status' => 'refunded',
            'gateway_response' => array_merge($payment->gateway_response ?: [], [
                'refund_processed' => true,
                'refunded_by' => auth()->id(),
                'refunded_at' => now()->toISOString(),
                'refund_reason' => $request->reason ?: 'Manual refund'
            ])
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment refund processed successfully!'
        ]);
    }

    /**
     * Delete payment (KEEP YOUR EXISTING LOGIC)
     */
    public function destroy(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending payments can be deleted.'
            ], 400);
        }

        $payment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment deleted successfully!'
        ]);
    }

    /**
     * Export payments (KEEP YOUR EXISTING LOGIC)
     */
    public function export(Request $request)
    {
        $payments = Payment::with(['student', 'course'])
            ->when($request->status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->get();

        $csvData = [];
        $csvData[] = ['Order ID', 'Student Name', 'Course', 'Amount', 'Currency', 'Status', 'Gateway', 'Payment Date', 'Created Date'];

        foreach ($payments as $payment) {
            $csvData[] = [
                $payment->order_id,
                $payment->student->name ?? 'N/A',
                $payment->course->name ?? 'N/A',
                $payment->amount,
                $payment->currency,
                ucfirst($payment->status),
                $payment->gateway ?: 'Manual',
                $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i:s') : 'N/A',
                $payment->created_at->format('Y-m-d H:i:s')
            ];
        }

        $filename = 'payments-export-' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Calculate success rate (KEEP YOUR EXISTING LOGIC)
     */
    private function calculateSuccessRate()
    {
        $total = Payment::count();
        if ($total == 0) return 0;
        
        $completed = Payment::where('status', 'completed')->count();
        return round(($completed / $total) * 100, 1);
    }

    public function confirmUpi(Request $request, Payment $payment)
    {
        $request->validate([
            'transaction_id' => 'required|string|min:10|max:20'
        ]);
        
        // Update payment status
        $payment->update([
            'status' => 'completed',
            'gateway_payment_id' => $request->transaction_id,
            'gateway_response' => [
                'upi_transaction_id' => $request->transaction_id,
                'payment_method' => 'upi_qr',
                'confirmed_at' => now(),
                'confirmed_by' => auth()->id() ?? 1
            ],
            'paid_at' => now()
        ]);
        
        return redirect()
            ->route('admin.payments.show', $payment)
            ->with('success', 'UPI payment confirmed successfully!');
    }
}
