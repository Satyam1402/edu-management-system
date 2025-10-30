@extends('layouts.custom-admin')

@section('title', 'Course Catalog')
@section('page-title', 'Course Catalog')

@section('css')
<link rel="stylesheet" href="{{ asset('css/franchise/courses/index.css') }}">
<!-- SweetAlert2 for beautiful alerts -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.min.css">
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header with Stats Cards -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="fas fa-graduation-cap mr-2 text-primary"></i>Available Courses</h3>
                <div class="d-flex gap-3">
                    <a href="{{ route('franchise.courses.revenue') }}" class="btn btn-outline-success">
                        <i class="fas fa-chart-line mr-2"></i>Revenue Tracking
                    </a>
                    <a href="{{ route('franchise.enrollments.my-students') }}" class="btn btn-primary">
                        <i class="fas fa-users mr-2"></i>My Students
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-left-primary">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Available Courses
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_courses'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-book fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-left-success">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        My Students
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['my_enrolled_students'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-left-info">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Total Revenue
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($stats['total_revenue']) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-rupee-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-left-warning">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Active Enrollments
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_enrollments'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('franchise.courses.index') }}" class="form-row align-items-end">
                <div class="col-md-4 mb-3">
                    <label for="search" class="form-label">Search Courses</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}" placeholder="Search by name, code, or description...">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 mb-3">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-control" id="category" name="category">
                        <option value="">All Categories</option>
                        <option value="technology" {{ request('category') == 'technology' ? 'selected' : '' }}>Technology</option>
                        <option value="business" {{ request('category') == 'business' ? 'selected' : '' }}>Business</option>
                        <option value="design" {{ request('category') == 'design' ? 'selected' : '' }}>Design</option>
                        <option value="marketing" {{ request('category') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                        <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label for="level" class="form-label">Level</label>
                    <select class="form-control" id="level" name="level">
                        <option value="">All Levels</option>
                        <option value="beginner" {{ request('level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="intermediate" {{ request('level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="advanced" {{ request('level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label for="pricing" class="form-label">Pricing</label>
                    <select class="form-control" id="pricing" name="pricing">
                        <option value="">All Courses</option>
                        <option value="free" {{ request('pricing') == 'free' ? 'selected' : '' }}>Free</option>
                        <option value="paid" {{ request('pricing') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="franchise" {{ request('pricing') == 'franchise' ? 'selected' : '' }}>Franchise Pricing</option>
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    @if(request()->anyFilled(['search', 'category', 'level', 'pricing']))
                        <a href="{{ route('franchise.courses.index') }}" class="btn btn-outline-secondary btn-block mt-2">
                            <i class="fas fa-times mr-2"></i>Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Courses Grid -->
    <div class="row">
        @forelse($courses as $course)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm course-card">
                    @if($course->is_featured)
                        <div class="ribbon ribbon-top-left">
                            <span><i class="fas fa-star"></i> Featured</span>
                        </div>
                    @endif

                    <div class="card-body d-flex flex-column">
                        <!-- Course Header -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-1">{{ $course->name }}</h5>
                                <small class="text-muted">Code: {{ $course->code }}</small>
                            </div>
                            <div class="text-right">
                                @if($course->is_free)
                                    <span class="badge badge-success px-2 py-1">FREE</span>
                                @else
                                    <div class="text-primary font-weight-bold">
                                        @if($course->franchise_fee)
                                            <small class="text-muted">Franchise Price:</small><br>
                                            ₹{{ number_format($course->franchise_fee) }}
                                        @elseif($course->discount_fee)
                                            <small class="text-muted">Discounted:</small><br>
                                            ₹{{ number_format($course->discount_fee) }}
                                        @else
                                            ₹{{ number_format($course->fee) }}
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Course Description -->
                        <p class="card-text flex-grow-1">{{ Str::limit($course->description, 120) }}</p>

                        <!-- Course Meta Info -->
                        <div class="row text-sm mb-3">
                            <div class="col-6">
                                <i class="fas fa-clock text-muted mr-1"></i>
                                <span class="text-muted">{{ $course->duration_months }} months</span>
                            </div>
                            <div class="col-6">
                                <i class="fas fa-layer-group text-muted mr-1"></i>
                                <span class="text-muted">{{ ucfirst($course->level ?? 'All Levels') }}</span>
                            </div>
                            @if($course->category)
                            <div class="col-6 mt-1">
                                <i class="fas fa-tag text-muted mr-1"></i>
                                <span class="text-muted">{{ ucfirst($course->category) }}</span>
                            </div>
                            @endif
                            <div class="col-6 mt-1">
                                <i class="fas fa-users text-muted mr-1"></i>
                                <span class="text-muted">{{ $course->students->count() }} enrolled</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-auto">
                            <div class="row">
                                <div class="col-6">
                                    <a href="{{ route('franchise.courses.show', $course) }}"
                                       class="btn btn-outline-primary btn-sm btn-block">
                                        <i class="fas fa-eye mr-1"></i>View Details
                                    </a>
                                </div>
                                <div class="col-6">
                                    @if($course->students->count() > 0)
                                        <a href="{{ route('franchise.courses.students', $course) }}"
                                           class="btn btn-success btn-sm btn-block">
                                            <i class="fas fa-users mr-1"></i>My Students
                                        </a>
                                    @else
                                        <button class="btn btn-primary btn-sm btn-block enroll-student-btn"
                                                data-toggle="modal"
                                                data-target="#enrollStudentModal"
                                                data-course-id="{{ $course->id }}"
                                                data-course-name="{{ $course->name }}"
                                                data-course-fee="{{ $course->franchise_fee ?? $course->discount_fee ?? $course->fee ?? 0 }}">
                                            <i class="fas fa-user-plus mr-1"></i>Enroll Student
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No courses found</h4>
                    <p class="text-muted">Try adjusting your search criteria or check back later for new courses.</p>
                    @if(request()->anyFilled(['search', 'category', 'level', 'pricing']))
                        <a href="{{ route('franchise.courses.index') }}" class="btn btn-primary">
                            <i class="fas fa-refresh mr-2"></i>View All Courses
                        </a>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($courses->hasPages())
        <div class="d-flex justify-content-center">
            {{ $courses->appends(request()->query())->links() }}
        </div>
    @endif
</div>

{{-- ============================================================================= --}}
{{-- 🚀 COMPLETE ENROLLMENT MODAL WITH WORKING FORM --}}
{{-- ============================================================================= --}}
<div class="modal fade" id="enrollStudentModal" tabindex="-1" role="dialog" aria-labelledby="enrollStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="enrollStudentForm">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="enrollStudentModalLabel">
                        <i class="fas fa-user-plus mr-2"></i>Enroll New Student
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="courseId" name="course_id">

                    <div class="row">
                        <!-- Course Info Display -->
                        <div class="col-12 mb-3">
                            <div class="alert alert-info">
                                <strong>Course:</strong> <span id="selectedCourseName">-</span><br>
                                <strong>Fee:</strong> ₹<span id="selectedCourseFee">-</span>
                            </div>
                        </div>

                        <!-- Student Details -->
                        <div class="col-md-6 mb-3">
                            <label for="studentName" class="form-label">Student Name *</label>
                            <input type="text" class="form-control" id="studentName" name="student_name" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="studentEmail" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="studentEmail" name="student_email" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Payment Details -->
                        <div class="col-md-6 mb-3">
                            <label for="paymentMethod" class="form-label">Payment Method *</label>
                            <select class="form-control" id="paymentMethod" name="payment_method" required>
                                <option value="">Select Payment Method</option>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="upi">UPI</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="amountPaid" class="form-label">Amount Paid</label>
                            <input type="number" step="0.01" class="form-control" id="amountPaid" name="amount_paid" placeholder="Enter amount">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="paymentStatus" class="form-label">Payment Status *</label>
                            <select class="form-control" id="paymentStatus" name="payment_status" required>
                                <option value="">Select Status</option>
                                <option value="paid">Paid</option>
                                <option value="pending">Pending</option>
                                <option value="partial">Partial</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any additional notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="enrollButton">
                        <i class="fas fa-user-plus mr-2"></i>Enroll Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js"></script>

<script>
$(document).ready(function() {
    // Handle Enroll Student button clicks
    $('.enroll-student-btn').on('click', function() {
        const courseId = $(this).data('course-id');
        const courseName = $(this).data('course-name');
        const courseFee = $(this).data('course-fee');

        // Populate modal with course info
        $('#courseId').val(courseId);
        $('#selectedCourseName').text(courseName);
        $('#selectedCourseFee').text(courseFee);

        // Clear form
        $('#enrollStudentForm')[0].reset();
        $('#courseId').val(courseId); // Reset clears this too

        // Clear validation states
        clearValidationStates();
    });

    // Handle form submission
    $('#enrollStudentForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const enrollButton = $('#enrollButton');
        const originalText = enrollButton.html();

        // Show loading state
        enrollButton.prop('disabled', true);
        enrollButton.html('<i class="fas fa-spinner fa-spin mr-2"></i>Enrolling...');

        // Clear previous validation
        clearValidationStates();

        $.ajax({
            url: '{{ route("franchise.enrollments.store") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Success
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        // Close modal and refresh page
                        $('#enrollStudentModal').modal('hide');
                        location.reload();
                    });
                } else {
                    // Error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.error || 'Something went wrong',
                        confirmButtonColor: '#dc3545'
                    });
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);

                // Handle validation errors
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors || {};
                    showValidationErrors(errors);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.error || 'Something went wrong. Please try again.',
                        confirmButtonColor: '#dc3545'
                    });
                }
            },
            complete: function() {
                // Reset button
                enrollButton.prop('disabled', false);
                enrollButton.html(originalText);
            }
        });
    });

    function clearValidationStates() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    }

    function showValidationErrors(errors) {
        Object.keys(errors).forEach(function(field) {
            const input = $(`[name="${field}"]`);
            if (input.length) {
                input.addClass('is-invalid');
                const feedback = input.closest('.form-group, .mb-3').find('.invalid-feedback');
                if (feedback.length) {
                    feedback.text(errors[field][0]);
                }
            }
        });
    }
});

// Legacy function for backward compatibility
function enrollStudent(courseId) {
    console.log('Enroll student in course:', courseId);
    $('#enrollStudentModal').modal('show');
}
</script>
@endsection
