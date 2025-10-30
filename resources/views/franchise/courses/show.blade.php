@extends('layouts.custom-admin')


@section('title', 'Course Details - ' . $course->name)
@section('page-title', 'Course')

@section('css')
<link rel="stylesheet" href="{{ asset('css/franchise/courses/show.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header with Breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('franchise.courses.index') }}">
                            <i class="fas fa-graduation-cap mr-1"></i>Courses
                        </a>
                    </li>
                    <li class="breadcrumb-item active">{{ $course->name }}</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="mb-1">{{ $course->name }}</h2>
                    <p class="text-muted mb-0">Course Code: <strong>{{ $course->code }}</strong></p>
                </div>
                <div class="text-right">
                    @if($course->is_featured)
                        <span class="badge badge-warning px-3 py-2 mb-2">
                            <i class="fas fa-star mr-1"></i>Featured Course
                        </span><br>
                    @endif
                    <button class="btn btn-primary btn-lg" onclick="enrollStudent({{ $course->id }})">
                        <i class="fas fa-user-plus mr-2"></i>Enroll Student
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Overview Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-left-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Your Students
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $enrollmentStats['my_students'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-left-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Available Slots
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $enrollmentStats['available_slots'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chair fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-left-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Your Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($enrollmentStats['my_revenue']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-rupee-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-left-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Duration
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $course->duration_months }} Months</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Course Details (Left Column) -->
        <div class="col-lg-8">
            <!-- Course Description -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Course Description</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $course->description }}</p>
                </div>
            </div>

            <!-- Course Details Table -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list mr-2"></i>Course Details</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="font-weight-bold" width="30%">Course Code:</td>
                                    <td>{{ $course->code }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Duration:</td>
                                    <td>{{ $course->duration_months }} months</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Level:</td>
                                    <td>
                                        @if($course->level)
                                            <span class="badge badge-secondary">{{ ucfirst($course->level) }}</span>
                                        @else
                                            <span class="text-muted">All Levels</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Category:</td>
                                    <td>
                                        @if($course->category)
                                            <span class="badge badge-primary">{{ ucfirst($course->category) }}</span>
                                        @else
                                            <span class="text-muted">Not categorized</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Max Students:</td>
                                    <td>
                                        @if($course->max_students)
                                            {{ $course->max_students }} students
                                        @else
                                            <span class="text-success">Unlimited</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Status:</td>
                                    <td>
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle mr-1"></i>Active
                                        </span>
                                    </td>
                                </tr>
                                @if($course->fee_notes)
                                <tr>
                                    <td class="font-weight-bold">Fee Notes:</td>
                                    <td class="text-muted">{{ $course->fee_notes }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- My Enrolled Students -->
            @if($course->students->count() > 0)
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-users mr-2"></i>My Enrolled Students ({{ $course->students->count() }})</h5>
                    <a href="{{ route('franchise.courses.students', $course) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-eye mr-1"></i>View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Student Name</th>
                                    <th>Email</th>
                                    <th>Enrolled Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($course->students->take(5) as $student)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img class="rounded-circle mr-2" width="30" height="30"
                                                 src="{{ $student->avatar ?? 'https://via.placeholder.com/30' }}"
                                                 alt="{{ $student->name }}">
                                            <strong>{{ $student->name }}</strong>
                                        </div>
                                    </td>
                                    <td>{{ $student->email }}</td>
                                    <td>{{ $student->courseEnrollments->first()->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge badge-success">Active</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-outline-primary btn-sm"
                                                onclick="viewStudentProgress({{ $student->id }}, {{ $course->id }})">
                                            <i class="fas fa-chart-line"></i> Progress
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Pricing & Actions (Right Column) -->
        <div class="col-lg-4">
            <!-- Pricing Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-tag mr-2"></i>Course Pricing</h5>
                </div>
                <div class="card-body text-center">
                    @if($course->is_free)
                        <div class="display-4 text-success font-weight-bold mb-2">FREE</div>
                        <p class="text-muted mb-3">This course is completely free!</p>
                    @else
                        @if($course->franchise_fee)
                            <div class="mb-2">
                                <small class="text-muted d-block">Your Franchise Price:</small>
                                <div class="display-4 text-primary font-weight-bold">₹{{ number_format($course->franchise_fee) }}</div>
                            </div>
                            @if($course->fee > $course->franchise_fee)
                                <div class="text-success mb-2">
                                    <small>You save: ₹{{ number_format($course->fee - $course->franchise_fee) }}</small>
                                </div>
                            @endif
                        @elseif($course->discount_fee)
                            <div class="mb-2">
                                <small class="text-muted d-block">Discounted Price:</small>
                                <div class="display-4 text-success font-weight-bold">₹{{ number_format($course->discount_fee) }}</div>
                                <del class="text-muted">₹{{ number_format($course->fee) }}</del>
                                <div class="text-success">
                                    <small>{{ round((($course->fee - $course->discount_fee) / $course->fee) * 100) }}% OFF</small>
                                </div>
                            </div>
                        @else
                            <div class="display-4 text-primary font-weight-bold mb-2">₹{{ number_format($course->fee) }}</div>
                        @endif
                        <p class="text-muted mb-3">Per student enrollment</p>
                    @endif

                    <button class="btn btn-primary btn-lg btn-block" onclick="enrollStudent({{ $course->id }})">
                        <i class="fas fa-user-plus mr-2"></i>Enroll New Student
                    </button>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt mr-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    @if($course->students->count() > 0)
                        <a href="{{ route('franchise.courses.students', $course) }}" class="btn btn-outline-primary btn-block mb-2">
                            <i class="fas fa-users mr-2"></i>View All My Students
                        </a>
                    @endif
                    <button class="btn btn-outline-success btn-block mb-2" onclick="downloadCourseInfo({{ $course->id }})">
                        <i class="fas fa-download mr-2"></i>Download Course Info
                    </button>
                    <button class="btn btn-outline-info btn-block" onclick="shareCourse({{ $course->id }})">
                        <i class="fas fa-share-alt mr-2"></i>Share Course
                    </button>
                </div>
            </div>

            <!-- Course Progress (if students enrolled) -->
            @if($course->students->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie mr-2"></i>Enrollment Overview</h5>
                </div>
                <div class="card-body">
                    <div class="progress mb-3" style="height: 20px;">
                        @php
                            $enrollmentPercentage = $course->max_students
                                ? ($course->students()->count() / $course->max_students) * 100
                                : 50; // Default visualization for unlimited
                        @endphp
                        <div class="progress-bar bg-success" role="progressbar"
                             style="width: {{ min($enrollmentPercentage, 100) }}%">
                            {{ $course->students()->count() }} Students
                        </div>
                    </div>

                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border-right">
                                <div class="h6 mb-0 text-primary">{{ $enrollmentStats['my_students'] }}</div>
                                <small class="text-muted">Your Students</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-right">
                                <div class="h6 mb-0 text-success">{{ $course->students()->count() }}</div>
                                <small class="text-muted">Total Enrolled</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="h6 mb-0 text-info">{{ $enrollmentStats['available_slots'] }}</div>
                            <small class="text-muted">Available</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Student Enrollment Modal --}}
<div class="modal fade" id="enrollStudentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus mr-2"></i>Enroll Student in {{ $course->name }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="enrollmentForm" action="{{ route('franchise.enrollments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="course_id" value="{{ $course->id }}">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="student_name">Student Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="student_name" name="student_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="student_email">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="student_email" name="student_email" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="student_phone">Phone Number</label>
                                <input type="text" class="form-control" id="student_phone" name="student_phone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                                <select class="form-control" id="payment_method" name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="upi">UPI</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    @if(!$course->is_free)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="amount_paid">Amount Paid <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">₹</span>
                                    </div>
                                    <input type="number" step="0.01" class="form-control" id="amount_paid" name="amount_paid"
                                           value="{{ $course->franchise_fee ?? $course->discount_fee ?? $course->fee }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_status">Payment Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="payment_status" name="payment_status" required>
                                    <option value="paid">Paid</option>
                                    <option value="pending">Pending</option>
                                    <option value="partial">Partial</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="form-group">
                        <label for="notes">Additional Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"
                                  placeholder="Any additional information about this enrollment..."></textarea>
                    </div>

                    <!-- Pricing Summary -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle mr-2"></i>Enrollment Summary:</h6>
                        <div class="row">
                            <div class="col-sm-6">
                                <strong>Course:</strong> {{ $course->name }}<br>
                                <strong>Duration:</strong> {{ $course->duration_months }} months
                            </div>
                            <div class="col-sm-6">
                                @if($course->is_free)
                                    <strong class="text-success">Price: FREE</strong>
                                @else
                                    <strong>Your Price:
                                        ₹{{ number_format($course->franchise_fee ?? $course->discount_fee ?? $course->fee) }}
                                    </strong>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus mr-2"></i>Enroll Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
function enrollStudent(courseId) {
    $('#enrollStudentModal').modal('show');
}

function viewStudentProgress(studentId, courseId) {
    // Implement student progress tracking
    alert('Student progress tracking will be implemented next!');
}

function downloadCourseInfo(courseId) {
    window.open(`/franchise/courses/${courseId}/download-info`, '_blank');
}

function shareCourse(courseId) {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(function() {
        alert('Course URL copied to clipboard!');
    });
}

// Form submission handling
$('#enrollmentForm').on('submit', function(e) {
    e.preventDefault();

    // Show loading state
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Enrolling...');

    // Submit form via AJAX
    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            $('#enrollStudentModal').modal('hide');
            alert('Student enrolled successfully!');
            location.reload();
        },
        error: function(xhr) {
            alert('Error enrolling student. Please try again.');
        },
        complete: function() {
            submitBtn.prop('disabled', false).html(originalText);
        }
    });
});
</script>
@endsection
