{{-- resources/views/admin/dashboard.blade.php - SAFE VERSION --}}
@extends('layouts.custom-admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 15px;">
                <div class="card-body text-white p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-2">Welcome back, {{ Auth::user()->name ?? 'Administrator' }}! 👋</h3>
                            <p class="mb-0 opacity-90">Here's what's happening with your education management system today.</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="dashboard-icon" style="font-size: 4rem; opacity: 0.3;">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 rounded-circle" style="background: linear-gradient(45deg, #28a745, #20c997);">
                                <i class="fas fa-users text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Total Students</div>
                            <div class="h4 mb-0">{{ App\Models\Student::count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 rounded-circle" style="background: linear-gradient(45deg, #007bff, #6610f2);">
                                <i class="fas fa-book text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Total Courses</div>
                            <div class="h4 mb-0">{{ App\Models\Course::count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 rounded-circle" style="background: linear-gradient(45deg, #ffc107, #fd7e14);">
                                <i class="fas fa-building text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Franchises</div>
                            <div class="h4 mb-0">{{ App\Models\Franchise::count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 rounded-circle" style="background: linear-gradient(45deg, #dc3545, #e83e8c);">
                                <i class="fas fa-certificate text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Certificates</div>
                            <div class="h4 mb-0">{{ App\Models\Certificate::count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt text-warning me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.students.create') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
                                <i class="fas fa-user-plus fa-2x mb-2"></i>
                                <span>Add New Student</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.courses.create') }}" class="btn btn-outline-success w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
                                <i class="fas fa-plus fa-2x mb-2"></i>
                                <span>Create Course</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.franchises.create') }}" class="btn btn-outline-warning w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
                                <i class="fas fa-building fa-2x mb-2"></i>
                                <span>Add Franchise</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
                                <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                <span>View Reports</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-graduate text-primary me-2"></i>Recent Students
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $recentStudents = App\Models\Student::latest()->take(5)->get();
                    @endphp
                    @forelse($recentStudents as $student)
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-sm me-3" style="width: 40px; height: 40px; background: linear-gradient(45deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px;">
                                {{ substr($student->name, 0, 1) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-medium">{{ $student->name }}</div>
                                <div class="text-muted small">{{ $student->student_id }} • {{ $student->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">No students yet</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-book text-success me-2"></i>Active Courses
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $activeCourses = App\Models\Course::where('status', 'active')->latest()->take(5)->get();
                    @endphp
                    @forelse($activeCourses as $course)
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <div class="badge badge-{{ $course->status_badge }} px-2 py-1">{{ $course->code }}</div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-medium">{{ $course->name }}</div>
                                <div class="text-muted small">{{ $course->formatted_fee }} • {{ $course->duration_text }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">No active courses</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
<style>
    .avatar-sm {
        transition: all 0.3s ease;
    }

    .avatar-sm:hover {
        transform: scale(1.1);
    }
</style>
@endsection
