<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    /**
     * Display approved certificates for this franchise
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $certificates = Certificate::with(['student', 'course', 'certificateRequest'])
                ->whereHas('certificateRequest', function($query) {
                    $query->where('franchise_id', auth()->user()->franchise_id)
                          ->where('status', 'approved');
                })
                ->orderBy('created_at', 'desc');

            return DataTables::of($certificates)
                ->addIndexColumn()
                ->addColumn('student_info', function($row) {
                    return "
                        <div class='student-info'>
                            <strong>{$row->student->name}</strong><br>
                            <small class='text-muted'>{$row->student->email}</small>
                        </div>
                    ";
                })
                ->addColumn('course_info', function($row) {
                    return $row->course ? $row->course->name : '<em class="text-muted">General Certificate</em>';
                })
                ->addColumn('certificate_number', function($row) {
                    return "<strong class='text-primary'>{$row->number}</strong>";
                })
                ->addColumn('issued_date', function($row) {
                    return "<div class='text-center'>
                                <strong>" . $row->issued_at->format('d M Y') . "</strong><br>
                                <small class='text-muted'>" . $row->issued_at->format('h:i A') . "</small>
                            </div>";
                })
                ->addColumn('status_badge', function($row) {
                    $color = $row->status === 'issued' ? 'success' : 'info';
                    return "<span class='badge badge-{$color}'>
                                <i class='fas fa-certificate mr-1'></i>" . ucfirst($row->status) . "
                            </span>";
                })
                ->addColumn('actions', function($row) {
                    return "
                        <div class='btn-group-actions'>
                            <button onclick='viewCertificate({$row->id})'
                                    class='btn btn-info btn-sm'
                                    title='Preview Certificate'>
                                <i class='fas fa-eye'></i>
                            </button>
                            <a href='" . route('franchise.certificates.download', $row->id) . "'
                               class='btn btn-success btn-sm'
                               title='Download PDF'>
                                <i class='fas fa-download'></i>
                            </a>
                            <button onclick='printCertificate({$row->id})'
                                    class='btn btn-primary btn-sm'
                                    title='Print Certificate'>
                                <i class='fas fa-print'></i>
                            </button>
                        </div>
                    ";
                })
                ->rawColumns(['student_info', 'course_info', 'certificate_number', 'issued_date', 'status_badge', 'actions'])
                ->make(true);
        }

        // Get stats
        $stats = [
            'total_certificates' => Certificate::whereHas('certificateRequest', function($query) {
                $query->where('franchise_id', auth()->user()->franchise_id);
            })->count(),
            'this_month' => Certificate::whereHas('certificateRequest', function($query) {
                $query->where('franchise_id', auth()->user()->franchise_id);
            })->whereMonth('created_at', now()->month)->count(),
        ];

        return view('franchise.certificates.index', compact('stats'));
    }

    /**
     * Show certificate details
     */
    public function show($id)
    {
        $certificate = Certificate::with(['student', 'course', 'certificateRequest'])
            ->whereHas('certificateRequest', function($query) {
                $query->where('franchise_id', auth()->user()->franchise_id);
            })
            ->findOrFail($id);

        return view('franchise.certificates.show', compact('certificate'));
    }

    /**
     * Download certificate PDF
     */
    public function download($id)
    {
        $certificate = Certificate::with(['student', 'course', 'certificateRequest'])
            ->whereHas('certificateRequest', function($query) {
                $query->where('franchise_id', auth()->user()->franchise_id);
            })
            ->findOrFail($id);

        $pdf = PDF::loadView('certificates.pdf-template', compact('certificate'));

        $filename = 'Certificate-' . $certificate->number . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Print certificate view
     */
    public function print($id)
    {
        $certificate = Certificate::with(['student', 'course', 'certificateRequest'])
            ->whereHas('certificateRequest', function($query) {
                $query->where('franchise_id', auth()->user()->franchise_id);
            })
            ->findOrFail($id);

        return view('certificates.print-template', compact('certificate'));
    }
}
