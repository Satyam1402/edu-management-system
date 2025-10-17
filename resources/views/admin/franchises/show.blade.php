{{-- resources/views/admin/franchises/show.blade.php --}}
@extends('layouts.custom-admin')

@section('title', $franchise->name)
@section('page-title', 'Franchise Details - ' . $franchise->name)

@section('content')
    <!-- Franchise Header -->
    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="card-body text-white p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-lg me-3" style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 24px;">
                            {{ substr($franchise->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="mb-1">{{ $franchise->name }}</h3>
                            <p class="mb-0 opacity-90">Code: <strong>{{ $franchise->code }}</strong></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><i class="fas fa-envelope me-2"></i>{{ $franchise->email }}</p>
                            <p class="mb-0"><i class="fas fa-phone me-2"></i>{{ $franchise->phone }}</p>
                        </div>
                        <div class="col-md-6">
                            @if($franchise->city || $franchise->state)
                                <p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i>{{ $franchise->city }}{{ $franchise->city && $franchise->state ? ', ' : '' }}{{ $franchise->state }}</p>
                            @endif
                            <span class="badge bg-{{ $franchise->status_badge }} px-3 py-2">{{ ucfirst($franchise->status) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-right">
                    <div class="d-flex flex-column align-items-end">
                        <div class="mb-2">
                            <a href="{{ route('admin.franchises.edit', $franchise) }}" class="btn btn-light btn-sm me-2">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button class="btn btn-outline-light btn-sm" onclick="createUser({{ $franchise->id }})">
                                <i class="fas fa-user-plus"></i> Add User
                            </button>
                        </div>
                        <div class="dashboard-icon" style="font-size: 3rem; opacity: 0.3;">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 text-center">
                <div class="card-body">
                    <div class="text-primary mb-2">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['total_students'] }}</h4>
                    <small class="text-muted">Total Students</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 text-center">
                <div class="card-body">
                    <div class="text-success mb-2">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['active_students'] }}</h4>
                    <small class="text-muted">Active Students</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 text-center">
                <div class="card-body">
                    <div class="text-info mb-2">
                        <i class="fas fa-user-tie fa-2x"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['total_users'] }}</h4>
                    <small class="text-muted">System Users</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 text-center">
                <div class="card-body">
                    <div class="text-warning mb-2">
                        <i class="fas fa-calendar fa-2x"></i>
                    </div>
                    <h4 class="mb-1">{{ $franchise->created_at->diffInDays() }}</h4>
                    <small class="text-muted">Days Active</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Franchise Details -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>Franchise Information
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-medium text-muted" width="40%">Franchise Code:</td>
                            <td><strong class="text-primary">{{ $franchise->code }}</strong></td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-muted">Contact Person:</td>
                            <td>{{ $franchise->contact_person ?: 'Not specified' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-muted">Email:</td>
                            <td>{{ $franchise->email }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-muted">Phone:</td>
                            <td>{{ $franchise->phone }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-muted">Address:</td>
                            <td>
                                @if($franchise->address)
                                    {{ $franchise->address }}<br>
                                @endif
                                @if($franchise->city || $franchise->state)
                                    {{ $franchise->city }}{{ $franchise->city && $franchise->state ? ', ' : '' }}{{ $franchise->state }}
                                    @if($franchise->pincode) - {{ $franchise->pincode }} @endif
                                @else
                                    Not specified
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-muted">Established:</td>
                            <td>{{ $franchise->established_date ? $franchise->established_date->format('M d, Y') : 'Not specified' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-muted">Created:</td>
                            <td>{{ $franchise->created_at->format('M d, Y \a\t H:i') }}</td>
                        </tr>
                    </table>

                    @if($franchise->notes)
                        <div class="mt-3">
                            <strong class="text-muted">Notes:</strong>
                            <p class="mt-1">{{ $franchise->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Franchise Users -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-users text-success me-2"></i>Franchise Users ({{ $franchise->users->count() }})
                        </h5>
                        <button class="btn btn-sm btn-success" onclick="createUser({{ $franchise->id }})">
                            <i class="fas fa-plus"></i> Add User
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($franchise->users->count() > 0)
                        @foreach($franchise->users as $user)
                            <div class="d-flex align-items-center mb-3 p-3 border rounded">
                                <div class="avatar-sm me-3" style="width: 40px; height: 40px; background: linear-gradient(45deg, #28a745, #20c997); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px;">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-medium">{{ $user->name }}</div>
                                    <div class="text-muted small">{{ $user->email }}</div>
                                    <div class="text-muted small">Created: {{ $user->created_at->format('M d, Y') }}</div>
                                </div>
                                <div>
                                    <span class="badge bg-success">Active</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No users created yet</p>
                            <button class="btn btn-success" onclick="createUser({{ $franchise->id }})">
                                <i class="fas fa-plus me-2"></i>Create First User
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Students -->
    @if($franchise->students->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-graduate text-info me-2"></i>Recent Students ({{ $franchise->students->count() }} total)
                </h5>
            </div>
            <div class="card-body">
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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($franchise->students->take(10) as $student)
                                <tr>
                                    <td><strong>{{ $student->student_id }}</strong></td>
                                    <td>{{ $student->name }}</td>
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
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($franchise->students->count() > 10)
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.students.by-franchise', $franchise) }}" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-2"></i>View All {{ $franchise->students->count() }} Students
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Create User Modal (same as in index page) -->
    <div class="modal fade" id="createUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New User for {{ $franchise->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createUserForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
function createUser(franchiseId) {
    new bootstrap.Modal(document.getElementById('createUserModal')).show();

    document.getElementById('createUserForm').onsubmit = function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch(`/admin/franchises/${franchiseId}/create-user`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('createUserModal')).hide();

                // Show success alert
                const alertHtml = `
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <h5><i class="fas fa-check-circle me-2"></i>User Created Successfully!</h5>
                        <strong>Login Credentials:</strong><br>
                        <strong>Email:</strong> <code>${data.user.email}</code><br>
                        <strong>Password:</strong> <code>${data.user.password}</code>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                document.querySelector('.content').insertAdjacentHTML('afterbegin', alertHtml);

                // Reload after 3 seconds
                setTimeout(() => location.reload(), 3000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error creating user. Please try again.');
        });
    };
}
</script>
@endsection
