{{-- resources/views/franchise/dashboard.blade.php - PROFESSIONAL DESIGN --}}
@extends('layouts.franchise-admin')

@section('title', 'Franchise Dashboard')
@section('page-title', 'Dashboard - ' . (Auth::user()->franchise->name ?? 'Franchise'))

@section('content')
    <!-- Professional Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none; border-radius: 15px; overflow: hidden;">
                <div class="card-body text-white p-4 position-relative">
                    <!-- Background Pattern -->
                    <div style="position: absolute; top: 0; right: 0; width: 200px; height: 200px; background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><circle cx=\"20\" cy=\"20\" r=\"2\" fill=\"white\" opacity=\"0.1\"/><circle cx=\"60\" cy=\"20\" r=\"2\" fill=\"white\" opacity=\"0.1\"/><circle cx=\"40\" cy=\"40\" r=\"2\" fill=\"white\" opacity=\"0.1\"/><circle cx=\"80\" cy=\"40\" r=\"2\" fill=\"white\" opacity=\"0.1\"/><circle cx=\"20\" cy=\"60\" r=\"2\" fill=\"white\" opacity=\"0.1\"/><circle cx=\"60\" cy=\"60\" r=\"2\" fill=\"white\" opacity=\"0.1\"/><circle cx=\"40\" cy=\"80\" r=\"2\" fill=\"white\" opacity=\"0.1\"/></svg>') repeat; opacity: 0.3;"></div>

                    <div class="row align-items-center position-relative">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-3">
                                <div class="mr-3">
                                    <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 15px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                                        <i class="fas fa-building" style="font-size: 24px; color: white;"></i>
                                    </div>
                                </div>
                                <div>
                                    <h2 class="mb-1 font-weight-bold">Welcome back, {{ Auth::user()->name }}! 👋</h2>
                                    <p class="mb-0 h5 font-weight-light opacity-90">{{ Auth::user()->franchise->name ?? 'Your Franchise' }}</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center text-white-50">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                <span>{{ Auth::user()->franchise->address ?? 'Location not specified' }}</span>
                            </div>
                        </div>
                        <div class="col-md-4 text-right d-none d-md-block">
                            <i class="fas fa-graduation-cap" style="font-size: 5rem; opacity: 0.2;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 class="font-weight-bold">{{ $stats['students'] }}</h3>
                    <p class="mb-0">My Students</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('franchise.students.index') }}" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 class="font-weight-bold">{{ $stats['active_students'] }}</h3>
                    <p class="mb-0">Active Students</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <a href="{{ route('franchise.students.index') }}?status=active" class="small-box-footer">
                    View Active <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 class="font-weight-bold">{{ $stats['courses'] }}</h3>
                    <p class="mb-0">Available Courses</p>
                </div>
                <div class="icon">
                    <i class="fas fa-book"></i>
                </div>
                <a href="{{ route('franchise.courses.index') }}" class="small-box-footer">
                    Browse Courses <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3 class="font-weight-bold">{{ $stats['certificates'] }}</h3>
                    <p class="mb-0">Certificates Issued</p>
                </div>
                <div class="icon">
                    <i class="fas fa-certificate"></i>
                </div>
                <a href="{{ route('franchise.certificates.index') }}" class="small-box-footer">
                    View Certificates <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Enhanced Recent Students Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-user-graduate mr-2 text-success"></i>Recent Students
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-success">{{ count($recent_students) }} students</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @forelse($recent_students as $student)
                        <div class="d-flex align-items-center p-3 border-bottom">
                            <div class="mr-3">
                                <div style="width: 45px; height: 45px; background: linear-gradient(45deg, #28a745, #20c997); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 16px; box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);">
                                    {{ substr($student->name, 0, 1) }}
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold text-dark">{{ $student->name }}</div>
                                <div class="text-muted small">
                                    <i class="fas fa-id-card mr-1"></i>{{ $student->student_id }}
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-clock mr-1"></i>{{ $student->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div>
                                <span class="badge badge-{{ $student->status == 'active' ? 'success' : ($student->status == 'graduated' ? 'primary' : 'secondary') }} px-3 py-2">
                                    {{ ucfirst($student->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-users fa-4x text-muted"></i>
                            </div>
                            <h4 class="text-muted font-weight-bold">No Students Yet</h4>
                            <p class="text-muted mb-4">Start building your student base by enrolling your first student.</p>
                            <a href="{{ route('franchise.students.create') }}" class="btn btn-success btn-lg rounded-pill">
                                <i class="fas fa-plus mr-2"></i>Add First Student
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
