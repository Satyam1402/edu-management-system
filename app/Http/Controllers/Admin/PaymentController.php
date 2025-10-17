<?php
// app/Http/Controllers/Admin/PaymentController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['student', 'course'])->latest()->get();
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $pendingPayments = Payment::where('status', 'pending')->sum('amount');

        return view('admin.payments.index', compact('payments', 'totalRevenue', 'pendingPayments'));
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
            'amount' => 'required|numeric|min:1',
            'payment_type' => 'required|in:registration,course_fee,exam_fee,certificate_fee',
            'payment_method' => 'required|in:cash,card,upi,bank_transfer',
            'transaction_id' => 'nullable|string|unique:payments,transaction_id',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,completed,failed,refunded',
        ]);

        // Generate payment ID
        $paymentId = 'PAY' . date('Ymd') . str_pad(Payment::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['payment_id'] = $paymentId;
        $validated['payment_date'] = now();

        Payment::create($validated);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment record created successfully!');
    }

    public function show(Payment $payment)
    {
        $payment->load(['student', 'course']);
        return view('admin.payments.show', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,failed,refunded',
            'notes' => 'nullable|string',
        ]);

        $payment->update($validated);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment status updated successfully!');
    }
}
