<?php
// app/Http/Controllers/Admin/ExamController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::with(['course'])->latest()->get();
        return view('admin.exams.index', compact('exams'));
    }

    public function create()
    {
        $courses = Course::where('status', 'active')->get();
        return view('admin.exams.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'exam_date' => 'required|date|after:today',
            'duration' => 'required|integer|min:30|max:300',
            'total_marks' => 'required|integer|min:10',
            'passing_marks' => 'required|integer|min:1',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
        ]);

        // Generate exam code
        $examCode = 'EX' . strtoupper(substr($request->title, 0, 4)) . date('Y');
        $validated['exam_code'] = $examCode;

        Exam::create($validated);

        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam created successfully!');
    }

    public function show(Exam $exam)
    {
        $exam->load(['course', 'results.student']);
        return view('admin.exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        $courses = Course::where('status', 'active')->get();
        return view('admin.exams.edit', compact('exam', 'courses'));
    }

    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'exam_date' => 'required|date',
            'duration' => 'required|integer|min:30|max:300',
            'total_marks' => 'required|integer|min:10',
            'passing_marks' => 'required|integer|min:1',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
        ]);

        $exam->update($validated);

        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam updated successfully!');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();
        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam deleted successfully!');
    }
}
