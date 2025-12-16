<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $franchiseId = Auth::user()->franchise_id;

                $students = Student::where('franchise_id', $franchiseId)
                                  ->with(['course'])
                                  ->orderBy('created_at', 'desc')
                                  ->select('students.*');

                return DataTables::of($students)
                    ->addIndexColumn()
                    ->editColumn('student_id', function($student) {
                        return '<span class="badge badge-info font-weight-bold">' . $student->student_id . '</span>';
                    })
                    ->editColumn('name', function($student) {
                        return '<div>
                            <strong>' . htmlspecialchars($student->name) . '</strong><br>
                            <small class="text-muted">' . ($student->email ?? 'No email') . '</small>
                        </div>';
                    })
                    ->editColumn('email', function($student) {
                        return $student->email ?
                            '<a href="mailto:' . $student->email . '" class="text-primary">' . $student->email . '</a>' :
                            '<span class="text-muted">Not provided</span>';
                    })
                    ->editColumn('phone', function($student) {
                        return $student->phone ?
                            '<a href="tel:' . $student->phone . '" class="text-success">' . $student->phone . '</a>' :
                            '<span class="text-muted">Not provided</span>';
                    })
                    ->addColumn('course_name', function($student) {
                        if ($student->course) {
                            return '<span class="badge badge-primary">' . htmlspecialchars($student->course->name) . '</span>';
                        }
                        return '<span class="badge badge-secondary">Not Enrolled</span>';
                    })
                    ->editColumn('status_badge', function($student) {
                        $badges = [
                            'active' => '<span class="badge badge-success">Active</span>',
                            'inactive' => '<span class="badge badge-secondary">Inactive</span>',
                            'graduated' => '<span class="badge badge-primary">Graduated</span>',
                            'dropped' => '<span class="badge badge-warning">Dropped</span>',
                        ];
                        return $badges[$student->status] ?? '<span class="badge badge-secondary">Unknown</span>';
                    })
                    ->editColumn('enrollment_date', function($student) {
                        if ($student->enrollment_date) {
                            $date = \Carbon\Carbon::parse($student->enrollment_date);
                            return '<div class="text-center">
                                <strong>' . $date->format('M d, Y') . '</strong><br>
                                <small class="text-muted">' . $date->diffForHumans() . '</small>
                            </div>';
                        }
                        return '<div class="text-center">
                            <strong>' . $student->created_at->format('M d, Y') . '</strong><br>
                            <small class="text-muted">' . $student->created_at->diffForHumans() . '</small>
                        </div>';
                    })
                    ->addColumn('action', function($student) {
                        $actions = '
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="' . route('franchise.students.show', $student->id) . '"
                               class="btn btn-info btn-sm"
                               data-toggle="tooltip"
                               title="View Details">
                               <i class="fas fa-eye"></i>
                            </a>
                            <a href="' . route('franchise.students.edit', $student->id) . '"
                               class="btn btn-warning btn-sm"
                               data-toggle="tooltip"
                               title="Edit Student">
                               <i class="fas fa-edit"></i>
                            </a>';

                        // Only show delete if student has no certificates or payments
                        if (!$student->certificates()->exists() && !$student->payments()->exists()) {
                            $actions .= '
                            <button onclick="deleteStudent(' . $student->id . ')"
                                    class="btn btn-danger btn-sm"
                                    data-toggle="tooltip"
                                    title="Delete Student">
                                <i class="fas fa-trash"></i>
                            </button>';
                        }

                        $actions .= '</div>';
                        return $actions;
                    })
                    ->rawColumns(['student_id', 'name', 'email', 'phone', 'course_name', 'status_badge', 'enrollment_date', 'action'])
                    ->make(true);
            }

            return view('franchise.students.index');

        } catch (\Exception $e) {
            Log::error('Students Index Error: ' . $e->getMessage() . ' - Line: ' . $e->getLine());

            if ($request->ajax()) {
                return response()->json([
                    'draw' => $request->get('draw'),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'Unable to load students: ' . $e->getMessage()
                ], 500);
            }

            return view('franchise.students.index')->with('error', 'Unable to load students.');
        }
    }

    public function create()
    {
        try {
            $courses = Course::where('status', 'active')->get();

            return view('franchise.students.create', compact('courses'));

        } catch (\Exception $e) {
            Log::error('Student Create Error: ' . $e->getMessage());
            return redirect()->route('franchise.students.index')
                ->with('error', 'Unable to load create form.');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:students,email',
            'phone' => 'required|string|max:20',
            'course_id' => 'nullable|exists:courses,id',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date|before:today',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'enrollment_date' => 'required|date',
            'status' => 'required|in:active,inactive,graduated,dropped'
        ]);

        try {
            if ($validated['course_id']) {
                $course = Course::where('id', $validated['course_id'])
                              ->where('status', 'active')
                              ->first();

                if (!$course) {
                    return redirect()->back()
                        ->with('error', 'Invalid course selected.')
                        ->withInput();
                }
            }

            $validated['franchise_id'] = Auth::user()->franchise_id;
            $validated['student_id'] = $this->generateStudentId();
            $validated['password'] = Hash::make('password123'); // Default password

            $student = Student::create($validated);

            return redirect()->route('franchise.students.index')
                           ->with('success', 'Student "' . $student->name . '" added successfully!');

        } catch (\Exception $e) {
            Log::error('Student Store Error: ' . $e->getMessage() . ' - Data: ' . json_encode($validated));
            return redirect()->back()
                ->with('error', 'Failed to create student. Please try again.')
                ->withInput();
        }
    }

    public function show($id)
    {
        try {
            $franchiseId = Auth::user()->franchise_id;

            // Find student with proper franchise check
            $student = Student::where('id', $id)
                            ->where('franchise_id', $franchiseId)
                            ->with(['course', 'certificates', 'payments'])
                            ->first();

            if (!$student) {
                Log::warning('Student not found in show method', [
                    'student_id' => $id,
                    'franchise_id' => $franchiseId
                ]);

                return redirect()->route('franchise.students.index')
                    ->with('error', 'Student not found or access denied.');
            }
            // dd($student);
            return view('franchise.students.show', compact('student'));

        } catch (\Exception $e) {
            Log::error('Student Show Error: ' . $e->getMessage() . ' - Student ID: ' . $id);
            return redirect()->route('franchise.students.index')
                ->with('error', 'Student not found or access denied.');
        }
    }

    public function edit($id)
    {
        try {
            $franchiseId = Auth::user()->franchise_id;

            Log::info('Student Edit Debug:', [
                'student_id' => $id,
                'franchise_id' => $franchiseId,
                'user_id' => Auth::user()->id
            ]);

            // Find student with proper franchise check
            $student = Student::where('id', $id)
                            ->where('franchise_id', $franchiseId)
                            ->first();

            if (!$student) {
                Log::error('Student not found in edit method', [
                    'student_id' => $id,
                    'franchise_id' => $franchiseId
                ]);

                return redirect()->route('franchise.students.index')
                    ->with('error', "Student with ID {$id} not found in your franchise.");
            }

            // 🔧 FIXED: Get all active courses (courses are global, no franchise filtering)
            $courses = Course::where('status', 'active')->get();

            Log::info('Student Edit Success:', [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'courses_count' => $courses->count()
            ]);

            return view('franchise.students.edit', compact('student', 'courses'));

        } catch (\Exception $e) {
            Log::error('Student Edit Exception: ' . $e->getMessage() . ' - Student ID: ' . $id);
            return redirect()->route('franchise.students.index')
                ->with('error', 'Unable to edit student: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        Log::info('=== STUDENT UPDATE STARTED ===', [
            'student_id' => $id,
            'user_id' => Auth::user()->id,
            'franchise_id' => Auth::user()->franchise_id
        ]);

        try {
            $franchiseId = Auth::user()->franchise_id;

            // Find student with proper franchise check
            $student = Student::where('id', $id)
                            ->where('franchise_id', $franchiseId)
                            ->first();

            if (!$student) {
                Log::error('Student not found for update', [
                    'student_id' => $id,
                    'franchise_id' => $franchiseId
                ]);

                return redirect('/franchise/students')
                    ->with('error', 'Student not found or access denied.');
            }

            Log::info('Student found for update:', [
                'student_id' => $student->id,
                'student_name' => $student->name
            ]);

            // Validate the request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'last_name' => 'required|string|max:255',
                'father_name' => 'nullable|string|max:255',
                'mother_name' => 'nullable|string|max:255',
                'email' => 'required|email|unique:students,email,' . $student->id,
                'phone' => 'required|string|max:20',
                'course_id' => 'nullable|exists:courses,id',
                'gender' => 'nullable|in:male,female,other',
                'date_of_birth' => 'nullable|date|before:today',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'pincode' => 'nullable|string|max:10',
                'enrollment_date' => 'required|date',
                'status' => 'required|in:active,inactive,graduated,dropped'
            ]);

            if (empty($validated['date_of_birth']) || $validated['date_of_birth'] === '') {
                $validated['date_of_birth'] = null;
            }

            Log::info('Validation successful:', $validated);

            // Validate course exists and is active (if provided)
            if (!empty($validated['course_id'])) {
                $course = Course::where('id', $validated['course_id'])
                            ->where('status', 'active')
                            ->first();

                if (!$course) {
                    Log::error('Invalid course selected:', ['course_id' => $validated['course_id']]);

                    return redirect()->back()
                        ->with('error', 'Invalid course selected.')
                        ->withInput();
                }

                Log::info('Course validation passed:', [
                    'course_id' => $course->id,
                    'course_name' => $course->name
                ]);
            }

            // Update student
            $student->update($validated);

            Log::info('Student updated successfully:', [
                'student_id' => $student->id,
                'student_name' => $student->name
            ]);

            // Success message
            $successMessage = 'Student "' . $student->name . '" updated successfully!';

            Log::info('Attempting redirect to index', [
                'message' => $successMessage
            ]);

            // Use direct path redirect
            return redirect('/franchise/students')->with('success', $successMessage);

        } catch (\Exception $e) {
            Log::error('=== STUDENT UPDATE EXCEPTION ===', [
                'student_id' => $id,
                'exception_message' => $e->getMessage(),
                'exception_file' => $e->getFile(),
                'exception_line' => $e->getLine()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update student: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $franchiseId = Auth::user()->franchise_id;

            // Find student with proper franchise check
            $student = Student::where('id', $id)
                            ->where('franchise_id', $franchiseId)
                            ->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found or access denied.'
                ], 404);
            }

            // Check if student has any certificates or payments
            if ($student->certificates()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete student with existing certificates.'
                ], 400);
            }

            if ($student->payments()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete student with existing payment records.'
                ], 400);
            }

            $studentName = $student->name;
            $student->delete();

            return response()->json([
                'success' => true,
                'message' => 'Student "' . $studentName . '" deleted successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Student Delete Error: ' . $e->getMessage() . ' - Student ID: ' . $id);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete student. Please try again.'
            ], 500);
        }
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $franchiseId = Auth::user()->franchise_id;

            // Find student with proper franchise check
            $student = Student::where('id', $id)
                            ->where('franchise_id', $franchiseId)
                            ->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found or access denied.'
                ], 404);
            }

            $request->validate([
                'status' => 'required|in:active,inactive,graduated,dropped'
            ]);

            $student->status = $request->status;
            $student->save();

            return response()->json([
                'success' => true,
                'message' => 'Student status updated to "' . ucfirst($request->status) . '" successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Student Status Update Error: ' . $e->getMessage() . ' - Student ID: ' . $id);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update student status.'
            ], 500);
        }
    }

    public function getStats()
    {
        try {
            $franchiseId = Auth::user()->franchise_id;

            // Get all students for this franchise
            $totalStudents = Student::where('franchise_id', $franchiseId)->count();

            // Get active students
            $activeStudents = Student::where('franchise_id', $franchiseId)
                                    ->where('status', 'active')
                                    ->count();

            // Get graduated students
            $graduatedStudents = Student::where('franchise_id', $franchiseId)
                                    ->where('status', 'graduated')
                                    ->count();

            // Get students enrolled this month
            $thisMonthStudents = Student::where('franchise_id', $franchiseId)
                                    ->whereMonth('enrollment_date', now()->month)
                                    ->whereYear('enrollment_date', now()->year)
                                    ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_students' => $totalStudents,
                    'active_students' => $activeStudents,
                    'graduated_students' => $graduatedStudents,
                    'this_month' => $thisMonthStudents
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching student stats', [
                'error' => $e->getMessage(),
                'franchise_id' => Auth::user()->franchise_id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching statistics',
                'data' => [
                    'total_students' => 0,
                    'active_students' => 0,
                    'graduated_students' => 0,
                    'this_month' => 0
                ]
            ], 500);
        }
    }

    private function generateStudentId()
    {
        $prefix = 'STU';
        $franchiseCode = strtoupper(substr(Auth::user()->name ?? 'FR', 0, 3));

        do {
            $random = strtoupper(substr(uniqid(), -6));
            $studentId = $prefix . $franchiseCode . $random;
        } while (Student::where('student_id', $studentId)->exists());

        return $studentId;
    }
}
