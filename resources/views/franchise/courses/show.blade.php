@extends('layouts.custom-admin')

@section('title', 'Course Details')
@section('page-title', 'Course Details')

@section('content')
<div class="container-fluid">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <!-- Course Info Card -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-book mr-2"></i>{{ $course->name }}</h5>
            <a href="{{ route('franchise.courses.enroll-form', $course) }}" class="btn btn-success btn-sm">
                <i class="fas fa-user-plus mr-1"></i> Enroll Students
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <p class="lead">{{ $course->description }}</p>
                    @if($course->duration)
                        <p><strong>Duration:</strong> {{ $course->duration }}</p>
                    @endif
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h3 class="text-primary">{{ $enrollments->count() }}</h3>
                            <p class="mb-0">Students Enrolled</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enrolled Students -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-users mr-2"></i>Enrolled Students</h5>
        </div>
        <div class="card-body">
            @if($enrollments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Enrollment Date</th>
                                <th>Status</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enrollments as $enrollment)
                                <tr>
                                    <td>
                                        <strong>{{ $enrollment->student->name }}</strong><br>
                                        <small class="text-muted">{{ $enrollment->student->email }}</small>
                                    </td>
                                    <td>{{ $enrollment->enrollment_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $enrollment->status_badge }}">
                                            {{ ucfirst($enrollment->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $enrollment->notes ?? 'No notes' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No students enrolled yet</p>
                    <a href="{{ route('franchise.courses.enroll-form', $course) }}" class="btn btn-primary">
                        <i class="fas fa-user-plus mr-1"></i> Enroll First Student
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
