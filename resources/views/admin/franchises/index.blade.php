{{-- resources/views/admin/franchises/index.blade.php - DataTables Version --}}
@extends('layouts.custom-admin')

@section('title', 'Manage Franchises')
@section('page-title', 'Franchise Management')

@section('css')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap4.min.css">

<!-- Custom Franchise DataTable Styles -->
<link rel="stylesheet" href="{{ asset('css/admin/franchise-index.css') }}">
@endsection


@section('content')
    <!-- Enhanced Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="mb-1 font-weight-bold">
                    <i class="fas fa-building mr-3"></i>Franchise Management
                </h2>
                <p class="mb-0 h6" style="opacity: 0.9;">
                    Manage and monitor all franchise locations
                </p>
            </div>
            <div class="col-md-6 text-right">
                <a href="{{ route('admin.franchises.create') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-plus mr-2"></i>Add New Franchise
                </a>
            </div>
        </div>
    </div>

    <!-- ALERTS MOVED HERE - RIGHT AFTER HEADER -->
    <!-- @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif -->

    <!-- User Creation Success Alert - FIXED -->
    @if(session('user_created'))
        @php $userData = session('user_created'); @endphp
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert" style="background: linear-gradient(45deg, #28a745, #20c997); color: white;">
            <h5><i class="fas fa-check-circle mr-2"></i>Franchise & User Created Successfully!</h5>
            <hr style="border-color: rgba(255,255,255,0.3);">
            <div class="mt-3">
                <strong><i class="fas fa-key mr-1"></i>LOGIN CREDENTIALS:</strong><br><br>
                <div class="row">
                    <div class="col-sm-6 mb-2">
                        <strong>Franchise:</strong><br>
                        <span class="credential-box">{{ $userData['franchise'] }}</span>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <strong>Owner Name:</strong><br>
                        <span class="credential-box">{{ $userData['name'] }}</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 mb-2">
                        <strong>Login Email:</strong><br>
                        <span class="credential-box">{{ $userData['email'] }}</span>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <strong>Password:</strong><br>
                        <span class="credential-box">{{ $userData['password'] }}</span>
                    </div>
                </div>
                <div class="mt-3 p-2" style="background: rgba(255,255,255,0.1); border-radius: 8px; border-left: 4px solid rgba(255,255,255,0.3);">
                    <small><i class="fas fa-info-circle mr-1"></i><strong>Important:</strong> Please save these credentials securely and share them with the franchise owner.</small>
                </div>
            </div>
            <button type="button" class="close text-white" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
        
        @php
            // Clear the session after displaying to prevent duplicates
            session()->forget('user_created');
        @endphp
    @endif

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card text-center">
                <div class="card-body p-4">
                    <div class="text-primary mb-3">
                        <i class="fas fa-building fa-3x"></i>
                    </div>
                    <h3 class="mb-1 font-weight-bold" id="totalFranchises">{{ App\Models\Franchise::count() }}</h3>
                    <p class="text-muted mb-0">Total Franchises</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card text-center">
                <div class="card-body p-4">
                    <div class="text-success mb-3">
                        <i class="fas fa-check-circle fa-3x"></i>
                    </div>
                    <h3 class="mb-1 font-weight-bold">{{ App\Models\Franchise::where('status', 'active')->count() }}</h3>
                    <p class="text-muted mb-0">Active Franchises</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card text-center">
                <div class="card-body p-4">
                    <div class="text-info mb-3">
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                    <h3 class="mb-1 font-weight-bold">{{ App\Models\User::whereHas('franchise')->count() }}</h3>
                    <p class="text-muted mb-0">Franchise Users</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card text-center">
                <div class="card-body p-4">
                    <div class="text-warning mb-3">
                        <i class="fas fa-user-graduate fa-3x"></i>
                    </div>
                    <h3 class="mb-1 font-weight-bold">{{ App\Models\Student::count() }}</h3>
                    <p class="text-muted mb-0">Total Students</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced DataTable -->
    <div class="datatable-container">
        <div class="table-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0 font-weight-bold text-dark">
                        <i class="fas fa-list mr-2 text-primary"></i>All Franchises
                    </h5>
                </div>
                <div class="col-md-6 text-right">
                    <button class="btn btn-outline-primary btn-sm mr-2" onclick="refreshTable()">
                        <i class="fas fa-sync-alt mr-1"></i>Refresh
                    </button>
                    <button class="btn btn-outline-success btn-sm" onclick="exportData()">
                        <i class="fas fa-download mr-1"></i>Export
                    </button>
                </div>
            </div>
        </div>
        
        <div class="table-responsive p-3">
            <table class="table table-hover" id="franchisesTable">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="18%">Franchise Details</th>
                        <th width="22%">Contact Information</th>
                        <th width="20%">Location</th>
                        <th width="8%">Users</th>
                        <th width="10%">Status</th>
                        <th width="12%">Dates</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    $('#franchisesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.franchises.index') }}",
            type: "GET"
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'franchise_details', name: 'name' },
            { data: 'contact_info', name: 'email' },
            { data: 'location_info', name: 'city' }, // FIXED: Changed from 'location' to 'location_info'
            { 
                data: 'users_count', 
                name: 'users_count',
                className: 'text-center',
                searchable: false,
                orderable: false,
                render: function(data, type, row) {
                    return '<span class="badge badge-info px-3 py-2">' + data + '</span>';
                }
            },
            { data: 'status_badge', name: 'status', className: 'text-center' },
            { data: 'date_info', name: 'created_at', className: 'text-center' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[6, 'desc']],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        responsive: true,
        language: {
            processing: '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>',
            emptyTable: '<div class="text-center py-4"><i class="fas fa-building fa-3x text-muted mb-3"></i><br><h5 class="text-muted">No franchises found</h5><p class="text-muted">Start by creating your first franchise.</p></div>',
            zeroRecords: '<div class="text-center py-4"><i class="fas fa-search fa-3x text-muted mb-3"></i><br><h5 class="text-muted">No matching records found</h5><p class="text-muted">Try adjusting your search criteria.</p></div>'
        }
    });
});


// Refresh table function
function refreshTable() {
    $('#franchisesTable').DataTable().ajax.reload();
    
    // Update stats
    location.reload();
}

// Export function
function exportData() {
    $('#franchisesTable').DataTable().button('.buttons-excel').trigger();
}

// Delete franchise function
// Delete franchise function - UPDATED
function deleteFranchise(id) {
    if (confirm('⚠️ Are you sure you want to delete this franchise?\n\nThis action cannot be undone and will also remove all associated users.')) {
        // Show loading state
        $(`button[data-id="${id}"]`).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $.ajax({
            url: `/admin/franchises/${id}`,
            type: 'DELETE',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Reload table
                    $('#franchisesTable').DataTable().ajax.reload();
                    showAlert('success', response.message);
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function(xhr) {
                let message = 'Error deleting franchise.';
                
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        message = response.message;
                    }
                } catch(e) {
                    console.error('Error parsing response:', e);
                }
                
                showAlert('error', message);
            },
            complete: function() {
                // Re-enable button
                $(`button[data-id="${id}"]`).prop('disabled', false).html('<i class="fas fa-trash"></i>');
            }
        });
    }
}

// Also add event delegation for dynamically created buttons
$(document).ready(function() {
    // Existing DataTable initialization...
    
    // Handle delete button clicks with event delegation
    $(document).on('click', '.delete-franchise', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        deleteFranchise(id);
    });
});


// Show alert function
function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show mt-3" role="alert">
            <i class="${icon} mr-2"></i>${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `;
    
    $('body').prepend(alertHtml);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        $('.alert').alert('close');
    }, 5000);
}
</script>
@endsection
