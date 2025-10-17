<?php
namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Course;

class StudentController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user->franchise_id) {
            abort(403, 'No franchise assigned');
        }

        $students = Student::where('franchise_id', $user->franchise_id)
                          ->with(['course'])
                          ->latest()
                          ->paginate(15);

        $stats = [
            'total' => Student::where('franchise_id', $user->franchise_id)->count(),
            'active' => Student::where('franchise_id', $user->franchise_id)->where('status', 'active')->count(),
            'graduated' => Student::where('franchise_id', $user->franchise_id)->where('status', 'graduated')->count(),
            'dropped' => Student::where('franchise_id', $user->franchise_id)->where('status', 'dropped')->count(),
        ];

        return view('franchise.students.index', compact('students', 'stats'));
    }

    public function show(Student $student)
    {
        // Ensure student belongs to this franchise
        if ($student->franchise_id !== Auth::user()->franchise_id) {
            abort(403, 'Unauthorized access to this student');
        }

        $student->load(['course', 'payments', 'certificates', 'examAttempts']);

        return view('franchise.students.show', compact('student'));
    }

    public function create()
    {
        $courses = Course::where('status', 'active')->get();

        return view('franchise.students.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->franchise_id) {
            return redirect()->back()->with('error', 'No franchise assigned');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'phone' => 'required|string|max:15',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:15',
            'course_id' => 'nullable|exists:courses,id',
            'batch' => 'nullable|string|max:100',
            'enrollment_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,graduated,dropped,suspended',
            'notes' => 'nullable|string',
        ]);

        // Auto-assign franchise
        $validated['franchise_id'] = $user->franchise_id;
        $validated['enrollment_date'] = $validated['enrollment_date'] ?? now();

        $student = Student::create($validated);

        return redirect()->route('franchise.students.index')
            ->with('success', 'Student created successfully! Student ID: ' . $student->student_id);
    }

    public function edit(Student $student)
    {
        // Ensure student belongs to this franchise
        if ($student->franchise_id !== Auth::user()->franchise_id) {
            abort(403, 'Unauthorized access to this student');
        }

        $courses = Course::where('status', 'active')->get();

        return view('franchise.students.edit', compact('student', 'courses'));
    }

    public function update(Request $request, Student $student)
    {
        // Ensure student belongs to this franchise
        if ($student->franchise_id !== Auth::user()->franchise_id) {
            abort(403, 'Unauthorized access to this student');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'phone' => 'required|string|max:15',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:15',
            'course_id' => 'nullable|exists:courses,id',
            'batch' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive,graduated,dropped,suspended',
            'notes' => 'nullable|string',
        ]);

        $student->update($validated);

        return redirect()->route('franchise.students.index')
            ->with('success', 'Student updated successfully!');
    }

    public function destroy(Student $student)
    {
        // Ensure student belongs to this franchise
        if ($student->franchise_id !== Auth::user()->franchise_id) {
            abort(403, 'Unauthorized access to this student');
        }

        $student->delete();

        return redirect()->route('franchise.students.index')
            ->with('success', 'Student deleted successfully!');
    }

    public function toggleStatus(Student $student)
    {
        if ($student->franchise_id !== Auth::user()->franchise_id) {
            abort(403, 'Unauthorized access to this student');
        }

        $newStatus = $student->status === 'active' ? 'inactive' : 'active';
        $student->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => 'Student status updated successfully!'
        ]);
    }
}
