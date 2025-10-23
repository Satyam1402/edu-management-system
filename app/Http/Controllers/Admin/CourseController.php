<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Display a listing of courses
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Course::query()
                        ->selectRaw('courses.*, (SELECT COUNT(*) FROM students WHERE students.course_id = courses.id AND students.deleted_at IS NULL) as students_count');

            // Apply filters
            if ($request->filled('status')) {
                $query->where('courses.status', $request->status);
            }

            if ($request->filled('level')) {
                $query->where('courses.level', $request->level);
            }

            if ($request->filled('category')) {
                $query->where('courses.category', $request->category);
            }

            if ($request->filled('featured')) {
                $query->where('courses.is_featured', $request->featured === 'yes');
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($course) {
                    return '<div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input course-checkbox" id="course_'.$course->id.'" value="'.$course->id.'">
                                <label class="custom-control-label" for="course_'.$course->id.'"></label>
                            </div>';
                })
                ->addColumn('course_details', function ($course) {
                    $featuredBadge = $course->is_featured ? '<span class="badge badge-warning badge-sm ml-1">Featured</span>' : '';
                    $levelBadge = '<span class="badge badge-' . ($course->level_badge ?? 'info') . ' badge-sm">' . ucfirst($course->level ?? 'N/A') . '</span>';
                    
                    $description = $course->description ? Str::limit($course->description, 50) : 'No description available';
                    
                    return '<div>
                                <h6 class="mb-1 font-weight-bold">' . e($course->name) . '</h6>
                                <p class="text-muted small mb-1">' . e($description) . '</p>
                                <div>' . $levelBadge . $featuredBadge . '</div>
                            </div>';
                })
                ->addColumn('course_code', function ($course) {
                    return '<span class="badge badge-primary px-3 py-2 font-weight-bold">' . e($course->code) . '</span>';
                })
                ->addColumn('duration', function ($course) {
                    return '<div class="text-center">
                                <i class="fas fa-clock text-info mr-1"></i>
                                <span class="font-weight-bold">' . e($course->duration_text) . '</span>
                            </div>';
                })
                ->addColumn('fee', function ($course) {
                    return '<div class="text-center">
                                <span class="font-weight-bold text-success">' . e($course->formatted_fee) . '</span>
                            </div>';
                })
                ->addColumn('students', function ($course) {
                    $enrolledCount = $course->students_count ?? 0;
                    $maxStudents = $course->max_students;
                    
                    $badgeClass = 'badge-info';
                    if ($maxStudents && $enrolledCount >= $maxStudents) {
                        $badgeClass = 'badge-danger';
                    } elseif ($maxStudents && $enrolledCount >= ($maxStudents * 0.8)) {
                        $badgeClass = 'badge-warning';
                    }
                    
                    $displayText = $enrolledCount;
                    if ($maxStudents) {
                        $displayText .= '/' . $maxStudents;
                    }
                    
                    return '<div class="text-center">
                                <span class="badge ' . $badgeClass . ' px-3 py-2">' . $displayText . ' enrolled</span>
                            </div>';
                })
                ->addColumn('status', function ($course) {
                    $statusColors = [
                        'active' => 'success',
                        'inactive' => 'secondary', 
                        'draft' => 'warning',
                        'archived' => 'danger'
                    ];
                    $color = $statusColors[$course->status] ?? 'secondary';
                    return '<div class="text-center">
                                <span class="badge badge-' . $color . ' px-3 py-2">' . ucfirst($course->status) . '</span>
                            </div>';
                })
                ->addColumn('actions', function ($course) {
                    return '<div class="d-flex justify-content-center">
                                <button class="btn btn-outline-info btn-sm mr-1" onclick="quickView(' . $course->id . ')" title="Quick View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="' . route('admin.courses.show', $course->id) . '" class="btn btn-outline-success btn-sm mr-1" title="View Details">
                                    <i class="fas fa-book"></i>
                                </a>
                                <a href="' . route('admin.courses.edit', $course->id) . '" class="btn btn-outline-primary btn-sm mr-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-outline-warning btn-sm mr-1" onclick="toggleFeatured(' . $course->id . ')" title="' . ($course->is_featured ? 'Remove Featured' : 'Mark Featured') . '">
                                    <i class="fas fa-star"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm" onclick="deleteCourse(' . $course->id . ')" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>';
                })


                // FIX: Override the global search to only search specific columns
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && !empty($request->input('search.value'))) {
                        $searchValue = $request->input('search.value');
                        $query->where(function($q) use ($searchValue) {
                            $q->where('courses.name', 'like', "%{$searchValue}%")
                            ->orWhere('courses.code', 'like', "%{$searchValue}%")
                            ->orWhere('courses.description', 'like', "%{$searchValue}%")
                            ->orWhere('courses.category', 'like', "%{$searchValue}%")
                            ->orWhere('courses.level', 'like', "%{$searchValue}%")
                            ->orWhere('courses.instructor_name', 'like', "%{$searchValue}%");
                        });
                    }
                })
                ->rawColumns(['checkbox', 'course_details', 'course_code', 'duration', 'fee', 'students', 'status', 'actions'])
                ->make(true);
        }

        return view('admin.courses.index');
    }

    /**
     * Show the form for creating a new course
     */
    public function create(): View
    {
        return view('admin.courses.create');
    }

    /**
     * Store a newly created course
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'fee' => 'required|numeric|min:0',
            'duration_months' => 'required|integer|min:1|max:60',
            'level' => 'required|in:beginner,intermediate,advanced',
            'category' => 'required|string',
            'max_students' => 'nullable|integer|min:1',
            'passing_percentage' => 'nullable|numeric|min:0|max:100',
            'instructor_name' => 'nullable|string|max:255',
            'instructor_email' => 'nullable|email',
            'prerequisites' => 'nullable|string',
            'curriculum' => 'nullable|string',
            'learning_outcomes' => 'nullable|array',
            'tags' => 'nullable|array',
            'is_featured' => 'boolean',
            'status' => 'required|in:active,inactive,draft'
        ]);

        $course = Course::create($validated);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully!');
    }

    /**
     * Display the specified course
     */
    public function show(Course $course)
    {
        if (request()->ajax()) {
            return view('admin.courses.partials.quick-view', compact('course'))->render();
        }
        
        $course->load(['students', 'exams', 'certificates']);
        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified course
     */
    public function edit(Course $course): View
    {
        return view('admin.courses.edit', compact('course'));
    }

    /**
     * Update the specified course
     */
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses,code,' . $course->id,
            'description' => 'required|string',
            'fee' => 'required|numeric|min:0',
            'duration_months' => 'required|integer|min:1',
            'level' => 'nullable|in:beginner,intermediate,advanced',
            'category' => 'nullable|in:technology,business,design,marketing,other',
            'status' => 'required|in:active,inactive,draft'
        ]);

        $course->update($request->only([
            'name', 'code', 'description', 'fee', 'duration_months', 
            'level', 'category', 'status'
        ]));

        return redirect()->route('admin.courses.index')
                        ->with('success', 'Course updated successfully!');
    }


    /**
     * Remove the specified course
     */
    public function destroy(Course $course): JsonResponse
    {
        try {
            if (!$course->canBeDeleted()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete course with enrolled students or existing exams.'
                ], 422);
            }

            $course->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Course deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting course: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle course featured status
     */
    public function toggleFeatured(Course $course): JsonResponse
    {
        try {
            $course->update(['is_featured' => !$course->is_featured]);

            return response()->json([
                'success' => true,
                'message' => $course->is_featured ? 'Course marked as featured!' : 'Course removed from featured!',
                'is_featured' => $course->is_featured
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating course: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle course status
     */
    public function toggleStatus(Course $course): JsonResponse
    {
        try {
            $newStatus = $course->status === 'active' ? 'inactive' : 'active';
            $course->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => "Course status changed to {$newStatus}",
                'new_status' => $newStatus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle bulk actions
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete,feature,unfeature',
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:courses,id'
        ]);

        try {
            $courses = Course::whereIn('id', $request->ids);
            $count = $courses->count();
            
            switch ($request->action) {
                case 'activate':
                    $courses->update(['status' => 'active']);
                    $message = "{$count} courses activated successfully!";
                    break;
                case 'deactivate':
                    $courses->update(['status' => 'inactive']);
                    $message = "{$count} courses deactivated successfully!";
                    break;
                case 'feature':
                    $courses->update(['is_featured' => true]);
                    $message = "{$count} courses marked as featured!";
                    break;
                case 'unfeature':
                    $courses->update(['is_featured' => false]);
                    $message = "{$count} courses removed from featured!";
                    break;
                case 'delete':
                    // Check if courses can be deleted
                    $cannotDelete = $courses->get()->filter(function($course) {
                        return !$course->canBeDeleted();
                    });

                    if ($cannotDelete->count() > 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Some courses cannot be deleted because they have enrolled students or existing exams.'
                        ], 422);
                    }

                    $courses->delete();
                    $message = "{$count} courses deleted successfully!";
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error performing bulk action: ' . $e->getMessage()
            ], 500);
        }
    }

        /**
     * Get students for a specific course
     */
    public function getStudents(Course $course): JsonResponse
    {
        $students = $course->students()->with(['payments', 'certificates'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $students,
            'total' => $students->count()
        ]);
    }

    /**
     * Get courses by category
     */
    public function byCategory(string $category)
    {
        $courses = Course::where('category', $category)
                        ->with(['students'])
                        ->withCount('students')
                        ->get();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $courses
            ]);
        }

        return view('admin.courses.by-category', compact('courses', 'category'));
    }

    /**
     * Get courses by level
     */
    public function byLevel(string $level)
    {
        $courses = Course::where('level', $level)
                        ->with(['students'])
                        ->withCount('students')
                        ->get();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $courses
            ]);
        }

        return view('admin.courses.by-level', compact('courses', 'level'));
    }

    /**
     * Get featured courses
     */
    public function featured()
    {
        $courses = Course::featured()
                        ->with(['students'])
                        ->withCount('students')
                        ->get();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $courses
            ]);
        }

        return view('admin.courses.featured', compact('courses'));
    }

    /**
     * Get popular courses
     */
    public function popular()
    {
        $courses = Course::popular()
                        ->with(['students'])
                        ->take(20)
                        ->get();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $courses
            ]);
        }

        return view('admin.courses.popular', compact('courses'));
    }

    /**
     * Search courses
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        $courses = Course::search($query)
                        ->with(['students'])
                        ->withCount('students')
                        ->limit(20)
                        ->get();

        return response()->json([
            'success' => true,
            'data' => $courses,
            'query' => $query
        ]);
    }

    /**
     * Duplicate a course
     */
    public function duplicate(Course $course): JsonResponse
    {
        try {
            $newCourse = $course->replicate();
            $newCourse->name = $course->name . ' (Copy)';
            $newCourse->code = Course::generateCourseCode($newCourse->name);
            $newCourse->status = 'draft';
            $newCourse->is_featured = false;
            $newCourse->save();

            return response()->json([
                'success' => true,
                'message' => 'Course duplicated successfully!',
                'course' => $newCourse,
                'redirect_url' => route('admin.courses.edit', $newCourse)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error duplicating course: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export courses data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        $courses = Course::with(['students'])->withCount('students')->get();
        
        switch ($format) {
            case 'excel':
                // Implement Excel export
                return response()->json(['success' => true, 'message' => 'Excel export started']);
            case 'csv':
                // Implement CSV export
                return response()->json(['success' => true, 'message' => 'CSV export started']);
            case 'pdf':
                // Implement PDF export
                return response()->json(['success' => true, 'message' => 'PDF export started']);
            default:
                return response()->json(['success' => false, 'message' => 'Invalid export format']);
        }
    }

    /**
     * Course analytics
     */
    public function analytics(Course $course): JsonResponse
    {
        try {
            $analytics = [
                'enrollment_stats' => [
                    'total_enrolled' => $course->students()->count(),
                    'active_students' => $course->students()->where('status', 'active')->count(),
                    'graduated_students' => $course->students()->where('status', 'graduated')->count(),
                    'completion_rate' => $course->getCompletionRate(),
                ],
                'financial_stats' => [
                    'total_revenue' => $course->getTotalRevenue(),
                    'average_payment' => $course->payments()->avg('amount') ?: 0,
                    'pending_payments' => $course->payments()->where('status', 'pending')->sum('amount'),
                ],
                'performance_stats' => [
                    'average_exam_score' => $course->getAverageExamScore(),
                    'certificates_issued' => $course->certificates()->count(),
                    'pass_rate' => $course->getPassRate(),
                ],
                'timeline_data' => $course->getEnrollmentTimeline(),
            ];

            return response()->json([
                'success' => true,
                'data' => $analytics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching analytics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign instructor to course
     */
    public function assignInstructor(Request $request, Course $course): JsonResponse
    {
        $request->validate([
            'instructor_name' => 'required|string|max:255',
            'instructor_email' => 'required|email'
        ]);

        try {
            $course->update([
                'instructor_name' => $request->instructor_name,
                'instructor_email' => $request->instructor_email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Instructor assigned successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning instructor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update course pricing
     */
    public function updatePricing(Request $request, Course $course): JsonResponse
    {
        $request->validate([
            'fee' => 'required|numeric|min:0'
        ]);

        try {
            $oldFee = $course->fee;
            $course->update(['fee' => $request->fee]);

            return response()->json([
                'success' => true,
                'message' => "Course fee updated from ₹{$oldFee} to ₹{$request->fee}",
                'old_fee' => $oldFee,
                'new_fee' => $request->fee
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating pricing: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send announcement to course students
     */
    public function sendAnnouncement(Request $request, Course $course): JsonResponse
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'send_via' => 'required|in:email,sms,both'
        ]);

        try {
            $studentsCount = $course->students()->where('status', 'active')->count();
            
            // Implement your notification logic here
            // This could use Laravel's notification system
            
            return response()->json([
                'success' => true,
                'message' => "Announcement sent to {$studentsCount} students successfully!",
                'students_notified' => $studentsCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending announcement: ' . $e->getMessage()
            ], 500);
        }
    }

}
