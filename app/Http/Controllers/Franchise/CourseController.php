<?php
// app/Http/Controllers/Franchise/CourseController.php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Student;
use App\Models\CourseEnrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::where('status', 'active')->get();
        return view('franchise.courses.index', compact('courses'));
    }

    public function show(Course $course)
    {
        $franchiseId = Auth::user()->franchise_id;

        // Get enrolled students for this course
        $enrollments = CourseEnrollment::with('student')
            ->where('course_id', $course->id)
            ->where('franchise_id', $franchiseId)
            ->latest('enrollment_date')
            ->get();

        return view('franchise.courses.show', compact('course', 'enrollments'));
    }

    // Show enrollment form
    public function enrollForm(Course $course)
    {
        $franchiseId = Auth::user()->franchise_id;

        // Get students not enrolled in this course
        $availableStudents = Student::where('franchise_id', $franchiseId)
            ->where('status', 'active')
            ->whereNotIn('id', function($query) use ($course) {
                $query->select('student_id')
                      ->from('course_enrollments')
                      ->where('course_id', $course->id);
            })
            ->orderBy('name')
            ->get();

        return view('franchise.courses.enroll', compact('course', 'availableStudents'));
    }

    // Enroll students in course
    public function enrollStudents(Request $request, Course $course)
    {
        $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'enrollment_date' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $franchiseId = Auth::user()->franchise_id;
            $enrolledCount = 0;

            foreach ($request->student_ids as $studentId) {
                // Check if student belongs to franchise
                $student = Student::where('id', $studentId)
                    ->where('franchise_id', $franchiseId)
                    ->first();

                if (!$student) continue;

                // Create enrollment
                CourseEnrollment::create([
                    'student_id' => $studentId,
                    'course_id' => $course->id,
                    'franchise_id' => $franchiseId,
                    'status' => 'enrolled',
                    'enrollment_date' => $request->enrollment_date,
                    'notes' => $request->notes
                ]);

                $enrolledCount++;
            }

            DB::commit();

            return redirect()
                ->route('franchise.courses.show', $course)
                ->with('success', "Successfully enrolled {$enrolledCount} student(s) in {$course->name}");

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Failed to enroll students: ' . $e->getMessage())
                ->withInput();
        }
    }
}
