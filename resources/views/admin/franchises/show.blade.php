{{-- resources/views/admin/franchises/show.blade.php - FIXED UI --}}
@extends('layouts.custom-admin')

@section('title', $franchise->name)
@section('page-title', 'Franchise Details - ' . $franchise->name)

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/franchise-show.css') }}">
@endsection

@section('content')
    <!-- Enhanced Franchise Header -->
    <div class="card border-0 shadow-sm mb-4 franchise-header">
        <div class="card-body text-white p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-3">
                        <div class="franchise-avatar-lg">
                            {{ substr($franchise->name, 0, 1) }}
                        </div>
                        <div>
                            <h2 class="mb-1 font-weight-bold">{{ $franchise->name }}</h2>
                            <p class="mb-0 h5" style="opacity: 0.9;">
                                <i class="fas fa-hashtag mr-1"></i>{{ $franchise->code }}
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><i class="fas fa-envelope mr-2"></i>{{ $franchise->email }}</p>
                            <p class="mb-0"><i class="fas fa-phone mr-2"></i>{{ $franchise->phone }}</p>
                        </div>
                        <div class="col-md-6">
                            @if($franchise->city || $franchise->state)
                                <p class="mb-2"><i class="fas fa-map-marker-alt mr-2"></i>{{ $franchise->city }}{{ $franchise->city && $franchise->state ? ', ' : '' }}{{ $franchise->state }}</p>
                            @endif
                            <span class="badge badge-{{ $franchise->status_badge }} px-3 py-2" style="border-radius: 12px; font-size: 13px;">
                                <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>{{ ucfirst($franchise->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-right d-none d-md-block">
                    <div class="d-flex flex-column align-items-end">
                        <div class="mb-3">
                            <a href="{{ route('admin.franchises.edit', $franchise) }}" class="btn btn-light action-btn-lg mr-2">
                                <i class="fas fa-edit mr-1"></i> Edit Franchise
                            </a>
                            <button class="btn btn-outline-light action-btn-lg" onclick="createUser({{ $franchise->id }})">
                                <i class="fas fa-user-plus mr-1"></i> Add User
                            </button>
                        </div>
                        <div style="font-size: 4rem; opacity: 0.2;">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card h-100 text-center">
                <div class="card-body p-4">
                    <div class="text-primary mb-3">
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                    <h3 class="mb-1 font-weight-bold text-dark">{{ $stats['total_students'] }}</h3>
                    <p class="text-muted mb-0">Total Students</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card h-100 text-center">
                <div class="card-body p-4">
                    <div class="text-success mb-3">
                        <i class="fas fa-user-check fa-3x"></i>
                    </div>
                    <h3 class="mb-1 font-weight-bold text-dark">{{ $stats['active_students'] }}</h3>
                    <p class="text-muted mb-0">Active Students</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card h-100 text-center">
                <div class="card-body p-4">
                    <div class="text-info mb-3">
                        <i class="fas fa-user-tie fa-3x"></i>
                    </div>
                    <h3 class="mb-1 font-weight-bold text-dark">{{ $stats['total_users'] }}</h3>
                    <p class="text-muted mb-0">System Users</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card h-100 text-center">
                <div class="card-body p-4">
                    <div class="text-warning mb-3">
                        <i class="fas fa-calendar-check fa-3x"></i>
                    </div>
                    <h3 class="mb-1 font-weight-bold text-dark">{{ $franchise->created_at->diffInDays() }}</h3>
                    <p class="text-muted mb-0">Days Active</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Enhanced Franchise Details -->
        <div class="col-md-6 mb-4">
            <div class="card info-card h-100">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="card-title mb-0 font-weight-bold text-dark">
                        <i class="fas fa-info-circle text-primary mr-2"></i>Franchise Information
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="font-weight-medium text-muted" width="40%">Franchise Code:</td>
                            <td><span class="badge badge-primary px-3 py-2">{{ $franchise->code }}</span></td>
                        </tr>
                        <tr>
                            <td class="font-weight-medium text-muted">Contact Person:</td>
                            <td>{{ $franchise->contact_person ?: 'Not specified' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-medium text-muted">Email Address:</td>
                            <td>
                                <i class="fas fa-envelope text-primary mr-2"></i>
                                <a href="mailto:{{ $franchise->email }}" class="text-decoration-none">{{ $franchise->email }}</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-medium text-muted">Phone Number:</td>
                            <td>
                                <i class="fas fa-phone text-success mr-2"></i>
                                <a href="tel:{{ $franchise->phone }}" class="text-decoration-none">{{ $franchise->phone }}</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-medium text-muted">Full Address:</td>
                            <td>
                                <i class="fas fa-map-marker-alt text-danger mr-2"></i>
                                @if($franchise->address)
                                    {{ $franchise->address }}<br>
                                @endif
                                @if($franchise->city || $franchise->state)
                                    {{ $franchise->city }}{{ $franchise->city && $franchise->state ? ', ' : '' }}{{ $franchise->state }}
                                    @if($franchise->pincode) - {{ $franchise->pincode }} @endif
                                @else
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-medium text-muted">Established Date:</td>
                            <td>
                                <i class="fas fa-calendar text-info mr-2"></i>
                                {{ $franchise->established_date ? $franchise->established_date->format('M d, Y') : 'Not specified' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-medium text-muted">System Registration:</td>
                            <td>
                                <i class="fas fa-clock text-warning mr-2"></i>
                                {{ $franchise->created_at->format('M d, Y \a\t H:i') }}
                            </td>
                        </tr>
                    </table>

                    @if($franchise->notes)
                        <div class="mt-4 p-3" style="background: #f8f9fa; border-radius: 8px; border-left: 4px solid #007bff;">
                            <h6 class="font-weight-bold text-dark mb-2">
                                <i class="fas fa-sticky-note text-warning mr-2"></i>Additional Notes
                            </h6>
                            <p class="mb-0 text-muted">{{ $franchise->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Enhanced Franchise Users -->
        <div class="col-md-6 mb-4">
            <div class="card info-card h-100">
                <div class="card-header bg-white border-0 pt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 font-weight-bold text-dark">
                            <i class="fas fa-users text-success mr-2"></i>System Users 
                            <span class="badge badge-success ml-2">{{ $franchise->users->count() }}</span>
                        </h5>
                        <button class="btn btn-success btn-sm" onclick="createUser({{ $franchise->id }})" style="border-radius: 8px;">
                            <i class="fas fa-plus mr-1"></i> Add User
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($franchise->users->count() > 0)
                        @foreach($franchise->users as $user)
                            <div class="d-flex align-items-center mb-3 p-3 border rounded" style="border-radius: 10px !important; background: #f8f9fa;">
                                <div class="user-avatar-sm">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="font-weight-bold text-dark">{{ $user->name }}</div>
                                    <div class="text-muted small mb-1">
                                        <i class="fas fa-envelope mr-1"></i>{{ $user->email }}
                                    </div>
                                    <div class="text-muted small">
                                        <i class="fas fa-calendar mr-1"></i>Created: {{ $user->created_at->format('M d, Y') }}
                                    </div>
                                </div>
                                <div>
                                    <span class="badge badge-success px-3 py-2" style="border-radius: 12px;">
                                        <i class="fas fa-check-circle mr-1"></i>Active
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted font-weight-bold">No Users Created</h5>
                            <p class="text-muted mb-4">Create the first user account to manage this franchise</p>
                            <button class="btn btn-success btn-lg" onclick="createUser({{ $franchise->id }})" style="border-radius: 25px;">
                                <i class="fas fa-plus mr-2"></i>Create First User
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Students Section -->
    @if($franchise->students->count() > 0)
        <div class="card info-card">
            <div class="card-header bg-white border-0 pt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 font-weight-bold text-dark">
                        <i class="fas fa-user-graduate text-info mr-2"></i>Enrolled Students
                        <span class="badge badge-info ml-2">{{ $franchise->students->count() }} Total</span>
                    </h5>
                    <a href="{{ route('admin.students.index') }}?franchise={{ $franchise->id }}" class="btn btn-info btn-sm" style="border-radius: 8px;">
                        <i class="fas fa-eye mr-1"></i> View All
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 font-weight-bold text-dark">Student ID</th>
                                <th class="border-0 font-weight-bold text-dark">Student Name</th>
                                <th class="border-0 font-weight-bold text-dark">Email Address</th>
                                <th class="border-0 font-weight-bold text-dark">Course</th>
                                <th class="border-0 font-weight-bold text-dark">Status</th>
                                <th class="border-0 font-weight-bold text-dark">Enrolled Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($franchise->students->take(10) as $student)
                                <tr>
                                    <td class="py-3">
                                        <strong class="text-primary">{{ $student->student_id }}</strong>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <div style="width: 35px; height: 35px; background: linear-gradient(45deg, #007bff, #6610f2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px; margin-right: 0.75rem;">
                                                {{ substr($student->name, 0, 1) }}
                                            </div>
                                            <span class="font-weight-medium">{{ $student->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3">{{ $student->email }}</td>
                                    <td class="py-3">
                                        @if($student->course)
                                            <span class="badge badge-info px-3 py-2" style="border-radius: 12px;">{{ $student->course->name }}</span>
                                        @else
                                            <span class="text-muted">Not Assigned</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <span class="badge badge-{{ $student->status_badge }} px-3 py-2" style="border-radius: 12px;">
                                            {{ ucfirst($student->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3">{{ $student->enrollment_date->format('M d, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($franchise->students->count() > 10)
                    <div class="card-footer bg-light text-center">
                        <a href="{{ route('admin.students.index') }}?franchise={{ $franchise->id }}" class="btn btn-outline-info">
                            <i class="fas fa-eye mr-2"></i>View All {{ $franchise->students->count() }} Students
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Create User Modal (Bootstrap 4 Compatible) -->
    <div class="modal fade" id="createUserModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-user-plus text-success mr-2"></i>Create New User for {{ $franchise->name }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="createUserForm">
                    <div class="modal-body pt-0">
                        <div class="form-group">
                            <label class="font-weight-medium">Full Name</label>
                            <input type="text" class="form-control" name="name" required style="border-radius: 8px; padding: 12px;">
                        </div>
                        <div class="form-group">
                            <label class="font-weight-medium">Email Address</label>
                            <input type="email" class="form-control" name="email" required style="border-radius: 8px; padding: 12px;">
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary px-4" data-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-success px-4" style="border-radius: 8px;">
                            <i class="fas fa-plus mr-2"></i>Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="{{ asset('js/admin/franchise-show.js') }}"></script>
@endsection
