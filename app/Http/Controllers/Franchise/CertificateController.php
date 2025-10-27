<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $certificates = Certificate::with('student')
                ->whereHas('student', function ($q) {
                    $q->where('franchise_id', Auth::user()->franchise_id);
                })
                ->select('certificates.*');

            return DataTables::of($certificates)
                ->addColumn('student', function ($cert) {
                    return $cert->student ? $cert->student->name : '-';
                })
                ->addColumn('issued_date', function ($cert) {
                    return $cert->issued_date ? \Carbon\Carbon::parse($cert->issued_date)->format('d M Y') : '-';
                })
                ->addColumn('action', function ($cert) {
                    // Only include 'View' action to match restrictions
                    return '<a href="'.route('franchise.certificates.show', $cert).'" class="btn btn-sm btn-info">View</a>';
                })
                ->rawColumns(['action']) // allow html for action buttons
                ->make(true);
        }

        return view('franchise.certificates.index');
    }

    public function show(Certificate $certificate)
    {
        $this->authorize('view', $certificate);
        return view('franchise.certificates.show', compact('certificate'));
    }


    // public function view(User $user, Certificate $certificate)
    // {
    //     return $certificate->student && $certificate->student->franchise_id === $user->franchise_id;
    // }
}
