<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        // Handle stats request
        if ($request->has('get_stats')) {
            $stats = [
                'requested' => Certificate::where('status', 'requested')->count(),
                'approved' => Certificate::where('status', 'approved')->count(),
                'issued' => Certificate::where('status', 'issued')->count(),
                'total' => Certificate::count()
            ];
            
            return response()->json(['stats' => $stats]);
        }

        if ($request->ajax()) {
            $query = Certificate::with(['student', 'course']);
            
            // Apply status filter if provided
            if ($request->has('status_filter') && !empty($request->status_filter)) {
                $query->where('status', $request->status_filter);
            }
            
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('certificate_number', function ($certificate) {
                    return '<div>
                                <h6 class="mb-1 font-weight-bold text-primary">CERT-' . $certificate->number . '</h6>
                                <small class="text-muted">' . $certificate->created_at->format('M d, Y') . '</small>
                            </div>';
                })
                ->addColumn('student_name', function ($certificate) {
                    return $certificate->student ? 
                        '<div>
                            <span class="font-weight-bold">' . e($certificate->student->name) . '</span><br>
                            <small class="text-muted">' . e($certificate->student->email ?? 'No email') . '</small>
                        </div>' : 'N/A';
                })
                ->addColumn('course_name', function ($certificate) {
                    return $certificate->course ? 
                        '<span class="badge badge-info">' . e($certificate->course->name) . '</span>' : 'N/A';
                })
                ->addColumn('status', function ($certificate) {
                    $colors = [
                        'requested' => 'warning',
                        'approved' => 'success',
                        'issued' => 'primary'
                    ];
                    $color = $colors[$certificate->status] ?? 'secondary';
                    return '<span class="badge badge-' . $color . '">' . ucfirst($certificate->status) . '</span>';
                })
                ->addColumn('issued_date', function ($certificate) {
                    return $certificate->issued_at ? 
                        $certificate->issued_at->format('M d, Y') : 
                        '<span class="text-muted">Not issued</span>';
                })
                ->addColumn('actions', function ($certificate) {
                    $actions = '<div class="btn-group" role="group">';
                    
                    // View button
                    $actions .= '<a href="' . route('admin.certificates.show', $certificate) . '" class="btn btn-outline-info btn-sm" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>';
                    
                    // Status action buttons
                    if ($certificate->status === 'requested') {
                        $actions .= '<button class="btn btn-outline-success btn-sm" onclick="approveRequest(' . $certificate->id . ')" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>';
                    }
                    
                    if ($certificate->status === 'approved') {
                        $actions .= '<button class="btn btn-outline-primary btn-sm" onclick="issueCertificate(' . $certificate->id . ')" title="Issue">
                                        <i class="fas fa-certificate"></i>
                                    </button>';
                    }
                    
                    if ($certificate->status === 'issued') {
                        $actions .= '<button class="btn btn-outline-secondary btn-sm" title="Download">
                                        <i class="fas fa-download"></i>
                                    </button>';
                    }
                    
                    // Delete button (only for non-issued certificates)
                    if ($certificate->status !== 'issued') {
                        $actions .= '<button class="btn btn-outline-danger btn-sm" onclick="deleteCertificate(' . $certificate->id . ')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>';
                    }
                    
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['certificate_number', 'student_name', 'course_name', 'status', 'issued_date', 'actions'])
                ->make(true);
        }

        return view('admin.certificates.index');
    }

    public function create()
    {
        // Include all relevant student statuses
        $students = Student::whereIn('status', ['active', 'enrolled', 'graduated'])
                        ->orWhereNull('status')
                        ->orderBy('name')
                        ->get();
        
        $courses = Course::where('status', 'active')->get();
        
        return view('admin.certificates.create', compact('students', 'courses'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'status' => 'required|in:requested,approved,issued'
        ]);

        // Generate unique certificate number
        $number = $this->generateCertificateNumber();

        $certificateData = $request->all();
        $certificateData['number'] = $number;
        
        // If issued, set issued_at
        if ($request->status === 'issued') {
            $certificateData['issued_at'] = now();
        }

        Certificate::create($certificateData);

        return redirect()->route('admin.certificates.index')
                         ->with('success', 'Certificate created successfully!');
    }

    public function show(Certificate $certificate)
    {
        $certificate->load(['student', 'course']);
        return view('admin.certificates.show', compact('certificate'));
    }

    public function destroy(Certificate $certificate)
    {
        if ($certificate->status === 'issued') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete issued certificates!'
            ], 400);
        }

        $certificate->delete();

        return response()->json([
            'success' => true,
            'message' => 'Certificate deleted successfully!'
        ]);
    }

    // Status change methods
    public function approve(Certificate $certificate)
    {
    if ($certificate->status !== 'requested') {
        return response()->json([
            'success' => false,
            'message' => 'Only requested certificates can be approved.'
        ], 400);
    }

    $certificate->update(['status' => 'approved']);

    return response()->json([
        'success' => true,
        'message' => 'Certificate approved successfully!'
    ]);
    }

    public function issue(Certificate $certificate)
    {
    if ($certificate->status !== 'approved') {
        return response()->json([
            'success' => false,
            'message' => 'Only approved certificates can be issued.'
        ], 400);
    }

    $certificate->update([
        'status' => 'issued',
        'issued_at' => now()
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Certificate issued successfully!'
    ]);
    }

    public function download(Certificate $certificate)
    {
        if ($certificate->status !== 'issued') {
            return response()->json([
                'success' => false,
                'message' => 'Only issued certificates can be downloaded.'
            ], 400);
        }

        try {
            // Load the certificate with relationships
            $certificate->load(['student', 'course']);
            
            // Generate PDF
            $pdf = Pdf::loadView('admin.certificates.pdf-template', compact('certificate'));
            
            // Set paper size and orientation
            $pdf->setPaper('A4', 'landscape');
            
            // Set options for better rendering
            $pdf->setOptions([
                'dpi' => 150,
                'defaultFont' => 'DejaVu Sans',
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
            ]);
            
            // Generate filename
            $filename = 'certificate-' . $certificate->number . '-' . now()->format('Y-m-d') . '.pdf';
            
            // Return PDF download
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('Certificate PDF generation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error generating PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    // Add export method for certificates list
    public function export()
    {
    // Simple CSV export for now
    $certificates = Certificate::with(['student', 'course'])->get();

    $csvData = [];
    $csvData[] = ['Certificate Number', 'Student Name', 'Course Name', 'Status', 'Created Date', 'Issued Date'];

    foreach ($certificates as $certificate) {
        $csvData[] = [
            $certificate->number,
            $certificate->student->name ?? 'N/A',
            $certificate->course->name ?? 'N/A',
            ucfirst($certificate->status),
            $certificate->created_at->format('Y-m-d'),
            $certificate->issued_at ? $certificate->issued_at->format('Y-m-d') : 'Not issued'
        ];
    }

    $filename = 'certificates-' . date('Y-m-d') . '.csv';

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


    private function generateCertificateNumber()
    {
        do {
            $number = strtoupper(Str::random(8));
        } while (Certificate::where('number', $number)->exists());

        return $number;
    }
}
