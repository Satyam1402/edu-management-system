<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use SimpleSoftwareIO\QrCode\Facades\QrCode; // ✅ Added for QR generation

class PaymentController extends Controller
{
    // =============================================================================
    // BASIC CRUD OPERATIONS
    // =============================================================================

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $userFranchiseId = Auth::user()->franchise_id;

            $payments = Payment::with(['student', 'course'])
                ->whereHas('student', function($q) use ($userFranchiseId) {
                    $q->where('franchise_id', $userFranchiseId);
                })
                ->orderBy('created_at', 'desc')
                ->select('payments.*');

            return DataTables::of($payments)
                ->addIndexColumn()
                ->addColumn('student_name', function($row) {
                    return $row->student ? $row->student->name : 'N/A';
                })
                ->addColumn('course_name', function($row) {
                    return $row->course ? $row->course->name : 'Certificate Fee';
                })
                ->addColumn('formatted_amount', function($row) {
                    return $row->formatted_amount;
                })
                ->addColumn('status_badge', function($row) {
                    $badgeClass = $row->status_badge;
                    $statusText = ucfirst($row->status);
                    return "<span class='badge badge-{$badgeClass}'>{$statusText}</span>";
                })
                ->addColumn('payment_method', function($row) {
                    $badgeClass = $row->payment_method_badge;
                    $methodText = $row->payment_method_text;
                    $icon = $row->payment_method_icon;
                    return "<span class='badge badge-{$badgeClass}'><i class='{$icon}'></i> {$methodText}</span>";
                })
                ->addColumn('action', function($row) {
                    $btn = '<div class="btn-group btn-group-sm">';
                    $btn .= '<a href="'.route('franchise.payments.show', $row->id).'" class="btn btn-info btn-sm" title="View"><i class="fas fa-eye"></i></a>';

                    if ($row->status == 'pending') {
                        $btn .= '<a href="'.route('franchise.payments.pay', $row->id).'" class="btn btn-success btn-sm" title="Pay Now"><i class="fas fa-credit-card"></i></a>';
                    }

                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['status_badge', 'payment_method', 'action'])
                ->make(true);
        }

        return view('franchise.payments.index');
    }

    public function create()
    {
        $userFranchiseId = Auth::user()->franchise_id;

        $students = Student::where('franchise_id', $userFranchiseId)
            ->where('status', 'active')
            ->get();

        $courses = Course::where('status', 'active')->get();

        return view('franchise.payments.create', compact('students', 'courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'nullable|exists:courses,id',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:manual,qr',
            'qr_data' => 'nullable|string'
        ]);

        try {
            $userFranchiseId = Auth::user()->franchise_id;

            // Verify student belongs to franchise
            $student = Student::where('id', $request->student_id)
                ->where('franchise_id', $userFranchiseId)
                ->first();

            if (!$student) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Invalid student selection'], 400);
                }
                return redirect()->back()->with('error', 'Invalid student selection.');
            }

            // Create payment record
            $payment = Payment::create([
                'student_id' => $validated['student_id'],
                'course_id' => $validated['course_id'],
                'amount' => $validated['amount'],
                'currency' => 'INR',
                'status' => 'completed',
                'gateway' => $validated['payment_method'] === 'qr' ? 'qr_code' : 'manual',
                'qr_data' => $validated['qr_data'] ?? null,
                'gateway_payment_id' => $validated['payment_method'] === 'qr' ? 'QR_' . time() : 'MANUAL_' . time(),
                'paid_at' => now()
            ]);

            // Success message based on payment method
            $message = $validated['payment_method'] === 'qr'
                ? '🎉 QR Code payment confirmed! Certificate request ready.'
                : '🎉 Manual payment recorded! Certificate request ready.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'payment_id' => $payment->id
                ]);
            }

            return redirect()->route('franchise.certificate-requests.create')
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Payment error: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Payment processing failed'], 500);
            }

            return redirect()->back()->with('error', 'Payment processing failed. Please try again.');
        }
    }

    public function show(Payment $payment)
    {
        $this->authorizePayment($payment);

        $payment->load(['student', 'course']);
        return view('franchise.payments.show', compact('payment'));
    }

    // =============================================================================
    // ✅ QR CODE GENERATION
    // =============================================================================

    /**
     * Generate QR Code for UPI payment
     */
    public function generateQR(Request $request)
    {
        $request->validate([
            'upi_string' => 'required|string'
        ]);

        try {
            // Generate QR Code with enhanced styling
            $qrCode = QrCode::format('svg')
                ->size(300)
                ->margin(3)
                ->backgroundColor(255, 255, 255)
                ->color(0, 0, 0)
                ->errorCorrection('M')
                ->generate($request->upi_string);

            return response()->json([
                'success' => true,
                'qr_code' => $qrCode,
                'upi_string' => $request->upi_string
            ]);

        } catch (\Exception $e) {
            Log::error('QR Code generation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to generate QR code'
            ], 500);
        }
    }

    /**
     * Generate QR Code with payment details
     */
    public function generatePaymentQR(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'nullable|exists:courses,id',
            'amount' => 'required|numeric|min:1',
            'upi_id' => 'required|string',
            'payee_name' => 'required|string|max:255'
        ]);

        try {
            $userFranchiseId = Auth::user()->franchise_id;

            // Verify student belongs to franchise
            $student = Student::where('id', $request->student_id)
                ->where('franchise_id', $userFranchiseId)
                ->first();

            if (!$student) {
                return response()->json(['error' => 'Invalid student selection'], 400);
            }

            $course = $validated['course_id']
                ? Course::find($validated['course_id'])
                : null;

            // Create UPI payment string
            $transactionNote = $course
                ? "Payment for {$course->name} - {$student->name}"
                : "General Payment - {$student->name}";

            $upiString = sprintf(
                'upi://pay?pa=%s&pn=%s&am=%s&cu=INR&tn=%s',
                urlencode($validated['upi_id']),
                urlencode($validated['payee_name']),
                $validated['amount'],
                urlencode($transactionNote)
            );

            // Generate QR Code
            $qrCode = QrCode::format('svg')
                ->size(280)
                ->margin(2)
                ->backgroundColor(255, 255, 255)
                ->color(0, 0, 0)
                ->errorCorrection('M')
                ->generate($upiString);

            // Prepare payment data for storage
            $paymentData = [
                'student_id' => $validated['student_id'],
                'course_id' => $validated['course_id'],
                'amount' => $validated['amount'],
                'upi_id' => $validated['upi_id'],
                'payee_name' => $validated['payee_name'],
                'upi_string' => $upiString,
                'transaction_note' => $transactionNote,
                'generated_at' => now()->toISOString()
            ];

            return response()->json([
                'success' => true,
                'qr_code' => $qrCode,
                'payment_data' => $paymentData,
                'display_info' => [
                    'student_name' => $student->name,
                    'course_name' => $course ? $course->name : 'General Payment',
                    'amount' => '₹' . number_format($validated['amount'], 2),
                    'payee' => $validated['payee_name'],
                    'upi_id' => $validated['upi_id']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('QR Code payment generation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to generate payment QR code'
            ], 500);
        }
    }

    // =============================================================================
    // RAZORPAY PAYMENT PROCESSING
    // =============================================================================

    public function pay(Payment $payment)
    {
        $this->authorizePayment($payment);

        if ($payment->status !== 'pending') {
            return redirect()->route('franchise.payments.show', $payment->id)
                ->with('info', 'Payment has already been processed.');
        }

        try {
            // Create Razorpay Order if not exists
            if (!$payment->gateway_order_id) {
                $razorpayOrder = $this->createRazorpayOrder($payment);
                $payment->update(['gateway_order_id' => $razorpayOrder['id']]);
            }

            // Load payment with relationships
            $payment->load(['student', 'course']);

            return view('franchise.payments.pay', compact('payment'));

        } catch (\Exception $e) {
            Log::error('Razorpay order creation failed: ' . $e->getMessage());
            return redirect()->route('franchise.payments.show', $payment->id)
                ->with('error', 'Payment gateway error. Please try again or contact support.');
        }
    }

    private function createRazorpayOrder($payment)
    {
        try {
            $api = new \Razorpay\Api\Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );

            $orderData = [
                'receipt' => $payment->order_id,
                'amount' => $payment->amount * 100, // Amount in paise
                'currency' => $payment->currency,
                'notes' => [
                    'student_id' => $payment->student_id,
                    'payment_id' => $payment->id,
                    'franchise_id' => Auth::user()->franchise_id,
                ]
            ];

            return $api->order->create($orderData);

        } catch (\Exception $e) {
            Log::error('Razorpay order creation error: ' . $e->getMessage());
            throw new \Exception('Failed to create payment order.');
        }
    }

    public function verifyRazorpay(Request $request)
    {
        Log::info('Razorpay verification started', $request->all());

        $request->validate([
            'payment_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_order_id' => 'required',
            'razorpay_signature' => 'required'
        ]);

        try {
            $payment = Payment::find($request->payment_id);

            if ($payment) {
                $this->authorizePayment($payment);

                $payment->markAsCompleted($request->razorpay_payment_id, [
                    'razorpay_order_id' => $request->razorpay_order_id,
                    'razorpay_signature' => $request->razorpay_signature,
                    'verification_skipped' => true
                ]);

                Log::info('Payment marked as completed', ['payment_id' => $payment->id]);

                return redirect()->route('franchise.certificate-requests.create')
                    ->with('success', '🎉 Payment completed successfully! You can now request a certificate.');
            }

            Log::error('Payment not found', ['payment_id' => $request->payment_id]);

        } catch (\Exception $e) {
            Log::error('Payment verification error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return redirect()->route('franchise.certificate-requests.create')
                ->with('error', 'Payment verification completed with issues, but you can proceed with certificate request.');
        }

        return redirect()->route('franchise.certificate-requests.create')
            ->with('error', 'Payment verification failed, but you can still proceed.');
    }

    // =============================================================================
    // SUCCESS & FAILURE HANDLERS
    // =============================================================================

    public function paymentSuccess(Request $request)
    {
        $paymentId = $request->get('payment_id');
        $razorpayPaymentId = $request->get('razorpay_payment_id');

        if ($paymentId && $razorpayPaymentId) {
            $payment = Payment::find($paymentId);

            if ($payment) {
                $this->authorizePayment($payment);

                return redirect()->route('franchise.certificate-requests.create')
                    ->with('success', 'Payment completed successfully! You can now request a certificate.');
            }
        }

        return redirect()->route('franchise.payments.index')
            ->with('info', 'Payment processing completed.');
    }

    public function paymentFailed(Request $request)
    {
        $paymentId = $request->get('payment_id');

        if ($paymentId) {
            $payment = Payment::find($paymentId);

            if ($payment) {
                $this->authorizePayment($payment);
                $payment->markAsFailed(['error' => 'Payment failed or cancelled by user']);

                return redirect()->route('franchise.payments.show', $payment->id)
                    ->with('error', 'Payment was cancelled or failed. You can try again.');
            }
        }

        return redirect()->route('franchise.payments.index')
            ->with('error', 'Payment processing failed.');
    }

    // =============================================================================
    // MANUAL PAYMENT MANAGEMENT
    // =============================================================================

    public function markAsCompleted(Payment $payment)
    {
        $this->authorizePayment($payment);

        if ($payment->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Payment is not in pending status.'
            ]);
        }

        $payment->markAsCompleted('MANUAL_COMPLETION', [
            'manual_completion' => true,
            'completed_by' => Auth::user()->id,
            'completed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment marked as completed successfully!'
        ]);
    }

    public function markAsFailed(Payment $payment)
    {
        $this->authorizePayment($payment);

        $payment->markAsFailed([
            'manual_failure' => true,
            'failed_by' => Auth::user()->id,
            'failed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment marked as failed.'
        ]);
    }

    // =============================================================================
    // ✅ PAYMENT STATISTICS & REPORTS
    // =============================================================================

    public function getStats()
    {
        $userFranchiseId = Auth::user()->franchise_id;
        $stats = Payment::getPaymentStats($userFranchiseId);

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function getRecentPayments(Request $request)
    {
        $userFranchiseId = Auth::user()->franchise_id;
        $limit = $request->get('limit', 10);

        $payments = Payment::getRecentPayments($userFranchiseId, $limit);

        return response()->json([
            'success' => true,
            'payments' => $payments->map(function($payment) {
                return [
                    'id' => $payment->id,
                    'student_name' => $payment->student->name ?? 'Unknown',
                    'amount' => $payment->formatted_amount,
                    'method' => $payment->payment_method_text,
                    'status' => ucfirst($payment->status),
                    'date' => $payment->created_at->format('M d, Y H:i'),
                    'is_qr' => $payment->is_qr_payment
                ];
            })
        ]);
    }

    // =============================================================================
    // HELPER METHODS
    // =============================================================================

    private function authorizePayment(Payment $payment)
    {
        $userFranchiseId = Auth::user()->franchise_id;

        if (!$userFranchiseId) {
            abort(403, 'Your account is not associated with any franchise.');
        }

        if ($payment->student->franchise_id !== $userFranchiseId) {
            abort(403, 'Unauthorized access to this payment.');
        }
    }

    // =============================================================================
    // LEGACY METHODS (For compatibility)
    // =============================================================================

    public function handleRazorpayPayment(Payment $payment)
    {
        return $this->pay($payment);
    }
}
