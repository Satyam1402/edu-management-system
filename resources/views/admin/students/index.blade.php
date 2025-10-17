{{-- resources/views/admin/students/index.blade.php --}}
@extends('layouts.custom-admin')

@section('title', 'Students')
@section('page-title', 'Student Management')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow" style="border: none; border-radius: 10px;">
                <div class="card-header" style="background: linear-gradient(45deg, #28a745, #1e7e34); color: white; border-radius: 10px 10px 0 0;">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-users mr-2"></i> All Students ({{ \App\Models\Student::count() }})
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.students.create') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-user-plus mr-1"></i> Add New Student
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead style="background: #f8f9fa;">
                                <tr>
                                    <th class="pl-4">Student Details</th>
                                    <th>Student ID</th>
                                    <th>Franchise</th>
                                    <th>Course</th>
                                    <th>Status</th>
                                    <th class="pr-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(\App\Models\Student::with(['franchise', 'course'])->get() as $student)
                                <tr>
                                    <td class="pl-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar mr-3" style="width: 40px; height: 40px; background: linear-gradient(45deg, #007bff, #0056b3); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                                {{ substr($student->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <strong class="text-dark">{{ $student->name }}</strong><br>
                                                <small class="text-muted">{{ $student->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary px-3 py-1">{{ $student->student_id }}</span>
                                    </td>
                                    <td>
                                        @if($student->franchise)
                                            <span class="text-info">{{ $student->franchise->name }}</span>
                                        @else
                                            <span class="text-muted">Not Assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($student->course)
                                            <span class="text-success">{{ $student->course->name }}</span>
                                        @else
                                            <span class="text-muted">Not Enrolled</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $student->status == 'active' ? 'success' : 'danger' }} px-3 py-1">
                                            {{ ucfirst($student->status) }}
                                        </span>
                                    </td>
                                    <td class="pr-4">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.students.show', $student) }}" class="btn btn-info btn-sm" title="View Profile">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-success btn-sm" title="Enroll in Course" onclick="enrollStudent({{ $student->id }})">
                                                <i class="fas fa-graduation-cap"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-users fa-3x mb-3"></i>
                                            <h5>No Students Found</h5>
                                            <p>Start building your student database!</p>
                                            <a href="{{ route('admin.students.create') }}" class="btn btn-success">
                                                <i class="fas fa-user-plus mr-1"></i> Add First Student
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
        function enrollStudent(studentId) {
            // Implementation for course enrollment modal
            alert('Course enrollment feature coming soon!');
        }
    </script>
@endsection
