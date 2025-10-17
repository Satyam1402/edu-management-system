<?php
// app/Http/Controllers/Admin/CourseController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of courses with pagination
     */
    public function index(Request $request)
    {
        $query = Course::query();

        // Add search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }

        // Add status filter
        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }

        $courses = $query->latest()->paginate(15);

        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course
     */
    public function create()
    {
        return view('admin.courses.create');
    }

    /**
     * Store a newly created course
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'required|string|max:20|unique:courses,code',
            'description' => 'nullable|string|max:500',
            'fee' => 'nullable|numeric|min:0',
            'duration_months' => 'nullable|integer|min:1|max:36',
            'status' => 'required|in:active,inactive'
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
        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing course
     */
    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    /**
     * Update the specified course
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'required|string|max:20|unique:courses,code,' . $course->id,
            'description' => 'nullable|string|max:500',
            'fee' => 'nullable|numeric|min:0',
            'duration_months' => 'nullable|integer|min:1|max:36',
            'status' => 'required|in:active,inactive'
        ]);

        $course->update($validated);

        return redirect()->route('admin.courses.show', $course)
                        ->with('success', 'Course updated successfully!');
    }

    /**
     * Remove the specified course
     */
    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('admin.courses.index')
                        ->with('success', 'Course deleted successfully!');
    }
}
