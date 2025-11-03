@extends('layouts.custom-admin')

@section('title', 'My Students')
@section('page-title', 'My Students')

@section('content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $stats['total_students'] }}</h3>
                <p>Total Students</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['active_enrollments'] }}</h3>
                <p>Active Enrollments</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['completed_enrollments'] }}</h3>
                <p>Completed</p>
            </div>
            <div class="icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>₹{{ number_format($stats['total_revenue']) }}</h3>
                <p>Total Revenue</p>
            </div>
            <div class="icon">
                <i class="fas fa-rupee-sign"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Filter Students</h3>
    </div>
    <div class="card-body">
        <form method="GET" class="row">
            <div class="col-md-3">
                <label for="search">Search Students</label>
                <input type="text" class="form-control" name="search" id="search" value="{{ request('search') }}" placeholder="Search students...">
            </div>
            <div class="col-md-3">
                <label for="course_id">Course</label>
                <select class="form-control" name="course_id" id="course_id">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="status">Status</label>
                <select class="form-control" name="status" id="status">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-filter mr-1"></i>Filter
                    </button>
                </div>
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <div>
                    @if(request()->anyFilled(['search', 'course_id', 'status']))
                        <a href="{{ route('franchise.enrollments.my-students') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-times mr-1"></i>Clear
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Students Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Students List</h3>
        <div class="card-tools">
            <span class="badge badge-primary">{{ $students->total() }} total students</span>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Email</th>
                    <th>Courses</th>
                    <th>Status</th>
                    <th>Revenue</th>
                    <th>Enrolled Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-2" style="width:32px; height:32px; font-size: 14px;">
                                    {{ substr($student->name, 0, 1) }}
                                </div>
                                <div>
                                    <strong>{{ $student->name }}</strong>
                                </div>
                            </div>
                        </td>
                        <td>{{ $student->email }}</td>
                        <td>
                            @foreach($student->enrollments as $enrollment)
                                <span class="badge badge-info mb-1 d-block" style="font-size: 11px;">
                                    {{ $enrollment->course->name }}
                                </span>
                            @endforeach
                        </td>
                        <td>
                            @foreach($student->enrollments as $enrollment)
                                @php
                                    $badgeColor = match($enrollment->status) {
                                        'active' => 'success',
                                        'completed' => 'primary',
                                        'cancelled' => 'danger',
                                        'on_hold' => 'warning',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge badge-{{ $badgeColor }} mb-1 d-block" style="font-size: 11px;">
                                    {{ ucfirst($enrollment->status) }}
                                </span>
                            @endforeach
                        </td>
                        <td>
                            <strong>₹{{ number_format($student->enrollments->sum('amount_paid')) }}</strong>
                        </td>
                        <td>
                            @foreach($student->enrollments as $enrollment)
                                <small class="text-muted d-block">
                                    {{ $enrollment->enrollment_date ? $enrollment->enrollment_date->format('M d, Y') : 'N/A' }}
                                </small>
                            @endforeach
                        </td>
                        <td>
                            @foreach($student->enrollments as $enrollment)
                                <div class="mb-2">
                                    @php
                                        $certificateRequest = $enrollment->certificateRequest ?? null;
                                        $paymentCompleted = $enrollment->payment_status === 'completed' || ($enrollment->payment && $enrollment->payment->status === 'completed');
                                    @endphp

                                    @if($enrollment->status === 'completed')
                                        @if($certificateRequest && $certificateRequest->status !== 'rejected')
                                            <span class="badge badge-info">
                                                Certificate Requested
                                            </span>
                                        @elseif(!$paymentCompleted)
                                            <a href="{{ route('franchise.payments.create', ['student_id' => $student->id, 'enrollment_id' => $enrollment->id]) }}"
                                               class="btn btn-warning btn-sm"
                                               title="Complete Payment First">
                                               <i class="fas fa-credit-card"></i> Payment Required
                                            </a>
                                        @else
                                            <a href="{{ route('franchise.certificate-requests.create', ['student_id' => $student->id, 'enrollment_id' => $enrollment->id]) }}"
                                               class="btn btn-success btn-sm"
                                               title="Request Certificate">
                                               <i class="fas fa-certificate"></i> Request Certificate
                                            </a>
                                        @endif
                                    @else
                                        <span class="text-muted" style="font-size:11px;">No cert. eligible</span>
                                    @endif
                                </div>
                            @endforeach

                            <div class="btn-group" role="group">
                                <a href="#" class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-success" title="Send Message">
                                    <i class="fas fa-envelope"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-center">
                                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No students found</h5>
                                <p class="text-muted">You haven't enrolled any students yet.</p>
                                <a href="{{ route('franchise.courses.index') }}" class="btn btn-primary">
                                    <i class="fas fa-plus mr-1"></i>Browse Courses
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($students->hasPages())
        <div class="card-footer clearfix">
            <div class="float-right">
                {{ $students->appends(request()->query())->links() }}
            </div>
            <div class="float-left">
                <small class="text-muted">
                    Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students
                </small>
            </div>
        </div>
    @endif
</div>

<!-- Quick Actions Card -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('franchise.courses.index') }}" class="btn btn-primary btn-block mb-2">
                    <i class="fas fa-eye mr-2"></i>Browse Available Courses
                </a>
                <a href="{{ route('franchise.courses.revenue') }}" class="btn btn-success btn-block">
                    <i class="fas fa-chart-line mr-2"></i>View Revenue Report
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Summary</h3>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li><i class="fas fa-users text-primary mr-2"></i>Total Students: <strong>{{ $stats['total_students'] }}</strong></li>
                    <li><i class="fas fa-check-circle text-success mr-2"></i>Active Enrollments: <strong>{{ $stats['active_enrollments'] }}</strong></li>
                    <li><i class="fas fa-graduation-cap text-info mr-2"></i>Completed: <strong>{{ $stats['completed_enrollments'] }}</strong></li>
                    <li><i class="fas fa-rupee-sign text-warning mr-2"></i>Total Revenue: <strong>₹{{ number_format($stats['total_revenue']) }}</strong></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endsection
