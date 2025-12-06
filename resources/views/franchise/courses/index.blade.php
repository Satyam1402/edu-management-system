@extends('layouts.custom-admin')

@section('title', 'Course Catalog')
@section('page-title', 'Course Catalog')

@section('css')
<link rel="stylesheet" href="{{ asset('css/franchise/courses/index.css') }}">
<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.min.css">
<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 40px !important;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        padding-left: 12px;
        color: #495057;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
    }
    .select2-dropdown {
        border: 1px solid #ced4da;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .course-card .card-body {
        min-height: 280px;
    }
    .ribbon {
        position: absolute;
        top: -5px;
        left: -5px;
        z-index: 1;
        overflow: hidden;
        width: 75px;
        height: 75px;
        text-align: right;
    }
    .ribbon span {
        font-size: 10px;
        font-weight: bold;
        color: #FFF;
        text-transform: uppercase;
        text-align: center;
        line-height: 20px;
        transform: rotate(-45deg);
        width: 100px;
        display: block;
        background: linear-gradient(#F79E05 0%, #8F5408 100%);
        box-shadow: 0 3px 10px -5px rgba(0, 0, 0, 1);
        position: absolute;
        top: 19px;
        left: -21px;
    }
</style>
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
                    <a href="#" class="btn btn-primary disabled" style="opacity:0.6; cursor:default;">
                        <i class="fas fa-users mr-2"></i>My Students
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Available Courses</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_courses'] }}</div>
                                </div>
                                <div class="col-auto"><i class="fas fa-book fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">My Students</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['my_enrolled_students'] }}</div>
                                </div>
                                <div class="col-auto"><i class="fas fa-user-graduate fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Revenue</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($stats['total_revenue']) }}</div>
                                </div>
                                <div class="col-auto"><i class="fas fa-rupee-sign fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Active Enrollments</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_enrollments'] }}</div>
                                </div>
                                <div class="col-auto"><i class="fas fa-clipboard-check fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('franchise.courses.index') }}" class="form-row align-items-end">
                <div class="col-md-4 mb-3">
                    <label for="search" class="form-label">Search Courses</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}" placeholder="Search by name, code...">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Category</label>
                    <select class="form-control" name="category">
                        <option value="">All Categories</option>
                        <option value="technology" {{ request('category') == 'technology' ? 'selected' : '' }}>Technology</option>
                        <option value="business" {{ request('category') == 'business' ? 'selected' : '' }}>Business</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Level</label>
                    <select class="form-control" name="level">
                        <option value="">All Levels</option>
                        <option value="beginner" {{ request('level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="advanced" {{ request('level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Pricing</label>
                    <select class="form-control" name="pricing">
                        <option value="">All</option>
                        <option value="paid" {{ request('pricing') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="free" {{ request('pricing') == 'free' ? 'selected' : '' }}>Free</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-filter mr-2"></i>Filter</button>
                    @if(request()->anyFilled(['search', 'category', 'level', 'pricing']))
                        <a href="{{ route('franchise.courses.index') }}" class="btn btn-link btn-block btn-sm text-danger mt-1">Clear Filters</a>
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
                        <div class="ribbon ribbon-top-left"><span>Featured</span></div>
                    @endif

                    <div class="card-body d-flex flex-column">
                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-1 font-weight-bold text-dark" style="font-size: 1.1rem;">{{ $course->name }}</h5>
                                <span class="badge badge-light border">{{ $course->code }}</span>
                            </div>
                            <div class="text-right">
                                @if($course->is_free)
                                    <span class="badge badge-success px-2 py-1">FREE</span>
                                @else
                                    <h5 class="text-primary font-weight-bold mb-0">
                                        ₹{{ number_format($course->franchise_fee ?? $course->fee) }}
                                    </h5>
                                    <small class="text-muted" style="font-size: 0.7rem;">Franchise Fee</small>
                                @endif
                            </div>
                        </div>

                        <!-- Description -->
                        <p class="card-text flex-grow-1 text-muted small">{{ Str::limit($course->description, 90) }}</p>

                        <!-- Meta Stats -->
                        <div class="row text-sm mb-3 bg-light p-2 rounded mx-0 border-0">
                            <div class="col-6 mb-1 text-muted"><i class="fas fa-clock mr-1"></i> {{ $course->duration_months }} Months</div>
                            <div class="col-6 mb-1 text-muted"><i class="fas fa-layer-group mr-1"></i> {{ ucfirst($course->level) }}</div>
                            <div class="col-12 mt-1 text-primary font-weight-bold">
                                <i class="fas fa-users mr-1"></i> {{ $course->my_students_count ?? 0 }} Enrolled (My Center)
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-auto">
                            <div class="row no-gutters">
                                {{-- View Details Button --}}
                                <div class="col-4 pr-1">
                                    <a href="{{ route('franchise.courses.show', $course) }}"
                                       class="btn btn-outline-secondary btn-sm btn-block" title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </div>

                                {{-- Enroll Button --}}
                                <div class="col pl-1">
                                    <button class="btn btn-primary btn-sm btn-block enroll-student-btn"
                                            data-toggle="modal"
                                            data-target="#enrollStudentModal"
                                            data-course-id="{{ $course->id }}"
                                            data-course-name="{{ $course->name }}"
                                            data-course-fee="{{ $course->franchise_fee ?? $course->fee }}">
                                        <i class="fas fa-user-plus mr-1"></i> Enroll
                                    </button>
                                </div>

                                {{-- Manage Students Button --}}
                                @if($course->my_students_count > 0)
                                    <div class="col-auto pl-1">
                                        <a href="{{ route('franchise.courses.students', $course) }}"
                                           class="btn btn-success btn-sm"
                                           data-toggle="tooltip"
                                           title="Manage {{ $course->my_students_count }} Students">
                                            <i class="fas fa-users"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="alert alert-light border shadow-sm">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No courses found matching your criteria.</h4>
                    <a href="{{ route('franchise.courses.index') }}" class="btn btn-primary mt-2">Reset Filters</a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($courses->hasPages())
        <div class="d-flex justify-content-center mt-4">{{ $courses->links() }}</div>
    @endif
</div>

{{-- ============================================================================= --}}
{{-- 🚀 ENROLLMENT MODAL (SEARCH & SELECT STUDENT) --}}
{{-- ============================================================================= --}}
<div class="modal fade" id="enrollStudentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="enrollStudentForm" method="POST">
                @csrf

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-user-plus mr-2"></i>Enroll Student</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Course Info -->
                    <div class="alert alert-info shadow-sm border-0 mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted text-uppercase font-weight-bold">Course</small>
                                <div class="h5 mb-0 font-weight-bold" id="selectedCourseName"></div>
                            </div>
                            <div class="text-right">
                                <small class="text-muted text-uppercase font-weight-bold">Fee</small>
                                <div class="h5 mb-0 font-weight-bold text-success">₹<span id="selectedCourseFee"></span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Select2 Student Dropdown -->
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Select Student <span class="text-danger">*</span></label>

                        <select class="form-control select2" name="student_id" style="width: 100%;" required>
                            <option value="">-- Select a Student --</option>
                            @if(isset($myStudents) && $myStudents->count() > 0)
                                @foreach($myStudents as $student)
                                    <option value="{{ $student->id }}">
                                        {{ $student->name }} ({{ $student->email }})
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled>No active students available.</option>
                            @endif
                        </select>

                        <!-- Helper Link -->
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-muted">Type to search by name or email.</small>
                            <a href="{{ route('franchise.students.create') }}" class="small font-weight-bold text-primary">
                                <i class="fas fa-plus-circle"></i> Add New Student
                            </a>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Confirm Enrollment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 inside Modal
    $('.select2').select2({
        dropdownParent: $('#enrollStudentModal'),
        placeholder: 'Search student by name or email...',
        allowClear: true,
        width: '100%' // Ensures full width in modal
    });

    // Initialize Tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Handle Enroll Button Click
    $('.enroll-student-btn').on('click', function() {
        const courseId = $(this).data('course-id');
        const courseName = $(this).data('course-name');
        const courseFee = $(this).data('course-fee');

        // Update Modal Info
        $('#selectedCourseName').text(courseName);
        $('#selectedCourseFee').text(courseFee);

        // Update Form Action URL Dynamically
        // Ensure the route name matches your web.php
        let url = "{{ route('franchise.courses.enroll', ':id') }}";
        url = url.replace(':id', courseId);
        $('#enrollStudentForm').attr('action', url);

        // Reset Select2
        $('.select2').val(null).trigger('change');
    });
});
</script>
@endsection
