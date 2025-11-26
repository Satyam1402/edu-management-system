<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Franchise;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    /**
     * Display a listing of certificates with DataTables support
     */
    public function index(Request $request)
    {
        // If it's a DataTables AJAX request
        if ($request->ajax()) {
            $query = Certificate::with(['student', 'course', 'franchise', 'issuedBy'])
                ->where('status', 'issued') // Only show issued certificates
                ->latest();

            // Apply franchise filter
            if ($request->filled('franchise_filter')) {
                $query->where('franchise_id', $request->franchise_filter);
            }

            // Apply date range filters
            if ($request->filled('date_from')) {
                $query->whereDate('issued_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('issued_at', '<=', $request->date_to);
            }

            return DataTables::of($query)
                ->addColumn('certificate_number', function ($certificate) {
                    return '<span class="badge badge-primary" style="font-size: 13px;">' 
                           . $certificate->number . '</span>';
                })
                ->addColumn('student_info', function ($certificate) {
                    $html = '<div>';
                    $html .= '<strong>' . ($certificate->student->name ?? 'N/A') . '</strong><br>';
                    $html .= '<small class="text-muted">' . ($certificate->student->email ?? '') . '</small>';
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('course_info', function ($certificate) {
                    return '<span class="badge badge-success" style="font-size: 12px; padding: 6px 12px;">' 
                           . ($certificate->course->name ?? 'N/A') . '</span>';
                })
                ->addColumn('franchise_info', function ($certificate) {
                    if ($certificate->franchise) {
                        return '<div>' 
                               . '<strong>' . $certificate->franchise->name . '</strong><br>'
                               . '<small class="text-muted">' . ($certificate->franchise->city ?? '') . '</small>'
                               . '</div>';
                    }
                    return '<span class="text-muted">N/A</span>';
                })
                ->addColumn('status', function ($certificate) {
                    $badges = [
                        'issued' => '<span class="badge badge-success">Issued</span>',
                        'pending' => '<span class="badge badge-warning">Pending</span>',
                        'approved' => '<span class="badge badge-info">Approved</span>',
                    ];
                    return $badges[$certificate->status] ?? '<span class="badge badge-secondary">' . ucfirst($certificate->status) . '</span>';
                })
                ->addColumn('issued_by', function ($certificate) {
                    if ($certificate->issued_at) {
                        $html = '<div>';
                        $html .= '<strong>' . ($certificate->issuedBy->name ?? 'System') . '</strong><br>';
                        $html .= '<small class="text-muted">' . $certificate->issued_at->format('M d, Y') . '</small>';
                        $html .= '</div>';
                        return $html;
                    }
                    return '<span class="text-muted">Not issued yet</span>';
                })
                ->addColumn('actions', function ($certificate) {
                    $actions = '<div class="btn-group btn-group-sm" role="group">';
                    
                    // View button
                    $actions .= '<a href="' . route('admin.certificates.show', $certificate->id) . '" '
                              . 'class="btn btn-info" title="View Details">'
                              . '<i class="fas fa-eye"></i></a>';
                    
                    // Download button
                    if ($certificate->status === 'issued') {
                        $actions .= '<a href="' . route('admin.certificates.download', $certificate->id) . '" '
                                  . 'class="btn btn-success" title="Download PDF">'
                                  . '<i class="fas fa-download"></i></a>';
                    }
                    
                    // View Request button (if linked to a request)
                    if ($certificate->certificateRequest) {
                        $actions .= '<a href="' . route('admin.certificate-requests.show', $certificate->certificateRequest->id) . '" '
                                  . 'class="btn btn-primary" title="View Request">'
                                  . '<i class="fas fa-clipboard-list"></i></a>';
                    }
                    
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['certificate_number', 'student_info', 'course_info', 'franchise_info', 'status', 'issued_by', 'actions'])
                ->make(true);
        }

        // Regular page load - pass statistics and franchises
        $stats = [
            'total' => Certificate::where('status', 'issued')->count(),
            'issued' => Certificate::where('status', 'issued')->count(),
            'this_month' => Certificate::where('status', 'issued')
                ->whereMonth('issued_at', now()->month)
                ->whereYear('issued_at', now()->year)
                ->count(),
            'this_week' => Certificate::where('status', 'issued')
                ->whereBetween('issued_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
        ];

        $franchises = Franchise::orderBy('name')->get();

        return view('admin.certificates.index', compact('stats', 'franchises'));
    }

    /**
     * Display the specified certificate
     */
    public function show($id)
    {
        $certificate = Certificate::with(['student', 'course', 'franchise', 'issuedBy', 'certificateRequest'])
            ->findOrFail($id);

        return view('admin.certificates.show', compact('certificate'));
    }

    /**
     * Download certificate as PDF
     */
    public function download($id)
    {
        $certificate = Certificate::with(['student', 'course', 'franchise', 'issuedBy'])
            ->findOrFail($id);

        // Only allow download for issued certificates
        if ($certificate->status !== 'issued') {
            return redirect()
                ->back()
                ->with('error', 'Only issued certificates can be downloaded.');
        }

        // Generate PDF
        $pdf = Pdf::loadView('admin.certificates.pdf-template', compact('certificate'))
            ->setPaper('a4', 'landscape');

        // Download with certificate number in filename
        return $pdf->download('certificate-' . $certificate->number . '.pdf');
    }

    /**
     * Export certificates to CSV
     */
    public function export(Request $request)
    {
        $certificates = Certificate::with(['student', 'course', 'franchise', 'issuedBy'])
            ->where('status', 'issued')
            ->latest()
            ->get();

        $filename = 'certificates-export-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($certificates) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Certificate Number',
                'Student Name',
                'Student Email',
                'Course Name',
                'Franchise',
                'Status',
                'Issued Date',
                'Issued By'
            ]);

            // CSV Data
            foreach ($certificates as $cert) {
                fputcsv($file, [
                    $cert->number,
                    $cert->student->name ?? 'N/A',
                    $cert->student->email ?? 'N/A',
                    $cert->course->name ?? 'N/A',
                    $cert->franchise->name ?? 'N/A',
                    ucfirst($cert->status),
                    $cert->issued_at ? $cert->issued_at->format('Y-m-d') : 'N/A',
                    $cert->issuedBy->name ?? 'System'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
