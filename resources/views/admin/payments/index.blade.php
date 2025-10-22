{{-- resources/views/admin/payments/index.blade.php --}}
@extends('layouts.custom-admin')

@section('title', 'Manage Payments')
@section('page-title', 'Payment Management')

@section('css')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap4.min.css">

<!-- Custom Payment DataTable Styles -->
<style>
.payment-management-header {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    color: white;
    padding: 2rem;
    border-radius: 15px 15px 0 0;
    position: relative;
    overflow: hidden;
}

.payment-management-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 100%;
    height: 200%;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    transform: rotate(-15deg);
}

.payment-management-header h3,
.payment-management-header p {
    position: relative;
    z-index: 2;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.payment-stats-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    padding: 1.5rem;
    margin-bottom: 1rem;
    border-left: 4px solid #17a2b8;
}

.datatable-container {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    overflow: hidden;
    margin-bottom: 2rem;
}

#paymentsTable thead th {
    background: #f8f9fa;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    padding: 1rem 0.75rem;
}

#paymentsTable tbody td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
    border-bottom: 1px solid #f0f0f0;
    font-size: 13px;
    line-height: 1.4;
}

.payment-details {
    min-width: 150px;
}

.student-info {
    min-width: 180px;
}

.btn-group {
    display: flex;
    justify-content: center;
    gap: 0.25rem;
}

.btn-group .btn {
    padding: 0.375rem 0.5rem;
    font-size: 12px;
    border-radius: 6px;
}
</style>
@endsection

@section('content')
    <!-- Enhanced Page Header -->
    <div class="datatable-container">
        <div class="payment-management-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="mb-2 font-weight-bold">
                        <i class="fas fa-credit-card mr-3"></i>Payment Management
                    </h3>
                    <p class="mb-0 h6" style="opacity: 0.9;">
                        Track and manage all student payments and transactions
                    </p>
                </div>
                <div class="col-md-4 text-right">
                    <a href="{{ route('admin.payments.create') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-plus mr-2"></i>Create Payment
                    </a>
                </div>
            </div>
        </div>

        <!-- Payment Stats Row -->
        <div class="p-4">
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="payment-stats-card text-center">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Revenue
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalRevenue">
                                    ₹0.00
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-rupee-sign fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="payment-stats-card text-center">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Pending Payments
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingCount">
                                    0
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="payment-stats-card text-center">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    This Month
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="monthlyRevenue">
                                    ₹0.00
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="payment-stats-card text-center">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Success Rate
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="successRate">
                                    0%
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ALERTS -->
    @if(session('success'))
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
    @endif

    <!-- Enhanced DataTable -->
    <div class="datatable-container">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="paymentsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Info</th>
                        <th>Payment Details</th>
                        <th>Gateway Info</th>
                        <th>Status</th>
                        <th>Date Info</th>
                        <th>Actions</th>
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
    // Initialize DataTable
    const table = $('#paymentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.payments.index') }}",
            type: "GET"
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'student_info', name: 'student.name', className: 'student-info' },
            { data: 'payment_details', name: 'order_id', className: 'payment-details' },
            { data: 'gateway_info', name: 'gateway', className: 'text-center' },
            { data: 'payment_status', name: 'status', className: 'text-center' },
            { data: 'date_info', name: 'created_at', className: 'text-center' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[5, 'desc']], // Order by date info
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        responsive: false,
        scrollX: true,
        autoWidth: false,
        language: {
            processing: '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>',
            emptyTable: '<div class="text-center py-4"><i class="fas fa-credit-card fa-3x text-muted mb-3"></i><br><h5 class="text-muted">No Payments Found</h5><p class="text-muted">Start by creating your first payment!</p><a href="{{ route('admin.payments.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Create First Payment</a></div>',
            zeroRecords: '<div class="text-center py-4"><i class="fas fa-search fa-3x text-muted mb-3"></i><br><h5 class="text-muted">No matching records found</h5><p class="text-muted">Try adjusting your search criteria.</p></div>'
        },
        drawCallback: function() {
            // Initialize tooltips after table redraw
            $('[data-toggle="tooltip"]').tooltip();
        }
    });

    // Handle delete button clicks
    $(document).on('click', '.delete-payment', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        deletePayment(id);
    });

    // Handle retry payment button clicks
    $(document).on('click', '.retry-payment', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        retryPayment(id);
    });

    // Load payment statistics
    loadPaymentStats();
});

// Delete payment function
function deletePayment(id) {
    if (confirm('⚠️ Are you sure you want to delete this payment?\n\nThis action cannot be undone.')) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $.ajax({
            url: `/admin/payments/${id}`,
            type: 'DELETE',
            success: function(response) {
                $('#paymentsTable').DataTable().ajax.reload();
                showAlert('success', 'Payment deleted successfully!');
                loadPaymentStats(); // Refresh stats
            },
            error: function(xhr) {
                let message = 'Error deleting payment.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showAlert('error', message);
            }
        });
    }
}

// Retry payment function
function retryPayment(id) {
    if (confirm('Do you want to retry this failed payment?')) {
        // Redirect to checkout page for retry
        window.location.href = `/admin/payments/${id}/checkout`;
    }
}

// Load payment statistics
function loadPaymentStats() {
    $.ajax({
        url: '/admin/payments/stats',
        type: 'GET',
        success: function(data) {
            $('#totalRevenue').text('₹' + parseFloat(data.totalRevenue || 0).toLocaleString('en-IN', {minimumFractionDigits: 2}));
            $('#pendingCount').text(data.pendingCount || 0);
            $('#monthlyRevenue').text('₹' + parseFloat(data.monthlyRevenue || 0).toLocaleString('en-IN', {minimumFractionDigits: 2}));
            $('#successRate').text(data.successRate || '0%');
        },
        error: function() {
            console.log('Could not load payment statistics');
        }
    });
}

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
    
    setTimeout(() => {
        $('.alert').alert('close');
    }, 5000);
}

// Refresh table function
function refreshTable() {
    $('#paymentsTable').DataTable().ajax.reload();
    loadPaymentStats();
}
</script>
@endsection
