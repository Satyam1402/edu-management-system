@extends('layouts.custom-admin')

@section('page-title', 'Issued Certificates')

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

.certificate-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    text-align: center;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    border-left: 4px solid;
    transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.stat-card:nth-child(1) { border-left-color: #28a745; }
.stat-card:nth-child(2) { border-left-color: #17a2b8; }
.stat-card:nth-child(3) { border-left-color: #ffc107; }
.stat-card:nth-child(4) { border-left-color: #6f42c1; }

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: #28a745;
    margin-bottom: 8px;
}

.stat-label {
    color: #6c757d;
    font-size: 0.95rem;
    font-weight: 500;
}

/* DataTable Styling */
table.dataTable {
    border-collapse: separate !important;
    border-spacing: 0;
    width: 100% !important;
}

table.dataTable thead th {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    color: white !important;
    border: none !important;
    font-weight: 600 !important;
    padding: 15px 12px !important;
    text-align: left !important;
}

table.dataTable tbody tr {
    background: white !important;
    transition: background 0.2s;
}

table.dataTable tbody tr:nth-child(even) {
    background: #f8f9fa !important;
}

table.dataTable tbody tr:hover {
    background: #e8f5e9 !important;
}

table.dataTable tbody td {
    padding: 12px !important;
    border-bottom: 1px solid #dee2e6 !important;
    vertical-align: middle !important;
}

.btn-group-sm .btn {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.info-alert {
    background: #e7f3ff;
    border-left: 4px solid #007bff;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h4 class="text-muted mb-1">Issued Certificates</h4>
                    <small class="text-secondary">View and manage all issued student certificates</small>
                </div>
                <div class="mt-2 mt-md-0">
                    <a href="{{ route('admin.certificate-requests.index') }}" class="btn btn-primary mr-2">
                        <i class="fas fa-list-alt mr-2"></i>View Requests
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="info-alert">
        <i class="fas fa-info-circle mr-2"></i>
        <strong>Note:</strong> Certificates are automatically generated when franchise requests are approved. 
        To issue new certificates, please review 
        <a href="{{ route('admin.certificate-requests.index') }}" class="font-weight-bold">Certificate Requests</a>.
    </div>

    <!-- Quick Stats -->
    <div class="certificate-stats">
        <div class="stat-card">
            <div class="stat-number text-success" id="totalCount">{{ $stats['total'] ?? 0 }}</div>
            <div class="stat-label">Total Certificates</div>
        </div>
        <div class="stat-card">
            <div class="stat-number text-info" id="issuedCount">{{ $stats['issued'] ?? 0 }}</div>
            <div class="stat-label">Issued</div>
        </div>
        <div class="stat-card">
            <div class="stat-number text-warning" id="thisMonthCount">{{ $stats['this_month'] ?? 0 }}</div>
            <div class="stat-label">This Month</div>
        </div>
        <div class="stat-card">
            <div class="stat-number text-purple" id="thisWeekCount">{{ $stats['this_week'] ?? 0 }}</div>
            <div class="stat-label">This Week</div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="row align-items-end">
            <div class="col-md-4 mb-3 mb-md-0">
                <label for="franchiseFilter"><strong>Filter by Franchise:</strong></label>
                <select id="franchiseFilter" class="form-control">
                    <option value="">All Franchises</option>
                    @foreach($franchises as $franchise)
                        <option value="{{ $franchise->id }}">{{ $franchise->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <label for="dateFrom"><strong>From Date:</strong></label>
                <input type="date" id="dateFrom" class="form-control">
            </div>
            <div class="col-md-4">
                <label for="dateTo"><strong>To Date:</strong></label>
                <input type="date" id="dateTo" class="form-control">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label for="searchInput"><strong>Search Certificates:</strong></label>
                <input type="text" id="searchInput" class="form-control" 
                       placeholder="Search by certificate number, student name, course...">
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button type="button" class="btn btn-primary mr-2" onclick="applyFilters()">
                    <i class="fas fa-filter mr-1"></i>Apply Filters
                </button>
                <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                    <i class="fas fa-times mr-1"></i>Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Certificates DataTable -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold text-white">
                        <i class="fas fa-certificate mr-2"></i>All Issued Certificates
                    </h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-light" onclick="refreshTable()" title="Refresh">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                        <a href="{{ route('admin.certificates.export') }}" class="btn btn-light" title="Export CSV">
                            <i class="fas fa-file-csv"></i> Export
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-hover table-bordered" id="certificatesTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>Certificate #</th>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Franchise</th>
                                <th>Status</th>
                                <th>Issued By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTables will populate -->
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
let certificatesTable;

$(document).ready(function() {
    // Initialize DataTable
    certificatesTable = $('#certificatesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('admin.certificates.index') }}",
            data: function (d) {
                d.franchise_filter = $('#franchiseFilter').val();
                d.date_from = $('#dateFrom').val();
                d.date_to = $('#dateTo').val();
            },
            error: function(xhr, error, thrown) {
                console.error('DataTables Error:', xhr.responseText);
                showToast('error', 'Failed to load certificates');
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
                data: 'student_info', 
                name: 'student.name',
                orderable: false,
                searchable: true
            },
            { 
                data: 'course_info', 
                name: 'course.name',
                orderable: false,
                searchable: true
            },
            { 
                data: 'franchise_info', 
                name: 'franchise.name',
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
                data: 'issued_by', 
                name: 'issued_by',
                orderable: false,
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
            processing: '<div class="spinner-border text-success" role="status"><span class="sr-only">Loading...</span></div>',
            emptyTable: "No certificates issued yet",
            zeroRecords: "No matching certificates found",
            info: "Showing _START_ to _END_ of _TOTAL_ certificates",
            infoEmpty: "No certificates to display",
            infoFiltered: "(filtered from _MAX_ total)",
            lengthMenu: "Show _MENU_ per page"
        },
        drawCallback: function() {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });

    // Custom search
    $('#searchInput').on('keyup', function() {
        certificatesTable.search(this.value).draw();
    });
});

function applyFilters() {
    certificatesTable.ajax.reload();
    showToast('success', 'Filters applied successfully');
}

function clearFilters() {
    $('#franchiseFilter').val('');
    $('#dateFrom').val('');
    $('#dateTo').val('');
    $('#searchInput').val('');
    certificatesTable.search('').ajax.reload();
    showToast('info', 'Filters cleared');
}

function refreshTable() {
    certificatesTable.ajax.reload(null, false);
    showToast('success', 'Table refreshed');
}

function viewRequest(requestId) {
    window.location.href = `/admin/certificate-requests/${requestId}`;
}

function showToast(type, message) {
    const colors = {
        success: '#28a745',
        error: '#dc3545',
        info: '#17a2b8',
        warning: '#ffc107'
    };
    
    const icons = {
        success: 'check-circle',
        error: 'exclamation-triangle',
        info: 'info-circle',
        warning: 'exclamation-circle'
    };
    
    const toast = $(`
        <div class="position-fixed animate__animated animate__fadeInRight" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <div class="alert alert-dismissible fade show m-0" 
                 style="background: ${colors[type]}; color: white; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                <i class="fas fa-${icons[type]} mr-2"></i>
                <strong>${message}</strong>
                <button type="button" class="close text-white" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        </div>
    `);
    
    $('body').append(toast);
    setTimeout(() => {
        toast.addClass('animate__fadeOutRight');
        setTimeout(() => toast.remove(), 500);
    }, 3500);
}
</script>
@endsection
