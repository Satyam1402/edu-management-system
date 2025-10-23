<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Course;
use App\Models\Franchise;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;

class StudentController extends Controller
{
    /**
     * Display a listing of students
     */
    public function index(Request $request)
    {
        // Handle AJAX request for DataTables
        if ($request->ajax()) {
            $query = Student::with(['franchise', 'course'])
                           ->select('students.*');

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('franchise')) {
                $query->where('franchise_id', $request->franchise);
            }

            if ($request->filled('course')) {
                $query->where('course_id', $request->course);
            }

            if ($request->filled('date_range')) {
                $this->applyDateRangeFilter($query, $request->date_range);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($student) {
                    return '<div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input student-checkbox" id="student_'.$student->id.'" value="'.$student->id.'">
                                <label class="custom-control-label" for="student_'.$student->id.'"></label>
                            </div>';
                })
                ->addColumn('student_details', function ($student) {
                    $genderIcon = match($student->gender) {
                        'male' => '👨',
                        'female' => '👩',
                        default => '🧑'
                    };
                    
                    return '<div class="d-flex align-items-center">
                                <div class="student-avatar mr-3">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-1 font-weight-bold">' . $student->name . '</h6>
                                    <small class="text-muted">' . $genderIcon . ' ' . ($student->age ? $student->age . ' years' : 'Age N/A') . '</small>
                                </div>
                            </div>';
                })
                ->addColumn('contact_info', function ($student) {
                    return '<div>
                                <div class="mb-1">
                                    <i class="fas fa-envelope fa-xs text-muted mr-1"></i>
                                    <small>' . $student->email . '</small>
                                </div>
                                <div>
                                    <i class="fas fa-phone fa-xs text-muted mr-1"></i>
                                    <small>' . $student->phone . '</small>
                                </div>
                            </div>';
                })
                ->addColumn('location_info', function ($student) {
                    return '<div>
                                <div class="font-weight-bold">' . ($student->city ?: 'N/A') . '</div>
                                <small class="text-muted">' . ($student->state ?: '') . ($student->pincode ? ', ' . $student->pincode : '') . '</small>
                            </div>';
                })
                ->addColumn('academic_info', function ($student) {
                    return '<div>
                                <div class="mb-1">
                                    <i class="fas fa-building fa-xs text-primary mr-1"></i>
                                    <small class="font-weight-bold">' . ($student->franchise->name ?? 'Not assigned') . '</small>
                                </div>
                                <div>
                                    <i class="fas fa-book fa-xs text-success mr-1"></i>
                                    <small>' . ($student->course->name ?? 'No course') . '</small>
                                </div>
                            </div>';
                })
                ->addColumn('status_badge', function ($student) {
                    $statusColors = [
                        'active' => 'success',
                        'inactive' => 'secondary',
                        'graduated' => 'info',
                        'dropped' => 'danger',
                        'suspended' => 'warning'
                    ];
                    $color = $statusColors[$student->status] ?? 'secondary';
                    return '<span class="badge badge-' . $color . ' px-3 py-2">' . ucfirst($student->status) . '</span>';
                })
                ->addColumn('enrollment_info', function ($student) {
                    $enrollmentDate = $student->enrollment_date ? $student->enrollment_date->format('M d, Y') : 'N/A';
                    $daysSince = $student->enrollment_date ? $student->enrollment_date->diffForHumans() : '';
                    
                    return '<div>
                                <div class="font-weight-bold">' . $enrollmentDate . '</div>
                                <small class="text-muted">' . $daysSince . '</small>
                            </div>';
                })
                ->addColumn('actions', function ($student) {
                    return '<div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-info btn-sm" onclick="quickView(' . $student->id . ')" title="Quick View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="' . route('admin.students.show', $student->id) . '" class="btn btn-outline-success btn-sm" title="View Details">
                                    <i class="fas fa-user"></i>
                                </a>
                                <a href="' . route('admin.students.edit', $student->id) . '" class="btn btn-outline-primary btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="' . route('admin.payments.create', ['student' => $student->id]) . '">
                                            <i class="fas fa-credit-card mr-2"></i>Add Payment
                                        </a>
                                        <a class="dropdown-item" href="' . route('admin.certificates.create', ['student' => $student->id]) . '">
                                            <i class="fas fa-certificate mr-2"></i>Issue Certificate
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger" href="#" onclick="deleteStudent(' . $student->id . ')">
                                            <i class="fas fa-trash mr-2"></i>Delete
                                        </a>
                                    </div>
                                </div>
                            </div>';
                })
                ->rawColumns(['checkbox', 'student_details', 'contact_info', 'location_info', 'academic_info', 'status_badge', 'enrollment_info', 'actions'])
                ->make(true);
        }

        // Return view for non-AJAX requests
        return view('admin.students.index');
    }

    /**
     * Apply date range filter
     */
    private function applyDateRangeFilter($query, $range)
    {
        switch ($range) {
            case 'today':
                $query->whereDate('enrollment_date', today());
                break;
            case 'week':
                $query->whereBetween('enrollment_date', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('enrollment_date', now()->month)
                      ->whereYear('enrollment_date', now()->year);
                break;
            case 'quarter':
                $query->whereBetween('enrollment_date', [now()->startOfQuarter(), now()->endOfQuarter()]);
                break;
            case 'year':
                $query->whereYear('enrollment_date', now()->year);
                break;
        }
    }

    /**
     * Show the form for creating a new student
     */
    public function create(): View
    {
        $franchises = Franchise::active()->get();
        $courses = Course::active()->get();
        
        return view('admin.students.create', compact('franchises', 'courses'));
    }

    /**
     * Store a newly created student
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'franchise_id' => 'required|exists:franchises,id',
            'course_id' => 'nullable|exists:courses,id',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'batch' => 'nullable|string|max:50',
            'notes' => 'nullable|string'
        ]);

        $student = Student::create($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student created successfully!');
    }

    /**
     * Display the specified student
     */
    public function show(Student $student)
    {
        // Handle AJAX request for quick view
        if (request()->ajax()) {
            return view('admin.students.partials.quick-view', compact('student'))->render();
        }
        
        $student->load(['franchise', 'course', 'payments', 'certificates']);
        return view('admin.students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified student
     */
    public function edit(Student $student): View
    {
        $franchises = Franchise::active()->get();
        $courses = Course::active()->get();
        
        return view('admin.students.edit', compact('student', 'franchises', 'courses'));
    }

    /**
     * Update the specified student
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'franchise_id' => 'required|exists:franchises,id',
            'course_id' => 'nullable|exists:courses,id',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'batch' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,graduated,dropped,suspended',
            'notes' => 'nullable|string'
        ]);

        $student->update($validated);

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Student updated successfully!');
    }

    /**
     * Remove the specified student
     */
    public function destroy(Student $student): JsonResponse
    {
        try {
            $student->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Student deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting student: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle bulk actions
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:students,id'
        ]);

        try {
            $students = Student::whereIn('id', $request->ids);
            $count = $students->count();
            
            switch ($request->action) {
                case 'activate':
                    $students->update(['status' => 'active']);
                    $message = "{$count} students activated successfully!";
                    break;
                case 'deactivate':
                    $students->update(['status' => 'inactive']);
                    $message = "{$count} students deactivated successfully!";
                    break;
                case 'delete':
                    $students->delete();
                    $message = "{$count} students deleted successfully!";
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
     * Export students data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        $students = Student::with(['franchise', 'course'])->get();
        
        // For now, return a simple response
        // You can implement actual export logic using Laravel Excel package
        
        return response()->json([
            'success' => true,
            'message' => "Export in {$format} format initiated",
            'data' => $students
        ]);
    }

    /**
     * Toggle student status
     */
    public function toggleStatus(Student $student): JsonResponse
    {
        try {
            $newStatus = $student->status === 'active' ? 'inactive' : 'active';
            $student->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => "Student status changed to {$newStatus}",
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
     * Get students by franchise
     */
    public function byFranchise(Franchise $franchise)
    {
        $students = $franchise->students()->with(['course'])->get();
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $students
            ]);
        }

        return view('admin.students.by-franchise', compact('franchise', 'students'));
    }

    /**
     * Search students
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        $students = Student::where('name', 'like', "%{$query}%")
                          ->orWhere('email', 'like', "%{$query}%")
                          ->orWhere('student_id', 'like', "%{$query}%")
                          ->orWhere('phone', 'like', "%{$query}%")
                          ->with(['franchise', 'course'])
                          ->limit(20)
                          ->get();

        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }
}
