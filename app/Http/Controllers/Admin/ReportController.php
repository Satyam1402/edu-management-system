<?php
// app/Http/Controllers/Admin/ReportController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Franchise;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Certificate;
use App\Models\Exam;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        // Financial Summary
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $monthlyRevenue = Payment::where('status', 'completed')
            ->whereMonth('payment_date', now()->month)
            ->sum('amount');

        // Student Statistics
        $totalStudents = Student::count();
        $activeStudents = Student::where('status', 'active')->count();
        $newStudentsThisMonth = Student::whereMonth('enrollment_date', now()->month)->count();

        // Course Statistics
        $totalCourses = Course::count();
        $activeCourses = Course::where('status', 'active')->count();

        // Franchise Statistics
        $totalFranchises = Franchise::count();
        $activeFranchises = Franchise::where('status', 'active')->count();

        // Certificate Statistics
        $totalCertificates = Certificate::count();
        $pendingCertificates = Certificate::where('status', 'requested')->count();
        $approvedCertificates = Certificate::where('status', 'approved')->count();

        // Recent Activity
        $recentStudents = Student::latest()->take(5)->get();
        $recentPayments = Payment::with(['student'])->latest()->take(5)->get();

        return view('admin.reports.index', compact(
            'totalRevenue', 'monthlyRevenue', 'totalStudents', 'activeStudents',
            'newStudentsThisMonth', 'totalCourses', 'activeCourses',
            'totalFranchises', 'activeFranchises', 'totalCertificates',
            'pendingCertificates', 'approvedCertificates', 'recentStudents', 'recentPayments'
        ));
    }

    public function financial()
    {
        $monthlyRevenue = Payment::where('status', 'completed')
            ->selectRaw('MONTH(payment_date) as month, SUM(amount) as total')
            ->whereYear('payment_date', now()->year)
            ->groupBy('month')
            ->get();

        $paymentMethods = Payment::where('status', 'completed')
            ->selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get();

        return view('admin.reports.financial', compact('monthlyRevenue', 'paymentMethods'));
    }

    public function students()
    {
        $studentsByFranchise = Student::with('franchise')
            ->selectRaw('franchise_id, COUNT(*) as count')
            ->groupBy('franchise_id')
            ->get();

        $studentsByCourse = Student::with('course')
            ->selectRaw('course_id, COUNT(*) as count')
            ->groupBy('course_id')
            ->get();

        return view('admin.reports.students', compact('studentsByFranchise', 'studentsByCourse'));
    }

    public function courses()
    {
        $courseEnrollments = Course::withCount('students')->get();
        $courseRevenue = Payment::with('course')
            ->where('status', 'completed')
            ->selectRaw('course_id, SUM(amount) as revenue')
            ->groupBy('course_id')
            ->get();

        return view('admin.reports.courses', compact('courseEnrollments', 'courseRevenue'));
    }
}
