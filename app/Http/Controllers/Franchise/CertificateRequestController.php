<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\CertificateRequest;
use App\Models\Student;
use App\Models\Course;
use App\Models\Payment;
use App\Http\Controllers\Franchise\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CertificateRequestController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $userFranchiseId = Auth::user()->franchise_id;

            $requests = CertificateRequest::with(['student', 'course', 'payment'])
                ->where('franchise_id', $userFranchiseId)
                ->select('certificate_requests.*');

            return DataTables::of($requests)
                ->addIndexColumn()
                ->addColumn('student_name', fn($row) => $row->student ? $row->student->name : 'N/A')
                ->addColumn('course_name', fn($row) => $row->course ? $row->course->name : 'General Certificate')
                ->addColumn('status_badge', function($row) {
                    $badgeClass = match($row->status) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'completed' => 'primary',
                        default => 'secondary'
                    };
                    return "<span class='badge badge-{$badgeClass}'>" . ucfirst($row->status) . "</span>";
                })
                ->addColumn('payment_status', fn($row) => $row->payment ? "<span class='badge badge-success'>Paid</span>" : "<span class='badge badge-danger'>Unpaid</span>")
                ->addColumn('action', function($row) {
                    return '<div class="btn-group btn-group-sm">' .
                        '<a href="'.route('franchise.certificate-requests.show', $row->id).'" class="btn btn-info btn-sm" title="View"><i class="fas fa-eye"></i></a>' .
                        '</div>';
                })
                ->rawColumns(['status_badge', 'payment_status', 'action'])
                ->make(true);
        }

        return view('franchise.certificate-requests.index');
    }

    public function create()
    {
        $userFranchiseId = Auth::user()->franchise_id;

        if (!$userFranchiseId) {
            return redirect()->back()->with('error', 'Your account is not associated with any franchise.');
        }

        $students = Student::where('franchise_id', $userFranchiseId)
            ->where('status', 'active')
            ->get();

        $payments = Payment::whereHas('student', function($q) use ($userFranchiseId) {
                $q->where('franchise_id', $userFranchiseId);
            })
            ->where('status', 'completed')
            ->with(['student', 'course'])
            ->get();

        return view('franchise.certificate-requests.create', compact('students', 'payments'));
    }

    // Updated store method for single and batch requests with wallet check
    public function store(Request $request)
    {
        // Example to handle both single and batch certificate requests
        $request->validate([
            'certificate_requests' => 'required|array',
            'certificate_requests.*.student_id' => 'required|exists:students,id',
            'certificate_requests.*.payment_id' => 'required|exists:payments,id',
            'certificate_requests.*.certificate_type' => 'nullable|string',
            'certificate_requests.*.special_note' => 'nullable|string|max:500'
        ]);

        $userFranchiseId = Auth::user()->franchise_id;

        $walletController = new WalletController();

        // Calculate total required amount for all payment_ids
        $paymentIds = collect($request->input('certificate_requests'))->pluck('payment_id')->toArray();

        $payments = Payment::whereIn('id', $paymentIds)
            ->where('status', 'completed')
            ->whereHas('student', fn($q) => $q->where('franchise_id', $userFranchiseId))
            ->get();

        if ($payments->count() !== count($paymentIds)) {
            return redirect()->back()->with('error', 'One or more payments are invalid or not completed.');
        }

        $totalAmount = $payments->sum('amount');

        // Check wallet balance
        $wallet = $walletController->getWalletByFranchise($userFranchiseId);
        if (!$wallet || $wallet->balance < $totalAmount) {
            return redirect()->back()->with('error', 'Insufficient wallet balance. Please top-up first.');
        }

        DB::beginTransaction();

        try {
            foreach ($request->input('certificate_requests') as $reqData) {
                $student = Student::where('id', $reqData['student_id'])
                    ->where('franchise_id', $userFranchiseId)
                    ->first();

                if (!$student) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Invalid student selection.');
                }

                $payment = $payments->where('id', $reqData['payment_id'])->first();

                if (!$payment) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Invalid payment selection.');
                }

                // Check for duplicate requests
                $existingRequest = CertificateRequest::where('payment_id', $reqData['payment_id'])->first();
                if ($existingRequest) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Certificate request already exists for payment ID ' . $reqData['payment_id']);
                }

                CertificateRequest::create([
                    'franchise_id' => $userFranchiseId,
                    'student_id' => $reqData['student_id'],
                    'course_id' => $payment->course_id,
                    'payment_id' => $reqData['payment_id'],
                    'certificate_type' => $reqData['certificate_type'] ?? 'General Certificate',
                    'special_note' => $reqData['special_note'] ?? null,
                    'status' => 'pending',
                    'requested_at' => now(),
                ]);
            }

            // Deduct total amount from wallet
            $walletController->deductForCertificateBatch($userFranchiseId, $totalAmount, [
                'payment_ids' => $paymentIds,
                'certificate_count' => count($paymentIds),
            ]);

            DB::commit();

            return redirect()->route('franchise.certificate-requests.index')
                ->with('success', 'Certificate request(s) submitted successfully! Wallet debited ₹' . number_format($totalAmount, 2));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to process certificate requests: ' . $e->getMessage());
        }
    }

    public function show(CertificateRequest $certificateRequest)
    {
        $userFranchiseId = Auth::user()->franchise_id;

        if ($certificateRequest->franchise_id !== $userFranchiseId) {
            abort(403, 'This action is unauthorized.');
        }

        $certificateRequest->load(['student', 'course', 'payment']);
        return view('franchise.certificate-requests.show', compact('certificateRequest'));
    }
}
