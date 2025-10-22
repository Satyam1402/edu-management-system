<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Yajra\DataTables\Facades\DataTables;

class PaymentController extends Controller
{
    private $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(
            config('services.razorpay.key_id'),
            config('services.razorpay.key_secret')
        );
    }

    public function index()
    {
        if (request()->ajax()) {
            return $this->getDatatablesData();
        }
        
        return view('admin.payments.index');
    }

    public function getDatatablesData()
    {
        $payments = Payment::with(['student', 'course'])->select([
            'id', 'order_id', 'student_id', 'course_id', 'amount',
            'currency', 'status', 'gateway', 'paid_at', 'created_at'
        ]);

        return DataTables::of($payments)
            ->addIndexColumn()
            ->addColumn('student_info', function ($payment) {
                if ($payment->student) {
                    return '<div class="student-info">' .
                        '<div class="font-weight-bold text-dark">' . $payment->student->name . '</div>' .
                        '<small class="text-muted">' . $payment->student->student_id . '</small>' .
                        '</div>';
                }
                return '<span class="text-muted">Student Not Found</span>';
            })
            ->addColumn('payment_details', function ($payment) {
                $html = '<div class="payment-details">';
                $html .= '<div class="font-weight-bold text-primary">' . $payment->order_id . '</div>';
                $html .= '<div class="text-success font-weight-bold">' . $payment->formatted_amount . '</div>';
                if ($payment->course) {
                    $html .= '<small class="text-muted">' . $payment->course->name . '</small>';
                }
                $html .= '</div>';
                return $html;
            })
            ->addColumn('payment_status', function ($payment) {
                $badgeClass = $payment->status_badge;
                $statusText = ucfirst($payment->status);
                
                return '<span class="badge badge-' . $badgeClass . ' px-2 py-1">' . $statusText . '</span>';
            })
            ->addColumn('gateway_info', function ($payment) {
                $html = '<div class="text-center">';
                if ($payment->gateway) {
                    $html .= '<div class="font-weight-bold text-info">' . ucfirst($payment->gateway) . '</div>';
                    if ($payment->gateway_payment_id) {
                        $html .= '<small class="text-muted">' . substr($payment->gateway_payment_id, 0, 15) . '...</small>';
                    }
                } else {
                    $html .= '<span class="text-muted">Manual</span>';
                }
                $html .= '</div>';
                return $html;
            })
            ->addColumn('date_info', function ($payment) {
                $html = '<div class="text-center">';
                
                if ($payment->paid_at) {
                    $html .= '<div class="small text-success font-weight-bold">Paid</div>';
                    $html .= '<div class="font-weight-bold">' . $payment->paid_at->format('M d, Y') . '</div>';
                    $html .= '<small class="text-muted">' . $payment->paid_at->format('h:i A') . '</small>';
                } else {
                    $html .= '<div class="font-weight-bold">' . $payment->created_at->format('M d, Y') . '</div>';
                    $html .= '<small class="text-muted">Created: ' . $payment->created_at->diffForHumans() . '</small>';
                }
                
                $html .= '</div>';
                return $html;
            })
            ->addColumn('actions', function ($payment) {
                $buttons = '<div class="btn-group" role="group">';
                
                // View button
                $buttons .= '<a href="' . route('admin.payments.show', $payment) . '" class="btn btn-sm btn-info mr-1" title="View Details">';
                $buttons .= '<i class="fas fa-eye"></i></a>';
                
                // Retry Payment button (only for failed payments)
                if ($payment->status === 'failed') {
                    $buttons .= '<button class="btn btn-sm btn-warning mr-1 retry-payment" data-id="' . $payment->id . '" title="Retry Payment">';
                    $buttons .= '<i class="fas fa-redo"></i></button>';
                }
                
                // Print Receipt button (only for completed payments)
                if ($payment->status === 'completed') {
                    $buttons .= '<a href="' . route('admin.payments.receipt', $payment) . '" class="btn btn-sm btn-secondary mr-1" title="Print Receipt" target="_blank">';
                    $buttons .= '<i class="fas fa-receipt"></i></a>';
                }
                
                // Delete button (only for failed/pending payments)
                if (in_array($payment->status, ['failed', 'pending'])) {
                    $buttons .= '<button class="btn btn-sm btn-danger delete-payment" data-id="' . $payment->id . '" title="Delete">';
                    $buttons .= '<i class="fas fa-trash"></i></button>';
                }
                
                $buttons .= '</div>';
                return $buttons;
            })
            ->rawColumns(['student_info', 'payment_details', 'payment_status', 'gateway_info', 'date_info', 'actions'])
            ->make(true);
    }

    public function create()
    {
        $students = Student::where('status', 'active')->get();
        $courses = Course::where('status', 'active')->get();
        
        return view('admin.payments.create', compact('students', 'courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'amount' => 'required|numeric|min:1'
        ]);

        try {
            // Create payment record
            $payment = Payment::create([
                'student_id' => $validated['student_id'],
                'course_id' => $validated['course_id'],
                'amount' => $validated['amount'],
                'currency' => 'INR',
                'status' => 'pending'
            ]);

            // Create Razorpay order
            $razorpayOrder = $this->razorpay->order->create([
                'amount' => $validated['amount'] * 100, // Amount in paise
                'currency' => 'INR',
                'receipt' => $payment->order_id,
                'payment_capture' => 1
            ]);

            // Update payment with gateway info
            $payment->update([
                'gateway' => 'razorpay',
                'gateway_order_id' => $razorpayOrder['id']
            ]);

            return redirect()->route('admin.payments.checkout', $payment);

        } catch (\Exception $e) {
            return back()->with('error', 'Error creating payment: ' . $e->getMessage());
        }
    }

    public function checkout(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return redirect()->route('admin.payments.index')
                ->with('error', 'Payment is not in pending status.');
        }

        $payment->load(['student', 'course']);
        return view('admin.payments.checkout', compact('payment'));
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'order_id' => 'required|string'
        ]);

        try {
            // Find payment
            $payment = Payment::where('order_id', $validated['order_id'])->firstOrFail();

            // Verify signature
            $attributes = [
                'razorpay_order_id' => $validated['razorpay_order_id'],
                'razorpay_payment_id' => $validated['razorpay_payment_id'],
                'razorpay_signature' => $validated['razorpay_signature']
            ];

            $this->razorpay->utility->verifyPaymentSignature($attributes);

            // Fetch payment details from Razorpay
            $razorpayPayment = $this->razorpay->payment->fetch($validated['razorpay_payment_id']);

            // Mark payment as completed
            $payment->markAsCompleted($validated['razorpay_payment_id'], $razorpayPayment->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Payment successful!',
                'redirect_url' => route('admin.payments.success', $payment)
            ]);

        } catch (\Exception $e) {
            // Mark payment as failed
            if (isset($payment)) {
                $payment->markAsFailed(['error' => $e->getMessage()]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ], 400);
        }
    }

    public function success(Payment $payment)
    {
        if ($payment->status !== 'completed') {
            return redirect()->route('admin.payments.index')
                ->with('error', 'Invalid payment status.');
        }

        $payment->load(['student', 'course']);
        return view('admin.payments.success', compact('payment'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['student', 'course']);
        return view('admin.payments.show', compact('payment'));
    }

    public function destroy(Payment $payment)
    {
        if ($payment->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Completed payments cannot be deleted.'
            ], 400);
        }

        try {
            $payment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Payment deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting payment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function receipt(Payment $payment)
    {
        if ($payment->status !== 'completed') {
            return redirect()->route('admin.payments.index')
                ->with('error', 'Receipt can only be generated for completed payments.');
        }

        $payment->load(['student', 'course']);
        return view('admin.payments.receipt', compact('payment'));
    }
}
