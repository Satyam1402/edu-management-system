{{-- resources/views/admin/franchises/index.blade.php --}}
@extends('layouts.custom-admin')

@section('title', 'Franchise Management')
@section('page-title', 'Franchise Management')

@section('content')
    <!-- Success Message with Credentials -->
    @if(session('user_created'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border: none; border-radius: 12px; background: linear-gradient(45deg, #28a745, #20c997); color: white;">
            <h5><i class="fas fa-check-circle me-2"></i>Franchise & User Created Successfully!</h5>
            <hr style="border-color: rgba(255,255,255,0.3);">
            <div class="row">
                <div class="col-md-6">
                    <strong>Franchise:</strong> {{ session('user_created')['franchise'] }}<br>
                    <strong>Owner:</strong> {{ session('user_created')['name'] }}
                </div>
                <div class="col-md-6">
                    <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px; backdrop-filter: blur(10px);">
                        <strong>🔐 LOGIN CREDENTIALS:</strong><br>
                        <strong>Email:</strong> <code style="background: rgba(255,255,255,0.3); padding: 2px 6px; border-radius: 4px;">{{ session('user_created')['email'] }}</code><br>
                        <strong>Password:</strong> <code style="background: rgba(255,255,255,0.3); padding: 2px 6px; border-radius: 4px;">{{ session('user_created')['password'] }}</code><br>
                        <strong>Panel URL:</strong> <code style="background: rgba(255,255,255,0.3); padding: 2px 6px; border-radius: 4px;">{{ url('/franchise') }}</code>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <small>
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <strong>Important:</strong> Share these credentials with the franchise owner immediately. They can change the password after first login.
                </small>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #28a745 !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 rounded-circle" style="background: linear-gradient(45deg, #28a745, #20c997);">
                                <i class="fas fa-building text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Total Franchises</div>
                            <div class="h4 mb-0">{{ \App\Models\Franchise::count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #007bff !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 rounded-circle" style="background: linear-gradient(45deg, #007bff, #6610f2);">
                                <i class="fas fa-check-circle text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Active Franchises</div>
                            <div class="h4 mb-0">{{ \App\Models\Franchise::active()->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 rounded-circle" style="background: linear-gradient(45deg, #ffc107, #fd7e14);">
                                <i class="fas fa-users text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Total Students</div>
                            <div class="h4 mb-0">{{ \App\Models\Student::count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545 !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 rounded-circle" style="background: linear-gradient(45deg, #dc3545, #e83e8c);">
                                <i class="fas fa-user-tie text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Franchise Users</div>
                            <div class="h4 mb-0">{{ \App\Models\User::role('franchise')->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Franchise List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-building text-primary me-2"></i>Franchises List
                    </h5>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.franchises.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add New Franchise
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($franchises->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Franchise Name</th>
                                <th>Contact</th>
                                <th>Location</th>
                                <th>Users</th>
                                <th>Students</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($franchises as $franchise)
                                <tr>
                                    <td><strong class="text-primary">{{ $franchise->code }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2" style="width: 40px; height: 40px; background: linear-gradient(45deg, #007bff, #6610f2); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 16px;">
                                                {{ substr($franchise->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $franchise->name }}</div>
                                                <div class="text-muted small">{{ $franchise->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <i class="fas fa-phone text-muted me-1"></i>{{ $franchise->phone }}<br>
                                            <i class="fas fa-envelope text-muted me-1"></i>{{ $franchise->email }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            {{ $franchise->city }}{{ $franchise->city && $franchise->state ? ', ' : '' }}{{ $franchise->state }}
                                            @if($franchise->pincode)
                                                <br><span class="text-muted">{{ $franchise->pincode }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $franchise->users->count() }} User(s)</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $franchise->students->count() }} Student(s)</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $franchise->status_badge }}">
                                            {{ ucfirst($franchise->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.franchises.show', $franchise) }}"
                                               class="btn btn-sm btn-outline-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.franchises.edit', $franchise) }}"
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                    title="Create User" onclick="createUser({{ $franchise->id }})">
                                                <i class="fas fa-user-plus"></i>
                                            </button>
                                            <form action="{{ route('admin.franchises.destroy', $franchise) }}"
                                                  method="POST" style="display: inline-block;"
                                                  onsubmit="return confirm('Are you sure you want to delete this franchise?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
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
                        Showing {{ $franchises->firstItem() }} to {{ $franchises->lastItem() }} of {{ $franchises->total() }} franchises
                    </div>
                    {{ $franchises->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-building fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No Franchises Yet</h4>
                    <p class="text-muted">Start expanding your business by adding your first franchise.</p>
                    <a href="{{ route('admin.franchises.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>Add First Franchise
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Create User Modal -->
    <div class="modal fade" id="createUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New User</h5>
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
let currentFranchiseId = null;

function createUser(franchiseId) {
    currentFranchiseId = franchiseId;
    new bootstrap.Modal(document.getElementById('createUserModal')).show();
}

document.getElementById('createUserForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    console.log('Sending request to:', `/admin/franchises/${currentFranchiseId}/create-user`); // Debug log

    fetch(`/admin/franchises/${currentFranchiseId}/create-user`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status); // Debug log
        console.log('Response ok:', response.ok); // Debug log
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data); // Debug log

        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('createUserModal')).hide();

            // Show credentials alert
            const alertHtml = `
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="border: none; border-radius: 12px; background: linear-gradient(45deg, #28a745, #20c997); color: white;">
                    <h5><i class="fas fa-check-circle me-2"></i>User Created Successfully!</h5>
                    <hr style="border-color: rgba(255,255,255,0.3);">
                    <div class="row">
                        <div class="col-md-12">
                            <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px; backdrop-filter: blur(10px);">
                                <strong>🔐 LOGIN CREDENTIALS:</strong><br>
                                <strong>Email:</strong> <code style="background: rgba(255,255,255,0.3); padding: 2px 6px; border-radius: 4px;">${data.user.email}</code><br>
                                <strong>Password:</strong> <code style="background: rgba(255,255,255,0.3); padding: 2px 6px; border-radius: 4px;">${data.user.password}</code><br>
                                <strong>Panel URL:</strong> <code style="background: rgba(255,255,255,0.3); padding: 2px 6px; border-radius: 4px;">${window.location.origin}/franchise</code>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small>
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <strong>Important:</strong> Share these credentials with the franchise owner immediately.
                        </small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            `;
            document.querySelector('.content').insertAdjacentHTML('afterbegin', alertHtml);

            // Reload page after 5 seconds to update user count
            setTimeout(() => location.reload(), 5000);
        } else {
            console.error('Success flag is false:', data);
            alert('Error: ' + (data.message || 'Unknown error occurred'));
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Network error creating user: ' + error.message);
    });
});
</script>

@endsection
