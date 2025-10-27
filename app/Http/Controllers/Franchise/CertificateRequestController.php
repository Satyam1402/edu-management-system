<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\CertificateRequest;
use App\Models\Student;
use App\Models\Course;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                ->addColumn('student_name', function($row) {
                    return $row->student ? $row->student->name : 'N/A';
                })
                ->addColumn('course_name', function($row) {
                    return $row->course ? $row->course->name : 'General Certificate';
                })
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
                ->addColumn('payment_status', function($row) {
                    return $row->payment ?
                        "<span class='badge badge-success'>Paid</span>" :
                        "<span class='badge badge-danger'>Unpaid</span>";
                })
                ->addColumn('action', function($row) {
                    $btn = '<div class="btn-group btn-group-sm">';
                    $btn .= '<a href="'.route('franchise.certificate-requests.show', $row->id).'" class="btn btn-info btn-sm" title="View"><i class="fas fa-eye"></i></a>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['status_badge', 'payment_status', 'action'])
                ->make(true);
        }

        return view('franchise.certificate-requests.index');
    }

    public function create()
    {
        $userFranchiseId = Auth::user()->franchise_id;

        // Debug: Check if franchise_id exists
        if (!$userFranchiseId) {
            return redirect()->back()->with('error', 'Your account is not associated with any franchise.');
        }

        // Get students belonging to this franchise
        $students = Student::where('franchise_id', $userFranchiseId)
            ->where('status', 'active')
            ->get();

        // Get completed payments for students belonging to this franchise
        $payments = Payment::whereHas('student', function($q) use ($userFranchiseId) {
                $q->where('franchise_id', $userFranchiseId);
            })
            ->where('status', 'completed')
            ->with(['student', 'course'])
            ->get();

        return view('franchise.certificate-requests.create', compact('students', 'payments'));
    }

    public function store(Request $request)
    {
        // ✅ CORRECT VALIDATION WITH PROPER FIELD NAMES
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'certificate_type' => 'nullable|string',
            'payment_id' => 'required|exists:payments,id',
            'special_note' => 'nullable|string|max:500'
        ]);

        $userFranchiseId = Auth::user()->franchise_id;

        // Verify student belongs to this franchise
        $student = Student::where('id', $request->student_id)
            ->where('franchise_id', $userFranchiseId)
            ->first();

        if (!$student) {
            return redirect()->back()->with('error', 'Invalid student selection.');
        }

        // Verify payment belongs to this student and is completed
        $payment = Payment::where('id', $request->payment_id)
            ->where('student_id', $request->student_id)
            ->where('status', 'completed')
            ->first();

        if (!$payment) {
            return redirect()->back()->with('error', 'Invalid payment selection or payment not completed.');
        }

        // Check if certificate request already exists for this payment
        $existingRequest = CertificateRequest::where('payment_id', $request->payment_id)->first();
        if ($existingRequest) {
            return redirect()->back()->with('error', 'Certificate request already exists for this payment.');
        }

        // Create certificate request
        CertificateRequest::create([
            'franchise_id' => $userFranchiseId, // ✅ CORRECT FRANCHISE ID
            'student_id' => $request->student_id,
            'course_id' => $payment->course_id, // Get course from payment
            'payment_id' => $request->payment_id,
            'certificate_type' => $request->certificate_type ?? 'General Certificate',
            'special_note' => $request->special_note,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        return redirect()->route('franchise.certificate-requests.index')
            ->with('success', 'Certificate request submitted successfully! 🎉');
    }

    public function show(CertificateRequest $certificateRequest)
    {
        $userFranchiseId = Auth::user()->franchise_id;

        // Verify this request belongs to the current franchise
        if ($certificateRequest->franchise_id !== $userFranchiseId) {
            abort(403, 'This action is unauthorized.');
        }

        $certificateRequest->load(['student', 'course', 'payment']);
        return view('franchise.certificate-requests.show', compact('certificateRequest'));
    }
}
