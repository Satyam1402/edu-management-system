<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Student;
use App\Models\Course;
use App\Models\Franchise;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    /**
     * Display a listing of certificates
     */
    public function index(Request $request): View
    {
        $query = Certificate::with(['student', 'course']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhereHas('student', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $certificates = $query->latest()->paginate(15);
        
        $franchises = Franchise::active()->get();
        $courses = Course::active()->get();

        return view('admin.certificates.index', compact(
            'certificates', 
            'franchises', 
            'courses'
        ));
    }

    /**
     * Show the form for creating a new certificate
     */
    public function create(Request $request): View
    {
        $students = Student::active()->with(['course', 'franchise'])->get();
        $courses = Course::active()->get();
        
        // Pre-select student if provided
        $selectedStudent = null;
        if ($request->filled('student')) {
            $selectedStudent = Student::with(['course', 'franchise'])->find($request->student);
        }

        return view('admin.certificates.create', compact(
            'students',
            'courses',
            'selectedStudent'
        ));
    }

    /**
     * Store a newly created certificate
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'status' => 'required|in:requested,approved,issued'
        ]);

        // Create certificate
        $certificate = Certificate::create([
            'student_id' => $validated['student_id'],
            'course_id' => $validated['course_id'],
            'number' => $this->generateCertificateNumber(),
            'status' => $validated['status'],
            'issued_at' => $validated['status'] === 'issued' ? now() : null
        ]);

        return redirect()->route('admin.certificates.show', $certificate)
            ->with('success', 'Certificate created successfully!');
    }

    /**
     * Display the specified certificate
     */
    public function show(Certificate $certificate): View
    {
        $certificate->load(['student', 'course']);
        
        return view('admin.certificates.show', compact('certificate'));
    }

    /**
     * Show the form for editing the specified certificate
     */
    public function edit(Certificate $certificate): View
    {
        $students = Student::active()->with(['course', 'franchise'])->get();
        $courses = Course::active()->get();

        return view('admin.certificates.edit', compact(
            'certificate',
            'students',
            'courses'
        ));
    }

    /**
     * Update the specified certificate
     */
    public function update(Request $request, Certificate $certificate): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'status' => 'required|in:requested,approved,issued'
        ]);

        // Update issued_at when status changes to issued
        if ($validated['status'] === 'issued' && $certificate->status !== 'issued') {
            $validated['issued_at'] = now();
        } elseif ($validated['status'] !== 'issued') {
            $validated['issued_at'] = null;
        }

        $certificate->update($validated);

        return redirect()->route('admin.certificates.show', $certificate)
            ->with('success', 'Certificate updated successfully!');
    }

    /**
     * Remove the specified certificate
     */
    public function destroy(Certificate $certificate): RedirectResponse
    {
        if ($certificate->status === 'issued') {
            return redirect()->back()->with('error', 'Cannot delete an issued certificate.');
        }

        $certificate->delete();

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Certificate deleted successfully!');
    }

    /**
     * Approve a certificate
     */
    public function approve(Certificate $certificate): RedirectResponse
    {
        if ($certificate->status !== 'requested') {
            return redirect()->back()->with('error', 'Only requested certificates can be approved.');
        }

        $certificate->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Certificate approved successfully!');
    }

    /**
     * Reject a certificate
     */
    public function reject(Certificate $certificate): RedirectResponse
    {
        if ($certificate->status === 'issued') {
            return redirect()->back()->with('error', 'Cannot reject an issued certificate.');
        }

        $certificate->update(['status' => 'requested']);

        return redirect()->back()->with('success', 'Certificate rejected successfully!');
    }

    /**
     * Issue a certificate
     */
    public function issue(Certificate $certificate): RedirectResponse
    {
        if ($certificate->status !== 'approved') {
            return redirect()->back()->with('error', 'Only approved certificates can be issued.');
        }

        $certificate->update([
            'status' => 'issued',
            'issued_at' => now()
        ]);

        return redirect()->back()->with('success', 'Certificate issued successfully!');
    }

    /**
     * Generate certificate number
     */
    private function generateCertificateNumber(): string
    {
        $prefix = 'CERT';
        $year = date('Y');
        $month = date('m');
        
        // Get the last certificate number for this month
        $lastCert = Certificate::where('number', 'like', "{$prefix}-{$year}{$month}-%")
                       ->orderBy('number', 'desc')
                       ->first();
                       
        if ($lastCert) {
            $lastNumber = (int) substr($lastCert->number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "{$prefix}-{$year}{$month}-{$newNumber}";
    }
}
