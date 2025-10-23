@extends('layouts.custom-admin')

@section('page-title', 'Payment Management')

@section('css')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">

<style>
.container-fluid {
    padding: 0 20px !important;
    background: #f5f7fa;
    /* min-height: 100vh; */
}

.card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    margin: 20px 0;
    overflow: hidden;
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95);
}

.card-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 50%, #17a2b8 100%);
    color: white;
    border-radius: 20px 20px 0 0;
    padding: 25px 30px;
    border: none;
    position: relative;
    overflow: hidden;
}

.card-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="10" cy="10" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="20" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="20" cy="90" r="0.8" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
    pointer-events: none;
}

.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border-left: 5px solid transparent;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.stat-card.revenue { border-left-color: #28a745; }
.stat-card.pending { border-left-color: #ffc107; }
.stat-card.monthly { border-left-color: #17a2b8; }
.stat-card.success { border-left-color: #6610f2; }

.stat-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
    background: linear-gradient(135deg, #28a745, #20c997);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.stat-number {
    font-size: 2.2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 10px;
}

.stat-label {
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.filter-section {
    background: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.filter-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    align-items: end;
}

.filter-group label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
    display: block;
}

.filter-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 8px 16px;
    border: 2px solid #dee2e6;
    background: white;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 14px;
    font-weight: 500;
    color: #6c757d;
}

.filter-btn.active {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    border-color: transparent;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.filter-btn:hover:not(.active) {
    border-color: #28a745;
    color: #28a745;
    background: rgba(40, 167, 69, 0.1);
}

/* DataTable Enhancements */
.dataTables_wrapper {
    padding: 0 !important;
}

.dataTables_filter {
    display: none;
}

.dataTables_length {
    margin-bottom: 20px;
}

table.dataTable {
    border-collapse: separate !important;
    border-spacing: 0;
    width: 100% !important;
    margin: 0 !important;
}

table.dataTable thead th {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    color: white !important;
    border: none !important;
    font-weight: 600 !important;
    padding: 15px 12px !important;
    text-align: center !important;
    border-bottom: none !important;
}

table.dataTable tbody tr {
    background: white !important;
    transition: all 0.3s ease;
}

table.dataTable tbody tr:nth-child(even) {
    background: #f8f9fa !important;
}

table.dataTable tbody tr:hover {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.05), rgba(32, 201, 151, 0.05)) !important;
    transform: scale(1.01);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

table.dataTable tbody td {
    padding: 15px 12px !important;
    border-bottom: 1px solid #dee2e6 !important;
    vertical-align: middle !important;
}

.btn-group .btn {
    border-radius: 6px !important;
    margin: 0 1px;
    transition: all 0.3s ease;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

@media (max-width: 768px) {
    .stats-container {
        grid-template-columns: 1fr;
    }
    
    .filter-row {
        grid-template-columns: 1fr;
    }
    
    .filter-buttons {
        justify-content: center;
    }
}

/* Loading Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    animation: fadeInUp 0.6s ease-out;
}

.stat-card:nth-child(1) { animation: fadeInUp 0.6s ease-out 0.1s both; }
.stat-card:nth-child(2) { animation: fadeInUp 0.6s ease-out 0.2s both; }
.stat-card:nth-child(3) { animation: fadeInUp 0.6s ease-out 0.3s both; }
.stat-card:nth-child(4) { animation: fadeInUp 0.6s ease-out 0.4s both; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="mb-2 mb-md-0">
                    <h4 class="text-muted mb-1">Payment Management</h4>
                    <p class="text-muted mb-0">Track and manage all student payments and transactions</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-outline-success" onclick="exportPayments()">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                    <a href="{{ route('admin.payments.create') }}" class="btn btn-success">
                        <i class="fas fa-plus mr-2"></i>Create Payment
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-container">
        <div class="stat-card revenue">
            <div class="stat-icon">
                <i class="fas fa-rupee-sign"></i>
            </div>
            <div class="stat-number" id="totalRevenue">₹0.00</div>
            <div class="stat-label">Total Revenue</div>
        </div>
        <div class="stat-card pending">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-number" id="pendingPayments">0</div>
            <div class="stat-label">Pending Payments</div>
        </div>
        <div class="stat-card monthly">
            <div class="stat-icon">
                <i class="fas fa-calendar-month"></i>
            </div>
            <div class="stat-number" id="thisMonthRevenue">₹0.00</div>
            <div class="stat-label">This Month</div>
        </div>
        <div class="stat-card success">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-number" id="successRate">0%</div>
            <div class="stat-label">Success Rate</div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="filter-row">
            <div class="filter-group">
                <label>Filter by Status</label>
                <div class="filter-buttons">
                    <div class="filter-btn active" data-status="">All Status</div>
                    <div class="filter-btn" data-status="pending">Pending</div>
                    <div class="filter-btn" data-status="completed">Completed</div>
                    <div class="filter-btn" data-status="failed">Failed</div>
                    <div class="filter-btn" data-status="refunded">Refunded</div>
                </div>
            </div>
            <div class="filter-group">
                <label>Filter by Date</label>
                <div class="filter-buttons">
                    <div class="filter-btn active" data-date="">All Time</div>
                    <div class="filter-btn" data-date="today">Today</div>
                    <div class="filter-btn" data-date="week">This Week</div>
                    <div class="filter-btn" data-date="month">This Month</div>
                </div>
            </div>
            <div class="filter-group">
                <label for="searchInput">Search Payments</label>
                <input type="text" id="searchInput" class="form-control" 
                       placeholder="Search by order ID, student name...">
            </div>
        </div>
    </div>

    <!-- Payments DataTable -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 font-weight-bold text-white">
                        <i class="fas fa-credit-card mr-2"></i>All Payments
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="paymentsTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>Order Info</th>
                                <th>Student Info</th>
                                <th>Payment Details</th>
                                <th>Gateway Info</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTables will populate this -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

<script>
let currentStatusFilter = '';
let currentDateFilter = '';
let paymentsTable;

$(document).ready(function() {
    // Initialize DataTable
    initializeDataTable();
    
    // Load statistics
    updateStats();
    
    // Filter buttons
    $('.filter-btn[data-status]').click(function() {
        $('.filter-btn[data-status]').removeClass('active');
        $(this).addClass('active');
        currentStatusFilter = $(this).data('status');
        paymentsTable.ajax.reload();
        updateStats();
    });
    
    $('.filter-btn[data-date]').click(function() {
        $('.filter-btn[data-date]').removeClass('active');
        $(this).addClass('active');
        currentDateFilter = $(this).data('date');
        paymentsTable.ajax.reload();
    });
    
    // Custom search
    $('#searchInput').on('keyup', function() {
        paymentsTable.search(this.value).draw();
    });
});

function initializeDataTable() {
    paymentsTable = $('#paymentsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('admin.payments.index') }}",
            data: function (d) {
                d.status_filter = currentStatusFilter;
                d.date_filter = currentDateFilter;
            }
        },
        columns: [
            { data: 'order_info', name: 'order_id', orderable: true },
            { data: 'student_info', name: 'student.name', orderable: false },
            { data: 'payment_details', name: 'amount', orderable: true },
            { data: 'gateway_info', name: 'gateway', orderable: true },
            { data: 'status', name: 'status', orderable: true },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        language: {
            processing: '<i class="fas fa-spinner fa-spin"></i> Loading payments...',
            emptyTable: "No payments found",
            zeroRecords: "No matching payments found"
        }
    });
}

function updateStats() {
    $.get("{{ route('admin.payments.index') }}", { get_stats: true }, function(data) {
        if (data.stats) {
            $('#totalRevenue').text('₹' + parseFloat(data.stats.total_revenue || 0).toLocaleString('en-IN', {minimumFractionDigits: 2}));
            $('#pendingPayments').text(data.stats.pending_payments || 0);
            $('#thisMonthRevenue').text('₹' + parseFloat(data.stats.this_month_revenue || 0).toLocaleString('en-IN', {minimumFractionDigits: 2}));
            $('#successRate').text((data.stats.success_rate || 0) + '%');
        }
    });
}

function markAsCompleted(paymentId) {
    if (!confirm('Are you sure you want to mark this payment as completed?')) return;
    
    $.ajax({
        url: `/admin/payments/${paymentId}/mark-completed`,
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            paymentsTable.ajax.reload();
            updateStats();
            showToast('success', response.message);
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Error updating payment';
            showToast('error', message);
        }
    });
}

function markAsFailed(paymentId) {
    if (!confirm('Are you sure you want to mark this payment as failed?')) return;
    
    $.ajax({
        url: `/admin/payments/${paymentId}/mark-failed`,
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            paymentsTable.ajax.reload();
            updateStats();
            showToast('success', response.message);
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Error updating payment';
            showToast('error', message);
        }
    });
}

function processRefund(paymentId) {
    if (!confirm('Are you sure you want to process a refund for this payment?')) return;
    
    $.ajax({
        url: `/admin/payments/${paymentId}/refund`,
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            paymentsTable.ajax.reload();
            updateStats();
            showToast('success', response.message);
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Error processing refund';
            showToast('error', message);
        }
    });
}

function deletePayment(paymentId) {
    if (!confirm('Are you sure you want to delete this payment? This action cannot be undone.')) return;
    
    $.ajax({
        url: `/admin/payments/${paymentId}`,
        type: 'DELETE',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            paymentsTable.ajax.reload();
            updateStats();
            showToast('success', response.message);
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Error deleting payment';
            showToast('error', message);
        }
    });
}

function exportPayments() {
    const status = currentStatusFilter;
    const url = "{{ route('admin.payments.export') }}" + (status ? `?status=${status}` : '');
    window.location.href = url;
    showToast('info', 'Export started! File will download shortly.');
}

function showToast(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'error' ? 'alert-danger' : 'alert-info';
    
    const toast = $(`
        <div class="position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
            <div class="alert ${alertClass} alert-dismissible fade show shadow-lg">
                <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'}-circle mr-2"></i>
                <strong>${message}</strong>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        </div>
    `);
    
    $('body').append(toast);
    setTimeout(() => toast.find('.alert').alert('close'), 4000);
}
</script>
@endsection
