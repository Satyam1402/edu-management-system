<?php
namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Course;
use App\Models\Certificate;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Check if user has franchise assigned
        if (!$user->franchise_id) {
            return redirect()->route('login')->with('error', 'No franchise assigned to your account. Please contact administrator.');
        }

        $franchise = $user->franchise;

        if (!$franchise) {
            return redirect()->route('login')->with('error', 'Franchise not found. Please contact administrator.');
        }

        // Get franchise-specific statistics - ALL REQUIRED KEYS
        $stats = [
            'students' => Student::where('franchise_id', $franchise->id)->count(),
            'active_students' => Student::where('franchise_id', $franchise->id)->where('status', 'active')->count(),
            'courses' => Course::where('status', 'active')->count(), // ← ADDED THIS
            'certificates' => Certificate::whereHas('student', function($query) use ($franchise) { // ← ADDED THIS
                $query->where('franchise_id', $franchise->id);
            })->count(),

            // Additional stats for later use
            'completed_students' => Student::where('franchise_id', $franchise->id)->where('status', 'graduated')->count(),
            'total_revenue' => 0, // Will calculate when payments system is ready
            'pending_payments' => 0, // Will calculate when payments system is ready
        ];

        // Recent students (last 5)
        $recent_students = Student::where('franchise_id', $franchise->id)
                                 ->latest()
                                 ->take(5)
                                 ->get();

        // Available courses
        $available_courses = Course::where('status', 'active')->take(6)->get();

        // Recent certificates
        $recent_certificates = collect(); // Empty collection for now, will populate when certificates exist

        return view('franchise.dashboard', compact(
            'stats',
            'recent_students',
            'franchise',
            'available_courses',
            'recent_certificates'
        ));
    }
}
