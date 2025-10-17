<?php
// app/Http/Controllers/Admin/StudentController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Franchise;
use App\Models\Course;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with(['franchise', 'course'])->latest()->get();
        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        $franchises = Franchise::where('status', 'active')->get();
        $courses = Course::where('status', 'active')->get();
        return view('admin.students.create', compact('franchises', 'courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'phone' => 'required|string|max:15',
            'date_of_birth' => 'required|date',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'franchise_id' => 'required|exists:franchises,id',
            'course_id' => 'nullable|exists:courses,id',
            'status' => 'required|in:active,inactive',
        ]);

        // Generate student ID
        $lastStudent = Student::latest('id')->first();
        $studentId = 'STU' . str_pad(($lastStudent ? $lastStudent->id + 1 : 1), 6, '0', STR_PAD_LEFT);

        $validated['student_id'] = $studentId;
        $validated['enrollment_date'] = now();

        Student::create($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student created successfully!');
    }

    public function show(Student $student)
    {
        $student->load(['franchise', 'course', 'certificates', 'payments']);
        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $franchises = Franchise::where('status', 'active')->get();
        $courses = Course::where('status', 'active')->get();
        return view('admin.students.edit', compact('student', 'franchises', 'courses'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'phone' => 'required|string|max:15',
            'date_of_birth' => 'required|date',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'franchise_id' => 'required|exists:franchises,id',
            'course_id' => 'nullable|exists:courses,id',
            'status' => 'required|in:active,inactive',
        ]);

        $student->update($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully!');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully!');
    }
}
