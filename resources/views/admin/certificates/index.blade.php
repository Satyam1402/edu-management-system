@extends('layouts.custom-admin')

@section('page-title', 'Certificates')

@section('css')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">

<style>
.container-fluid {
    padding: 0 20px !important;
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    margin: 20px 0;
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    border-radius: 15px 15px 0 0;
    padding: 20px 25px;
    border: none;
}

.filter-section {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}

.status-filter {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

.status-btn {
    padding: 8px 16px;
    border: 2px solid #dee2e6;
    background: white;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 14px;
    font-weight: 500;
}

.status-btn.active {
    background: #28a745;
    color: white;
    border-color: #28a745;
}

.status-btn:hover {
    border-color: #28a745;
    background: rgba(40, 167, 69, 0.1);
}

.certificate-stats {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}

.stat-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    flex: 1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #28a745;
}

/* DataTable Custom Styling */
.dataTables_wrapper {
    padding: 0 !important;
}

.dataTables_filter {
    display: none; /* Hide default search - we use custom */
}

.dataTables_length {
    float: left;
    margin-bottom: 20px;
}

.dataTables_info {
    float: left;
    margin-top: 10px;
}

.dataTables_paginate {
    float: right;
    margin-top: 10px;
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
}

table.dataTable tbody tr:nth-child(even) {
    background: #f8f9fa !important;
}

table.dataTable tbody tr:hover {
    background: #e8f5e8 !important;
}

table.dataTable tbody td {
    padding: 12px !important;
    border-bottom: 1px solid #dee2e6 !important;
    vertical-align: middle !important;
}

.dataTables_processing {
    background: rgba(40, 167, 69, 0.1) !important;
    color: #28a745 !important;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="text-muted mb-0">Manage student certificates</h4>
                </div>
                <a href="{{ route('admin.certificates.create') }}" class="btn btn-success">
                    <i class="fas fa-plus mr-2"></i>Create Certificate
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="certificate-stats">
        <div class="stat-card">
            <div class="stat-number" id="requestedCount">0</div>
            <div class="text-muted">Requested</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="approvedCount">0</div>
            <div class="text-muted">Approved</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="issuedCount">0</div>
            <div class="text-muted">Issued</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="totalCount">0</div>
            <div class="text-muted">Total</div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="row">
            <div class="col-md-6">
                <label class="mb-2"><strong>Filter by Status:</strong></label>
                <div class="status-filter">
                    <div class="status-btn active" data-status="">All Status</div>
                    <div class="status-btn" data-status="requested">Requested</div>
                    <div class="status-btn" data-status="approved">Approved</div>
                    <div class="status-btn" data-status="issued">Issued</div>
                </div>
            </div>
            <div class="col-md-6">
                <label for="searchInput"><strong>Search Certificates:</strong></label>
                <input type="text" id="searchInput" class="form-control" 
                       placeholder="Search by certificate number, student name...">
            </div>
        </div>
    </div>

    <!-- Certificates DataTable -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold text-white">
                        <i class="fas fa-certificate mr-2"></i>All Certificates
                    </h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-light" onclick="refreshTable()" title="Refresh">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <button type="button" class="btn btn-light" onclick="exportCertificates()" title="Export">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- CORRECTED TABLE STRUCTURE -->
                    <table class="table table-bordered" id="certificatesTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>Certificate #</th>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Status</th>
                                <th>Issued Date</th>
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

$(document).ready(function() {
    // Initialize DataTable with proper Yajra configuration
    window.certificatesTable = $('#certificatesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('admin.certificates.index') }}",
            data: function (d) {
                d.status_filter = currentStatusFilter;
            },
            error: function(xhr, error, thrown) {
                console.log('DataTables AJAX Error:', xhr, error, thrown);
            }
        },
        columns: [
            { 
                data: 'certificate_number', 
                name: 'number',
                orderable: true,
                searchable: true
            },
            { 
                data: 'student_name', 
                name: 'student.name', 
                orderable: false,
                searchable: true
            },
            { 
                data: 'course_name', 
                name: 'course.name', 
                orderable: false,
                searchable: true
            },
            { 
                data: 'status', 
                name: 'status',
                orderable: true,
                searchable: false
            },
            { 
                data: 'issued_date', 
                name: 'issued_at',
                orderable: true,
                searchable: false
            },
            { 
                data: 'actions', 
                name: 'actions', 
                orderable: false, 
                searchable: false,
                className: 'text-center'
            }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        language: {
            processing: '<i class="fas fa-spinner fa-spin"></i> Loading certificates...',
            emptyTable: "No certificates found",
            zeroRecords: "No matching certificates found",
            info: "Showing _START_ to _END_ of _TOTAL_ certificates",
            infoEmpty: "Showing 0 to 0 of 0 certificates",
            infoFiltered: "(filtered from _MAX_ total certificates)",
            lengthMenu: "Show _MENU_ certificates per page",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        drawCallback: function() {
            updateStats();
        },
        initComplete: function() {
            console.log('DataTable initialized successfully');
            updateStats();
        }
    });

    // Status filter buttons
    $('.status-btn').click(function() {
        $('.status-btn').removeClass('active');
        $(this).addClass('active');
        currentStatusFilter = $(this).data('status');
        certificatesTable.ajax.reload();
    });

    // Custom search
    $('#searchInput').on('keyup', function() {
        certificatesTable.search(this.value).draw();
    });
});

function refreshTable() {
    if (certificatesTable) {
        certificatesTable.ajax.reload();
        updateStats();
        showToast('info', 'Table refreshed successfully!');
    }
}

function updateStats() {
    $.get("{{ route('admin.certificates.index') }}", { get_stats: true }, function(data) {
        if (data.stats) {
            $('#requestedCount').text(data.stats.requested || 0);
            $('#approvedCount').text(data.stats.approved || 0);
            $('#issuedCount').text(data.stats.issued || 0);
            $('#totalCount').text(data.stats.total || 0);
        }
    }).fail(function() {
        // Fallback - count from current table data
        if (certificatesTable) {
            let info = certificatesTable.page.info();
            $('#totalCount').text(info.recordsTotal || 0);
        }
    });
}

function approveRequest(certificateId) {
    if (!confirm('Are you sure you want to approve this certificate request?')) return;
    
    $.ajax({
        url: `/admin/certificates/${certificateId}/approve`,
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            certificatesTable.ajax.reload();
            showToast('success', response.message);
            updateStats();
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Error approving certificate';
            showToast('error', message);
        }
    });
}

function issueCertificate(certificateId) {
    if (!confirm('Are you sure you want to issue this certificate?')) return;
    
    $.ajax({
        url: `/admin/certificates/${certificateId}/issue`,
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            certificatesTable.ajax.reload();
            showToast('success', response.message);
            updateStats();
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Error issuing certificate';
            showToast('error', message);
        }
    });
}

function deleteCertificate(certificateId) {
    if (!confirm('Are you sure you want to delete this certificate?')) return;
    
    $.ajax({
        url: `/admin/certificates/${certificateId}`,
        type: 'DELETE',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            certificatesTable.ajax.reload();
            showToast('success', response.message);
            updateStats();
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Error deleting certificate';
            showToast('error', message);
        }
    });
}

function exportCertificates() {
    showToast('info', 'Export feature will be implemented soon!');
}

function showToast(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'error' ? 'alert-danger' : 'alert-info';
    
    const toast = $(`
        <div class="position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
            <div class="alert ${alertClass} alert-dismissible fade show">
                <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'}-circle mr-2"></i>
                ${message}
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
