@extends('layouts.custom-admin')

@section('title', $course->name)

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/courses/show.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <!-- Course Header -->
    <div class="course-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1>{{ $course->name }}</h1>
                <p class="lead">{{ $course->description }}</p>
                <div>
                    <span class="badge badge-light mr-2">{{ $course->code }}</span>
                    <span class="badge badge-{{ $course->status == 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($course->status) }}
                    </span>
                    @if($course->is_featured)
                        <span class="badge badge-warning ml-2">Featured</span>
                    @endif
                </div>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-light">
                    <i class="fas fa-edit mr-2"></i>Edit Course
                </a>
                <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-light ml-2">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">{{ $course->students()->count() }}</div>
                <div class="text-muted">Students</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">₹{{ number_format($course->fee) }}</div>
                <div class="text-muted">Fee</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">{{ $course->duration_months }}</div>
                <div class="text-muted">Months</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">{{ ucfirst($course->level ?? 'N/A') }}</div>
                <div class="text-muted">Level</div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-md-8">
            <!-- Course Info -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Course Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Category:</strong></td>
                            <td>{{ ucfirst($course->category ?? 'Not specified') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Level:</strong></td>
                            <td>{{ ucfirst($course->level ?? 'Not specified') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Duration:</strong></td>
                            <td>{{ $course->duration_months }} months</td>
                        </tr>
                        <tr>
                            <td><strong>Fee:</strong></td>
                            <td>₹{{ number_format($course->fee, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Max Students:</strong></td>
                            <td>{{ $course->max_students ?? 'Unlimited' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <span class="badge badge-{{ $course->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($course->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Created:</strong></td>
                            <td>{{ $course->created_at->format('M d, Y g:i A') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Updated:</strong></td>
                            <td>{{ $course->updated_at->format('M d, Y g:i A') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Students List -->
            @if($course->students()->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Enrolled Students ({{ $course->students()->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Enrolled</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($course->students()->take(10)->get() as $student)
                                <tr>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>
                                        <span class="badge badge-{{ $student->status == 'active' ? 'success' : 'secondary' }} badge-sm">
                                            {{ ucfirst($student->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $student->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($course->students()->count() > 10)
                        <p class="text-muted text-center">Showing 10 of {{ $course->students()->count() }} students</p>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-edit mr-2"></i>Edit Course
                    </a>
                    <button class="btn btn-warning btn-block mb-2" onclick="toggleFeatured({{ $course->id }})">
                        <i class="fas fa-star mr-2"></i>{{ $course->is_featured ? 'Remove Featured' : 'Mark Featured' }}
                    </button>
                    <button class="btn btn-danger btn-block" onclick="deleteCourse({{ $course->id }})">
                        <i class="fas fa-trash mr-2"></i>Delete Course
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('js/admin/courses/show.js') }}"></script>
@endsection
