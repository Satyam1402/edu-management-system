<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    /**
     * Display students listing with DataTables
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $franchiseId = Auth::user()->franchise_id;
            
            $students = Student::where('franchise_id', $franchiseId)
                              ->with(['course'])
                              ->select('students.*');
            
            return DataTables::of($students)
                ->addIndexColumn()
                ->addColumn('course_name', function($row) {
                    return $row->course ? $row->course->name : '<span class="text-muted">Not Enrolled</span>';
                })
                ->addColumn('status_badge', function($row) {
                    $badges = [
                        'active' => '<span class="badge badge-success">Active</span>',
                        'inactive' => '<span class="badge badge-secondary">Inactive</span>',
                        'graduated' => '<span class="badge badge-info">Graduated</span>',
                        'dropped' => '<span class="badge badge-warning">Dropped</span>',
                    ];
                    return $badges[$row->status] ?? '<span class="badge badge-secondary">Unknown</span>';
                })
                ->addColumn('action', function($row) {
                    $btn = '<div class="btn-group btn-group-sm" role="group">';
                    $btn .= '<a href="'.route('franchise.students.show', $row->id).'" class="btn btn-info" title="View"><i class="fas fa-eye"></i></a>';
                    $btn .= '<a href="'.route('franchise.students.edit', $row->id).'" class="btn btn-primary" title="Edit"><i class="fas fa-edit"></i></a>';
                    $btn .= '<button type="button" class="btn btn-danger" onclick="deleteStudent('.$row->id.')" title="Delete"><i class="fas fa-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['course_name', 'status_badge', 'action'])
                ->make(true);
        }
        
        return view('franchise.students.index');
    }
    
    /**
     * Show form for creating new student
     */
    public function create()
    {
        $courses = Course::where('status', 'active')->get();
        return view('franchise.students.create', compact('courses'));
    }
    
    /**
     * Store new student
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'phone' => 'required|string|max:20',
            'course_id' => 'nullable|exists:courses,id',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'enrollment_date' => 'required|date',
            'status' => 'required|in:active,inactive,graduated,dropped'
        ]);
        
        // Add franchise ID from authenticated user
        $validated['franchise_id'] = Auth::user()->franchise_id;
        $validated['student_id'] = 'STU' . strtoupper(Str::random(8));
        
        Student::create($validated);
        
        return redirect()->route('franchise.students.index')
                       ->with('success', 'Student added successfully!');
    }
    
    /**
     * Display student details
     */
    public function show(Student $student)
    {
        // Verify student belongs to franchise
        $this->authorizeStudent($student);
        
        $student->load(['course', 'certificates', 'payments']);
        
        return view('franchise.students.show', compact('student'));
    }
    
    /**
     * Show form for editing student
     */
    public function edit(Student $student)
    {
        // Verify student belongs to franchise
        $this->authorizeStudent($student);
        
        $courses = Course::where('status', 'active')->get();
        
        return view('franchise.students.edit', compact('student', 'courses'));
    }
    
    /**
     * Update student
     */
    public function update(Request $request, Student $student)
    {
        // Verify student belongs to franchise
        $this->authorizeStudent($student);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'phone' => 'required|string|max:20',
            'course_id' => 'nullable|exists:courses,id',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'enrollment_date' => 'required|date',
            'status' => 'required|in:active,inactive,graduated,dropped'
        ]);
        
        $student->update($validated);
        
        return redirect()->route('franchise.students.index')
                       ->with('success', 'Student updated successfully!');
    }
    
    /**
     * Delete student
     */
    public function destroy(Student $student)
    {
        // Verify student belongs to franchise
        $this->authorizeStudent($student);
        
        $student->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Student deleted successfully!'
        ]);
    }
    
    /**
     * Toggle student status
     */
    public function toggleStatus(Request $request, Student $student)
    {
        // Verify student belongs to franchise
        $this->authorizeStudent($student);
        
        $student->status = $request->status;
        $student->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Student status updated successfully!'
        ]);
    }
    
    /**
     * Verify student belongs to current franchise
     */
    private function authorizeStudent(Student $student)
    {
        if ($student->franchise_id !== Auth::user()->franchise_id) {
            abort(403, 'Unauthorized access to this student.');
        }
    }
}
