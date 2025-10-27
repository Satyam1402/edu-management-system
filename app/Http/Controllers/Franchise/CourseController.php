<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        // If you want to show only courses that the franchise is allowed to see, filter here.
        $courses = Course::query()->get();
        // Replace with a filter if needed, e.g., if courses are assigned to franchise
        
        return view('franchise.courses.index', compact('courses'));
    }

    public function show(Course $course)
    {
        // Optional: authorize if you have franchise-specific limits
        
        return view('franchise.courses.show', compact('course'));
    }
}
