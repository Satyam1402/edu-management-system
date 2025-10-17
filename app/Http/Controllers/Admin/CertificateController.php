<?php
// app/Http/Controllers/Admin/CertificateController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Display a listing of certificates
     */
    public function index(Request $request)
    {
        $query = Certificate::with(['student', 'course']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('student', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('course', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('number', 'like', "%{$search}%");
        }

        // Status filter
        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }

        $certificates = $query->latest()->paginate(15);

        return view('admin.certificates.index', compact('certificates'));
    }

    /**
     * Display the specified certificate
     */
    public function show(Certificate $certificate)
    {
        $certificate->load(['student', 'course']);
        return view('admin.certificates.show', compact('certificate'));
    }

    /**
     * Update the certificate status (approve/reject)
     */
    public function update(Request $request, Certificate $certificate)
    {
        $request->validate([
            'status' => 'required|in:requested,approved,issued'
        ]);

        $certificate->update([
            'status' => $request->status,
            'issued_at' => $request->status === 'issued' ? now() : null
        ]);

        return redirect()->route('admin.certificates.index')
                        ->with('success', 'Certificate status updated successfully!');
    }
}
