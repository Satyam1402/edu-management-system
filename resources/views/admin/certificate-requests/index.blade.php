@extends('layouts.admin')

@section('page-title', 'Certificate Requests Management')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />
<style>
    .card {
        border-radius: 15px;
        box-shadow: 0 4px 14px #667eea16;
        border: none;
    }
    .card-header {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border-radius: 15px 15px 0 0;
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .stats-card {
        transition: transform 0.3s ease;
    }
    .stats-card:hover {
        transform: translateY(-5px);
    }
    .bulk-actions {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: none;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="pendingCount">{{ $requests->where('status', 'pending')->count() }}</h4>
                            <p class="mb-0">Pending Requests</p>
                        </div>
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="approvedCount">{{ $requests->where('status', 'approved')->count() }}</h4>
                            <p class="mb-0">Approved</p>
                        </div>
                        <i class="fas fa-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="rejectedCount">{{ $requests->where('status', 'rejected')->count() }}</h4>
                            <p class="mb-0">Rejected</p>
                        </div>
                        <i class="fas fa-times fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="totalCount">{{ $requests->count() }}</h4>
                            <p class="mb-0">Total Requests</p>
                        </div>
                        <i class="fas fa-certificate fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-certificate"></i> Certificate Requests Management
            </h5>
            <div>
                <button class="btn btn-success btn-sm" id="refreshData">
                    <i class="fas fa-sync"></i> Refresh
                </button>
            </div>
        </div>
        <div class="card-body">

            <!-- Bulk Actions Panel -->
            <div class="bulk-actions" id="bulkActions">
                <h6><i class="fas fa-tasks"></i> Bulk Actions</h6>
                <div class="d-flex justify-content-between align-items-center">
                    <span id="selectedCount">0 requests selected</span>
                    <div>
                        <button class="btn btn-success btn-sm" id="bulkApprove">
                            <i class="fas fa-check"></i> Approve Selected
                        </button>
                        <button class="btn btn-danger btn-sm" id="bulkReject">
                            <i class="fas fa-times"></i> Reject Selected
                        </button>
                        <button class="btn btn-secondary btn-sm" id="clearSelection">Clear</button>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <select class="form-control" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control" id="franchiseFilter">
                        <option value="">All Franchises</option>
                        @foreach($franchises as $franchise)
                            <option value="{{ $franchise->id }}">{{ $franchise->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" id="searchFilter" placeholder="Search by student name or email...">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-secondary w-100" id="clearFilters">Clear Filters</button>
                </div>
            </div>

            <!-- Requests Table -->
            <div class="table-responsive">
                <table class="table table-striped" id="requests-table">
                    <thead class="thead-light">
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>#</th>
                            <th>Franchise</th>
                            <th>Student</th>
                            <th>Certificate Type</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Request Date</th>
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

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Certificate Request</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="approveForm">
                    <div class="form-group">
                        <label>Certificate Title</label>
                        <input type="text" class="form-control" name="title" value="Certificate of Completion">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description" rows="3">This certifies that the student has successfully completed the requirements.</textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmApprove">Approve & Issue Certificate</button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Certificate Request</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="rejectForm">
                    <div class="form-group">
                        <label>Rejection Reason (Optional)</label>
                        <textarea class="form-control" name="rejection_reason" rows="3" placeholder="Enter reason for rejection..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmReject">Reject Request</button>
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
    let selectedRequests = [];
    let currentRequestId = null;

    // Initialize DataTable
    const table = $('#requests-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.certificate-requests.index") }}',
            data: function(d) {
                d.status = $('#statusFilter').val();
                d.franchise_id = $('#franchiseFilter').val();
                d.search = $('#searchFilter').val();
            }
        },
        columns: [
            { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'franchise_name', name: 'franchise.name' },
            { data: 'student_info', name: 'student.name' },
            { data: 'course.name', name: 'course.name', defaultContent: 'General Certificate' },
            { data: 'payment_status', name: 'payment_status', orderable: false },
            { data: 'status_badge', name: 'status' },
            { data: 'requested_at', name: 'requested_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[7, 'desc']], // Order by request date
        pageLength: 25
    });

    // Filter functionality
    $('#statusFilter, #franchiseFilter').on('change', function() {
        table.draw();
    });

    $('#searchFilter').on('keyup', function() {
        table.draw();
    });

    $('#clearFilters').on('click', function() {
        $('#statusFilter, #franchiseFilter, #searchFilter').val('');
        table.draw();
    });

    // Refresh data
    $('#refreshData').on('click', function() {
        table.ajax.reload();
    });

    // Selection handling
    $(document).on('change', '.request-checkbox', function() {
        const requestId = $(this).val();
        if ($(this).is(':checked')) {
            selectedRequests.push(requestId);
        } else {
            selectedRequests = selectedRequests.filter(id => id !== requestId);
        }
        updateBulkActions();
    });

    $('#selectAll').on('change', function() {
        $('.request-checkbox').prop('checked', $(this).is(':checked'));
        selectedRequests = $(this).is(':checked') ?
            $('.request-checkbox').map(function() { return $(this).val(); }).get() : [];
        updateBulkActions();
    });

    function updateBulkActions() {
        const count = selectedRequests.length;
        if (count > 0) {
            $('#bulkActions').show();
            $('#selectedCount').text(`${count} request${count > 1 ? 's' : ''} selected`);
        } else {
            $('#bulkActions').hide();
        }
    }

    // Individual actions
    window.approveRequest = function(id) {
        currentRequestId = id;
        $('#approveModal').modal('show');
    };

    window.rejectRequest = function(id) {
        currentRequestId = id;
        $('#rejectModal').modal('show');
    };

    // Confirm approve
    $('#confirmApprove').on('click', function() {
        const formData = $('#approveForm').serialize();
        $.post(`/admin/certificate-requests/${currentRequestId}/approve`, formData)
            .done(function(response) {
                if (response.success) {
                    $('#approveModal').modal('hide');
                    table.ajax.reload();
                    showAlert('success', response.message);
                } else {
                    showAlert('error', response.message);
                }
            })
            .fail(function() {
                showAlert('error', 'An error occurred while approving the request.');
            });
    });

    // Confirm reject
    $('#confirmReject').on('click', function() {
        const formData = $('#rejectForm').serialize();
        $.post(`/admin/certificate-requests/${currentRequestId}/reject`, formData)
            .done(function(response) {
                if (response.success) {
                    $('#rejectModal').modal('hide');
                    table.ajax.reload();
                    showAlert('success', response.message);
                } else {
                    showAlert('error', response.message);
                }
            })
            .fail(function() {
                showAlert('error', 'An error occurred while rejecting the request.');
            });
    });

    // Bulk actions
    $('#bulkApprove').on('click', function() {
        if (confirm('Are you sure you want to approve all selected requests?')) {
            performBulkAction('approve');
        }
    });

    $('#bulkReject').on('click', function() {
        if (confirm('Are you sure you want to reject all selected requests?')) {
            performBulkAction('reject');
        }
    });

    function performBulkAction(action) {
        $.post('{{ route("admin.certificate-requests.bulk-action") }}', {
            action: action,
            requests: selectedRequests,
            _token: '{{ csrf_token() }}'
        })
        .done(function(response) {
            if (response.success) {
                selectedRequests = [];
                $('#bulkActions').hide();
                table.ajax.reload();
                showAlert('success', response.message);
            } else {
                showAlert('error', response.message);
            }
        })
        .fail(function() {
            showAlert('error', 'An error occurred while processing the requests.');
        });
    }

    $('#clearSelection').on('click', function() {
        selectedRequests = [];
        $('.request-checkbox, #selectAll').prop('checked', false);
        $('#bulkActions').hide();
    });

    function showAlert(type, message) {
        // Implement your alert system here
        alert(message);
    }
});
</script>
@endsection
