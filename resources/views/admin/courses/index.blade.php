{{-- resources/views/admin/courses/index.blade.php --}}
@extends('layouts.custom-admin')

@section('title', 'Courses')
@section('page-title', 'Course Management')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow" style="border: none; border-radius: 10px;">
                <div class="card-header" style="background: linear-gradient(45deg, #007bff, #0056b3); color: white; border-radius: 10px 10px 0 0;">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-book mr-2"></i> All Courses ({{ \App\Models\Course::count() }})
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.courses.create') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-plus mr-1"></i> Create New Course
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead style="background: #f8f9fa;">
                                <tr>
                                    <th class="pl-4">Course Details</th>
                                    <th>Course Code</th>
                                    <th>Duration</th>
                                    <th>Fee</th>
                                    <th>Students</th>
                                    <th>Status</th>
                                    <th class="pr-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(\App\Models\Course::withCount('students')->get() as $course)
                                <tr>
                                    <td class="pl-4">
                                        <div>
                                            <strong class="text-dark">{{ $course->name }}</strong><br>
                                            <small class="text-muted">{{ Str::limit($course->description ?? 'No description', 50) }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary px-3 py-1">{{ $course->code }}</span>
                                    </td>
                                    <td>
                                        @if($course->duration_months)
                                            <span class="text-info">{{ $course->duration_months }} months</span>
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($course->fee)
                                            <span class="text-success font-weight-bold">₹{{ number_format($course->fee) }}</span>
                                        @else
                                            <span class="text-muted">Free</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info px-2 py-1">{{ $course->students_count }} enrolled</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $course->status == 'active' ? 'success' : 'danger' }} px-3 py-1">
                                            {{ ucfirst($course->status) }}
                                        </span>
                                    </td>
                                    <td class="pr-4">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-info btn-sm" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-primary btn-sm" title="Manage Curriculum" onclick="manageCurriculum({{ $course->id }})">
                                                <i class="fas fa-list"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-book fa-3x mb-3"></i>
                                            <h5>No Courses Found</h5>
                                            <p>Create your first course to get started!</p>
                                            <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus mr-1"></i> Create Course
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function manageCurriculum(courseId) {
            // Implementation for curriculum management
            alert('Curriculum management feature coming soon!');
        }
    </script>
@endsection
