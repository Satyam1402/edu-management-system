<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Display course catalog for franchise
     */
    public function index(Request $request)
    {
        $franchiseId = Auth::user()->franchise_id;

        $query = Course::where('status', 'active')
                      ->withCount(['enrollments as my_students_count' => function($q) use ($franchiseId) {
                          $q->where('franchise_id', $franchiseId);
                      }]);

        // 🔍 Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 🏷️ Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // 📊 Filter by level
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // 💰 Filter by pricing
        if ($request->filled('pricing')) {
            switch ($request->pricing) {
                case 'free':
                    $query->where('is_free', true);
                    break;
                case 'paid':
                    $query->where('is_free', false);
                    break;
                case 'franchise':
                    $query->whereNotNull('franchise_fee');
                    break;
            }
        }

        // 🌟 Featured courses first
        $query->orderByDesc('is_featured')
              ->orderBy('name');

        $courses = $query->paginate(12);

        // 📊 Statistics for dashboard cards
        $stats = [
            'total_courses' => Course::where('status', 'active')->count(),
            'my_enrolled_students' => $this->getMyEnrolledStudentsCount(),
            'total_revenue' => $this->getTotalRevenue(),
            'active_enrollments' => $this->getActiveEnrollmentsCount(),
        ];

        return view('franchise.courses.index', compact('courses', 'stats'));
    }

    /**
     * Display course details for franchise
     */
    public function show(Course $course)
    {
        // Only show active courses
        if ($course->status !== 'active') {
            abort(404);
        }

        $franchiseId = Auth::user()->franchise_id;

        // Get enrollment stats for this course
        $enrollmentStats = [
            'my_students' => $course->enrollments()->where('franchise_id', $franchiseId)->count(),
            'total_slots' => $course->max_students,
            'available_slots' => $course->max_students ? $course->max_students - $course->enrollments()->count() : 'Unlimited',
            'my_revenue' => $this->getCourseRevenue($course->id),
        ];

        return view('franchise.courses.show', compact('course', 'enrollmentStats'));
    }

    /**
     * Show enrolled students for a specific course
     */
    public function students(Course $course)
    {
        $franchiseId = Auth::user()->franchise_id;

        // Get students enrolled by this franchise for this course
        $students = User::whereHas('enrollments', function($q) use ($course, $franchiseId) {
                           $q->where('course_id', $course->id)
                             ->where('franchise_id', $franchiseId);
                       })
                       ->with(['enrollments' => function($q) use ($course) {
                           $q->where('course_id', $course->id);
                       }])
                       ->paginate(15);

        return view('franchise.courses.students', compact('course', 'students'));
    }

    /**
     * Revenue tracking for courses
     */
    public function revenue()
    {
        $franchiseId = Auth::user()->franchise_id;

        // Revenue by course
        $courseRevenues = Course::where('status', 'active')
                               ->withSum(['enrollments as total_revenue' => function($q) use ($franchiseId) {
                                   $q->where('franchise_id', $franchiseId)
                                     ->where('payment_status', 'paid');
                               }], 'amount_paid')
                               ->having('total_revenue', '>', 0)
                               ->orderByDesc('total_revenue')
                               ->get();

        $totalRevenue = $courseRevenues->sum('total_revenue');

        return view('franchise.courses.revenue', compact('courseRevenues', 'totalRevenue'));
    }

    // Helper methods
    private function getMyEnrolledStudentsCount()
    {
        $franchiseId = Auth::user()->franchise_id;
        return User::whereHas('enrollments', function($q) use ($franchiseId) {
                      $q->where('franchise_id', $franchiseId);
                  })->count();
    }

    private function getTotalRevenue()
    {
        $franchiseId = Auth::user()->franchise_id;
        return Enrollment::where('franchise_id', $franchiseId)
                         ->where('payment_status', 'paid')
                         ->sum('amount_paid');
    }

    private function getActiveEnrollmentsCount()
    {
        $franchiseId = Auth::user()->franchise_id;
        return Enrollment::where('franchise_id', $franchiseId)
                         ->where('status', 'active')
                         ->count();
    }

    private function getCourseRevenue($courseId)
    {
        $franchiseId = Auth::user()->franchise_id;
        return Enrollment::where('course_id', $courseId)
                         ->where('franchise_id', $franchiseId)
                         ->where('payment_status', 'paid')
                         ->sum('amount_paid');
    }
}
