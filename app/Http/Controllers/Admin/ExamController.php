<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $exams = Exam::with('course')->get();

            return DataTables::of($exams)
                ->addIndexColumn()
                ->addColumn('exam_details', function ($exam) {
                    return '<div>
                                <h6 class="mb-1 font-weight-bold">' . $exam->title . '</h6>
                                <small class="text-muted">' . ($exam->description ? substr($exam->description, 0, 50) . '...' : 'No description') . '</small>
                            </div>';
                })
                ->addColumn('course', function ($exam) {
                    return $exam->course ? '<span class="badge badge-info">' . $exam->course->name . '</span>' : 'N/A';
                })
                ->addColumn('exam_date', function ($exam) {
                    return $exam->exam_date ? date('M d, Y', strtotime($exam->exam_date)) : 'Not scheduled';
                })
                ->addColumn('duration', function ($exam) {
                    return $exam->duration_minutes . ' min';
                })
                ->addColumn('marks', function ($exam) {
                    return '<div class="text-center">' . $exam->total_marks . '</div>';
                })
                ->addColumn('status', function ($exam) {
                    $color = $exam->status === 'active' ? 'success' : 'secondary';
                    return '<span class="badge badge-' . $color . '">' . ucfirst($exam->status) . '</span>';
                })
                ->addColumn('actions', function ($exam) {
                    return '<div class="d-flex justify-content-center">
                                <a href="' . route('admin.exams.show', $exam) . '" class="btn btn-outline-info btn-sm mr-1" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="' . route('admin.exams.edit', $exam) . '" class="btn btn-outline-primary btn-sm mr-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-outline-danger btn-sm" onclick="deleteExam(' . $exam->id . ')" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>';
                })
                ->rawColumns(['exam_details', 'course', 'marks', 'status', 'actions'])
                ->make(true);
        }

        return view('admin.exams.index');
    }

    public function create()
    {
        $courses = Course::where('status', 'active')->get();
        return view('admin.exams.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'description' => 'nullable|string',
            'exam_date' => 'nullable|date',
            'start_time' => 'nullable',
            'duration_minutes' => 'required|integer|min:30',
            'total_marks' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive'
        ]);

        Exam::create($request->all());

        return redirect()->route('admin.exams.index')
                         ->with('success', 'Exam created successfully!');
    }

    public function show(Exam $exam)
    {
        $exam->load(['course', 'exam_attempts.student']);
        return view('admin.exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        $courses = Course::where('status', 'active')->get();
        return view('admin.exams.edit', compact('exam', 'courses'));
    }

    public function update(Request $request, Exam $exam)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'description' => 'nullable|string',
            'exam_date' => 'nullable|date',
            'start_time' => 'nullable',
            'duration_minutes' => 'required|integer|min:30',
            'total_marks' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive'
        ]);

        $exam->update($request->all());

        return redirect()->route('admin.exams.index')
                         ->with('success', 'Exam updated successfully!');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();

        return response()->json([
            'success' => true,
            'message' => 'Exam deleted successfully!'
        ]);
    }
}
