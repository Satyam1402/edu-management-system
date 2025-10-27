@extends('layouts.custom-admin')

@section('page-title', 'Certificate Requests')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />
<style>
    .card {
        border-radius: 15px;
        box-shadow: 0 4px 14px #667eea16;
        border: none;
    }
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-pending {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    .status-approved {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .status-rejected {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    .status-completed {
        background-color: #cce5f7;
        color: #004085;
        border: 1px solid #b8daff;
    }
    .filter-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .btn-group-custom .btn {
        margin-right: 5px;
    }
    .table th {
        background-color: #f1f3f4;
        color: #495057;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.5;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            {{-- SUCCESS/ERROR MESSAGES --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-certificate"></i> Certificate Requests
                    </h5>
                    <a href="{{ route('franchise.certificate-requests.create') }}" class="btn btn-light">
                        <i class="fas fa-plus"></i> New Request
                    </a>
                </div>
                <div class="card-body">

                    {{-- FILTER SECTION --}}
                    <div class="filter-card">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="statusFilter" class="form-label">
                                    <i class="fas fa-filter"></i> Filter by Status
                                </label>
                                <select class="form-control" id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="searchStudent" class="form-label">
                                    <i class="fas fa-search"></i> Search Student
                                </label>
                                <input type="text" class="form-control" id="searchStudent" placeholder="Search by student name...">
                            </div>
                            <div class="col-md-3">
                                <label for="dateRange" class="form-label">
                                    <i class="fas fa-calendar"></i> Date Range
                                </label>
                                <select class="form-control" id="dateRange">
                                    <option value="">All Time</option>
                                    <option value="today">Today</option>
                                    <option value="week">This Week</option>
                                    <option value="month">This Month</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button class="btn btn-secondary btn-block" id="clearFilters">
                                        <i class="fas fa-times"></i> Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- REQUESTS TABLE --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="requests-table">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="25%">Student</th>
                                    <th width="20%">Course</th>
                                    <th width="15%">Status</th>
                                    <th width="15%">Requested Date</th>
                                    <th width="10%">Payment</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- DataTables will populate this via AJAX --}}
                            </tbody>
                        </table>
                    </div>

                    {{-- EMPTY STATE (shown when no data) --}}
                    <div class="empty-state d-none" id="empty-state">
                        <i class="fas fa-certificate"></i>
                        <h4>No Certificate Requests Found</h4>
                        <p>You haven't made any certificate requests yet.</p>
                        <a href="{{ route('franchise.certificate-requests.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Your First Request
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable with AJAX
    var table = $('#requests-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('franchise.certificate-requests.index') }}",
            type: 'GET',
        },
        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'student_name',
                name: 'student.name',
                render: function(data, type, row) {
                    return '<div><strong>' + data + '</strong><br><small class="text-muted">' + row.student.email + '</small></div>';
                }
            },
            {
                data: 'course_name',
                name: 'course.name'
            },
            {
                data: 'status_badge',
                name: 'status',
                orderable: false,
                searchable: false
            },
            {
                data: 'requested_at',
                name: 'requested_at',
                render: function(data, type, row) {
                    if (data) {
                        var date = new Date(data);
                        return date.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    }
                    return 'N/A';
                }
            },
            {
                data: 'payment_status',
                name: 'payment_status',
                orderable: false,
                searchable: false
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],
        responsive: true,
        pageLength: 25,
        order: [[4, 'desc']], // Order by requested date (newest first)
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>',
            emptyTable: 'No certificate requests found',
            zeroRecords: 'No matching records found'
        },
        drawCallback: function(settings) {
            // Show/hide empty state
            if (settings.fnRecordsTotal() === 0) {
                $('#requests-table').hide();
                $('#empty-state').removeClass('d-none');
            } else {
                $('#requests-table').show();
                $('#empty-state').addClass('d-none');
            }
        }
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert-dismissible').fadeOut('slow');
    }, 5000);

    // Filter functionality
    $('#statusFilter').on('change', function() {
        table.column(3).search(this.value).draw();
    });

    $('#searchStudent').on('keyup', function() {
        table.column(1).search(this.value).draw();
    });

    $('#dateRange').on('change', function() {
        // You can implement date range filtering here
        var range = this.value;
        // For now, just log the selection
        console.log('Date range selected:', range);
    });

    $('#clearFilters').on('click', function() {
        $('#statusFilter').val('');
        $('#searchStudent').val('');
        $('#dateRange').val('');
        table.search('').columns().search('').draw();
    });

    // Tooltip initialization (if using Bootstrap tooltips)
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endsection
