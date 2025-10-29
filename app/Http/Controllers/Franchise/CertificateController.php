<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Stats calculation first
            $franchiseId = auth()->user()->franchise_id;

            $stats = [
                'total_certificates' => Certificate::where('franchise_id', $franchiseId)
                    ->where('status', 'issued')->count(),
                'this_month' => Certificate::where('franchise_id', $franchiseId)
                    ->where('status', 'issued')
                    ->whereMonth('issued_at', now()->month)
                    ->whereYear('issued_at', now()->year)
                    ->count(),
            ];

            // Handle AJAX requests for DataTables
            if ($request->ajax()) {
                $certificates = Certificate::with(['student', 'course'])
                    ->where('franchise_id', $franchiseId)
                    ->where('status', 'issued')
                    ->orderBy('issued_at', 'desc');

                return DataTables::of($certificates)
                    ->addIndexColumn()
                    ->editColumn('student_info', function($certificate) {
                        $studentName = $certificate->student ? $certificate->student->name : 'Unknown Student';
                        $studentEmail = $certificate->student ? $certificate->student->email : 'No email';

                        return '<div class="student-info">
                            <strong>' . htmlspecialchars($studentName) . '</strong><br>
                            <small class="text-muted">' . htmlspecialchars($studentEmail) . '</small>
                        </div>';
                    })
                    ->editColumn('course_info', function($certificate) {
                        return $certificate->course ? htmlspecialchars($certificate->course->name) : '<em class="text-muted">General Certificate</em>';
                    })
                    ->editColumn('certificate_number', function($certificate) {
                        return '<span class="badge badge-primary font-weight-bold">' . htmlspecialchars($certificate->number) . '</span>';
                    })
                    ->editColumn('issued_date', function($certificate) {
                        if ($certificate->issued_at) {
                            return '<div class="text-center">
                                <strong>' . $certificate->issued_at->format('M d, Y') . '</strong><br>
                                <small class="text-muted">' . $certificate->issued_at->format('h:i A') . '</small>
                            </div>';
                        }
                        return '<span class="text-muted">Not specified</span>';
                    })
                    ->editColumn('status_badge', function($certificate) {
                        return '<span class="status-badge status-issued">Issued</span>';
                    })
                    ->addColumn('actions', function($certificate) {
                        return '
                        <div class="btn-group-custom" role="group">
                            <button type="button" onclick="viewCertificate(' . $certificate->id . ')"
                                    class="btn btn-info btn-sm"
                                    data-toggle="tooltip"
                                    title="View Certificate">
                                <i class="fas fa-eye"></i>
                            </button>
                            <a href="' . route('franchise.certificates.download', $certificate->id) . '"
                               class="btn btn-success btn-sm"
                               data-toggle="tooltip"
                               title="Download PDF"
                               target="_blank">
                                <i class="fas fa-download"></i>
                            </a>
                            <a href="' . route('franchise.certificates.print', $certificate->id) . '"
                               class="btn btn-warning btn-sm"
                               data-toggle="tooltip"
                               title="Print Certificate"
                               target="_blank">
                                <i class="fas fa-print"></i>
                            </a>
                        </div>';
                    })
                    ->rawColumns(['student_info', 'course_info', 'certificate_number', 'issued_date', 'status_badge', 'actions'])
                    ->make(true);
            }

            return view('franchise.certificates.index', compact('stats'));

        } catch (\Exception $e) {
            Log::error('Certificate Index Error: ' . $e->getMessage() . ' - Line: ' . $e->getLine());

            if ($request->ajax()) {
                return response()->json([
                    'draw' => $request->get('draw'),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'Unable to load certificates: ' . $e->getMessage()
                ], 500);
            }

            return view('franchise.certificates.index', [
                'stats' => ['total_certificates' => 0, 'this_month' => 0]
            ])->with('error', 'Unable to load certificates. Please try again.');
        }
    }

    public function show($id)
    {
        try {
            $certificate = Certificate::with(['student', 'course'])
                ->where('franchise_id', auth()->user()->franchise_id)
                ->where('status', 'issued')
                ->findOrFail($id);

            return view('franchise.certificates.show', compact('certificate'));

        } catch (\Exception $e) {
            Log::error('Certificate Show Error: ' . $e->getMessage() . ' - Certificate ID: ' . $id);
            return redirect()->route('franchise.certificates.index')
                ->with('error', 'Certificate not found or access denied.');
        }
    }

    public function download($id)
    {
        try {
            $certificate = Certificate::with(['student', 'course'])
                ->where('franchise_id', auth()->user()->franchise_id)
                ->where('status', 'issued')
                ->findOrFail($id);

            // Try different template paths based on what exists
            $templatePaths = [
                'franchise.certificates.pdf-template',
                'franchise.certificates.pdf',
                'certificates.pdf-template'
            ];

            $template = null;
            foreach ($templatePaths as $path) {
                if (view()->exists($path)) {
                    $template = $path;
                    break;
                }
            }

            if (!$template) {
                throw new \Exception('PDF template not found. Please ensure the template file exists.');
            }

            $pdf = PDF::loadView($template, compact('certificate'))
                ->setPaper('a4', 'landscape')
                ->setOptions([
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'debugKeepTemp' => false,
                    'defaultFont' => 'sans-serif',
                ]);

            $filename = 'Certificate-' . $certificate->number . '.pdf';
            return $pdf->download($filename);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Certificate Not Found for Download - ID: ' . $id . ' - Franchise: ' . auth()->user()->franchise_id);
            return redirect()->back()->with('error', 'Certificate not found or you do not have permission to download it.');

        } catch (\Exception $e) {
            Log::error('Certificate Download Error: ' . $e->getMessage() . ' - Certificate ID: ' . $id . ' - Line: ' . $e->getLine());
            return redirect()->back()->with('error', 'Unable to download certificate: ' . $e->getMessage());
        }
    }

    public function print($id)
    {
        try {
            $certificate = Certificate::with(['student', 'course'])
                ->where('franchise_id', auth()->user()->franchise_id)
                ->where('status', 'issued')
                ->findOrFail($id);

            // Try different template paths for print
            $templatePaths = [
                'franchise.certificates.print-template',
                'franchise.certificates.print',
                'certificates.print-template'
            ];

            $template = null;
            foreach ($templatePaths as $path) {
                if (view()->exists($path)) {
                    $template = $path;
                    break;
                }
            }

            if (!$template) {
                // Fallback to show template if print template doesn't exist
                if (view()->exists('franchise.certificates.show')) {
                    $template = 'franchise.certificates.show';
                } else {
                    throw new \Exception('Print template not found. Please ensure the template file exists.');
                }
            }

            return view($template, compact('certificate'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Certificate Not Found for Print - ID: ' . $id . ' - Franchise: ' . auth()->user()->franchise_id);
            return redirect()->back()->with('error', 'Certificate not found or you do not have permission to print it.');

        } catch (\Exception $e) {
            Log::error('Certificate Print Error: ' . $e->getMessage() . ' - Certificate ID: ' . $id . ' - Line: ' . $e->getLine());
            return redirect()->back()->with('error', 'Unable to print certificate: ' . $e->getMessage());
        }
    }

    /**
     * Debug method - Remove this after fixing issues
     */
    public function debug()
    {
        try {
            $franchiseId = auth()->user()->franchise_id;

            $certificates = Certificate::with(['student', 'course'])
                ->where('franchise_id', $franchiseId)
                ->get();

            $debug = [
                'franchise_id' => $franchiseId,
                'total_certificates' => $certificates->count(),
                'issued_certificates' => $certificates->where('status', 'issued')->count(),
                'certificates' => $certificates->map(function($cert) {
                    return [
                        'id' => $cert->id,
                        'number' => $cert->number,
                        'status' => $cert->status,
                        'student_name' => $cert->student ? $cert->student->name : 'No Student',
                        'course_name' => $cert->course ? $cert->course->name : 'No Course',
                        'issued_at' => $cert->issued_at,
                    ];
                }),
                'templates_exist' => [
                    'pdf-template' => view()->exists('franchise.certificates.pdf-template'),
                    'print-template' => view()->exists('franchise.certificates.print-template'),
                    'show' => view()->exists('franchise.certificates.show'),
                ]
            ];

            return response()->json($debug);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
