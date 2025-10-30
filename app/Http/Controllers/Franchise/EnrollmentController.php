<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EnrollmentController extends Controller
{
    /**
     * Enroll a student in a course
     */
public function store(Request $request)
{
    try {
        // Add logging to catch exact point of failure
        \Log::info('=== ENROLLMENT START ===');
        \Log::info('Request data: ', $request->all());

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'student_name' => 'required|string|max:255',
            'student_email' => 'required|email|unique:users,email',
            'student_phone' => 'nullable|string|max:20',
            'payment_method' => 'required|in:cash,card,bank_transfer,upi',
            'amount_paid' => 'nullable|numeric|min:0',
            'payment_status' => 'required|in:paid,pending,partial',
            'notes' => 'nullable|string|max:1000',
        ]);
        \Log::info('✅ Validation passed');

        $course = Course::findOrFail($request->course_id);
        \Log::info('✅ Course found: ' . $course->name);

        // Check if course is active
        if ($course->status !== 'active') {
            \Log::info('❌ Course not active: ' . $course->status);
            return response()->json(['error' => 'Course is not available for enrollment'], 400);
        }
        \Log::info('✅ Course status check passed');

        // Check enrollment limit - POTENTIAL ISSUE HERE
        if ($course->max_students && $course->students()->count() >= $course->max_students) {
            \Log::info('❌ Course full. Max: ' . $course->max_students . ', Current: ' . $course->students()->count());
            return response()->json(['error' => 'Course is full'], 400);
        }
        \Log::info('✅ Course capacity check passed');

        DB::beginTransaction();
        \Log::info('✅ Transaction started');

        // Create student user account
        $student = User::create([
            'name' => $request->student_name,
            'email' => $request->student_email,
            'phone' => $request->student_phone,
            'password' => Hash::make('student123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);
        \Log::info('✅ Student created: ID ' . $student->id);

        // Create enrollment record
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'franchise_id' => Auth::user()->franchise_id,
            'enrollment_date' => now(),
            'payment_method' => $request->payment_method,
            'amount_paid' => $course->is_free ? 0 : $request->amount_paid,
            'payment_status' => $course->is_free ? 'paid' : $request->payment_status,
            'status' => 'active',
            'notes' => $request->notes,
        ]);
        \Log::info('✅ Enrollment created: ID ' . $enrollment->id);

        DB::commit();
        \Log::info('✅ Transaction committed');

        return response()->json([
            'success' => true,
            'message' => 'Student enrolled successfully!',
            'student' => $student,
            'enrollment' => $enrollment
        ]);

    } catch (\Exception $e) {
        DB::rollback();
        \Log::error('❌ ENROLLMENT ERROR: ' . $e->getMessage());
        \Log::error('❌ STACK TRACE: ' . $e->getTraceAsString());
        return response()->json(['error' => 'Enrollment failed: ' . $e->getMessage()], 500);
    }
}


    /**
     * Show all students enrolled by this franchise
     */
    public function myStudents(Request $request)
    {
        $franchiseId = Auth::user()->franchise_id; // Make sure this is correct

        $query = User::whereHas('enrollments', function($q) use ($franchiseId) {
                      $q->where('franchise_id', $franchiseId);
                  })
                  ->with(['enrollments' => function($q) use ($franchiseId) {
                      $q->where('franchise_id', $franchiseId)
                        ->with('course');
                  }]);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by course
        if ($request->filled('course_id')) {
            $query->whereHas('enrollments', function($q) use ($request, $franchiseId) {
                $q->where('course_id', $request->course_id)
                  ->where('franchise_id', $franchiseId);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->whereHas('enrollments', function($q) use ($request, $franchiseId) {
                $q->where('status', $request->status)
                  ->where('franchise_id', $franchiseId);
            });
        }

        $students = $query->paginate(15);

        // Get courses for filter dropdown
        $courses = Course::whereHas('enrollments', function($q) use ($franchiseId) {
                         $q->where('franchise_id', $franchiseId);
                     })
                     ->orderBy('name')
                     ->get();

        // Statistics - these are calculated in the controller, not the view
        $stats = [
            'total_students' => $this->getTotalStudents(),
            'active_enrollments' => $this->getActiveEnrollments(),
            'completed_enrollments' => $this->getCompletedEnrollments(),
            'total_revenue' => $this->getTotalRevenue(),
        ];

        return view('franchise.enrollments.my-students', compact('students', 'courses', 'stats'));
    }

    /**
     * Show enrollment details
     */
    public function show(Enrollment $enrollment)
    {
        $franchiseId = Auth::user()->franchise_id;

        // Ensure this enrollment belongs to the franchise
        if ($enrollment->franchise_id !== $franchiseId) {
            abort(403, 'Unauthorized');
        }

        $enrollment->load(['student', 'course']);

        return view('franchise.enrollments.show', compact('enrollment'));
    }

    /**
     * Update enrollment status
     */
    public function updateStatus(Request $request, Enrollment $enrollment)
    {
        $request->validate([
            'status' => 'required|in:active,completed,cancelled,on_hold'
        ]);

        // Verify this enrollment belongs to the current franchise
        if ($enrollment->franchise_id !== Auth::user()->franchise_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $enrollment->update([
            'status' => $request->status,
            'completion_date' => $request->status === 'completed' ? now() : null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }

    /**
     * Generate certificate for student
     */
    public function generateCertificate(Enrollment $enrollment)
    {
        // Verify this enrollment belongs to the current franchise
        if ($enrollment->franchise_id !== Auth::user()->franchise_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mark certificate as issued
        $enrollment->update([
            'certificate_issued' => true,
            'status' => 'completed',
            'completion_date' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Certificate generated successfully'
        ]);
    }

    /**
     * Download certificate
     */
    public function downloadCertificate(Enrollment $enrollment)
    {
        // Verify this enrollment belongs to the current franchise
        if ($enrollment->franchise_id !== Auth::user()->franchise_id) {
            abort(403);
        }

        // Generate and return certificate PDF
        // Implementation depends on your PDF generation library
        return response()->json([
            'message' => 'Certificate download functionality will be implemented'
        ]);
    }

    /**
     * Remove enrollment
     */
    public function destroy(Enrollment $enrollment)
    {
        // Verify this enrollment belongs to the current franchise
        if ($enrollment->franchise_id !== Auth::user()->franchise_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $enrollment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Enrollment removed successfully'
        ]);
    }

    // =============================================================================
    // 🔧 HELPER METHODS (THESE WERE MISSING!)
    // =============================================================================

    /**
     * Get total students enrolled by this franchise
     */
    private function getTotalStudents()
    {
        $franchiseId = Auth::user()->franchise_id;
        return User::whereHas('enrollments', function($q) use ($franchiseId) {
                      $q->where('franchise_id', $franchiseId);
                  })->distinct()->count();
    }

    /**
     * Get active enrollments count
     */
    private function getActiveEnrollments()
    {
        $franchiseId = Auth::user()->franchise_id;
        return Enrollment::where('franchise_id', $franchiseId)
                         ->where('status', 'active')
                         ->count();
    }

    /**
     * Get completed enrollments count
     */
    private function getCompletedEnrollments()
    {
        $franchiseId = Auth::user()->franchise_id;
        return Enrollment::where('franchise_id', $franchiseId)
                         ->where('status', 'completed')
                         ->count();
    }

    /**
     * Get total revenue from enrollments
     */
    private function getTotalRevenue()
    {
        $franchiseId = Auth::user()->franchise_id;
        return Enrollment::where('franchise_id', $franchiseId)
                         ->where('payment_status', 'paid')
                         ->sum('amount_paid');
    }

    /**
     * Get pending enrollments count
     */
    private function getPendingEnrollments()
    {
        $franchiseId = Auth::user()->franchise_id;
        return Enrollment::where('franchise_id', $franchiseId)
                         ->where('status', 'on_hold')
                         ->count();
    }

    /**
     * Get cancelled enrollments count
     */
    private function getCancelledEnrollments()
    {
        $franchiseId = Auth::user()->franchise_id;
        return Enrollment::where('franchise_id', $franchiseId)
                         ->where('status', 'cancelled')
                         ->count();
    }

    /**
     * Get monthly revenue
     */
    private function getMonthlyRevenue($month = null, $year = null)
    {
        $franchiseId = Auth::user()->franchise_id;
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        return Enrollment::where('franchise_id', $franchiseId)
                         ->where('payment_status', 'paid')
                         ->whereMonth('created_at', $month)
                         ->whereYear('created_at', $year)
                         ->sum('amount_paid');
    }

    /**
     * Get enrollments by course
     */
    private function getEnrollmentsByCourse()
    {
        $franchiseId = Auth::user()->franchise_id;
        return Enrollment::where('franchise_id', $franchiseId)
                         ->with('course')
                         ->get()
                         ->groupBy('course.name')
                         ->map(function ($enrollments) {
                             return $enrollments->count();
                         });
    }
}
