{{-- resources/views/franchise/students/index.blade.php --}}
@extends('layouts.franchise-admin')

@section('title', 'My Students')
@section('page-title', 'My Students Management')

@section('content')
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3 text-right">
                            <div class="h4 mb-0">{{ $stats['total'] }}</div>
                            <div class="small">Total Students</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-check fa-2x opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3 text-right">
                            <div class="h4 mb-0">{{ $stats['active'] }}</div>
                            <div class="small">Active Students</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-graduation-cap fa-2x opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3 text-right">
                            <div class="h4 mb-0">{{ $stats['graduated'] }}</div>
                            <div class="small">Graduated</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-times fa-2x opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3 text-right">
                            <div class="h4 mb-0">{{ $stats['dropped'] }}</div>
                            <div class="small">Dropped</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Students List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users text-success me-2"></i>Students List
                    </h5>
                </div>
                <div class="col-auto">
                    <a href="{{ route('franchise.students.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Add New Student
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($students->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Status</th>
                                <th>Enrolled</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td><strong>{{ $student->student_id }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2" style="width: 32px; height: 32px; background: linear-gradient(45deg, #28a745, #20c997); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 12px;">
                                                {{ substr($student->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $student->name }}</div>
                                                <div class="text-muted small">{{ $student->phone }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $student->email }}</td>
                                    <td>
                                        @if($student->course)
                                            <span class="badge bg-info">{{ $student->course->name }}</span>
                                        @else
                                            <span class="text-muted">Not Assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $student->status_badge }}">
                                            {{ ucfirst($student->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $student->enrollment_date->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('franchise.students.show', $student) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('franchise.students.edit', $student) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students
                    </div>
                    {{ $students->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No Students Yet</h4>
                    <p class="text-muted">Start building your student base by adding your first student.</p>
                    <a href="{{ route('franchise.students.create') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-plus me-2"></i>Add First Student
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
