@extends('layouts.custom-admin')

@section('page-title', 'Certificate Request Management')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
<style>
    .status-badge {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-weight: 600;
    }
    .student-info {
        line-height: 1.4;
    }
    .amount-info {
        text-align: center;
    }
    .btn-group-actions .btn {
        margin: 0 2px;
        border-radius: 4px;
    }
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: white;
        border-bottom: none;
    }
    .stats-card {
        border-radius: 10px;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }
    .stats-card:hover {
        transform: translateY(-5px);
    }
    .bulk-actions {
        display: none;
        margin-bottom: 1rem;
    }
    .bulk-actions.show {
        display: block;
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
                    <h4 class="text-muted mb-0">Manage and approve certificate requests</h4>
                </div>
                <div class="d-flex">
                    <button type="button" class="btn btn-secondary mr-2" onclick="refreshTable()">
                        <i class="fas fa-sync-alt mr-1"></i>Refresh
                    </button>
                    <button type="button" class="btn btn-info" onclick="exportRequests()">
                        <i class="fas fa-download mr-1"></i>Export
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card stats-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                            <p class="mb-0">Total Requests</p>
                        </div>
                        <div class="text-primary-50">
                            <i class="fas fa-clipboard-list fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card stats-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                            <p class="mb-0">Pending Approval</p>
                        </div>
                        <div class="text-warning-50">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card stats-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['approved'] }}</h3>
                            <p class="mb-0">Approved</p>
                        </div>
                        <div class="text-success-50">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card stats-card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['rejected'] }}</h3>
                            <p class="mb-0">Rejected</p>
                        </div>
                        <div class="text-danger-50">
                            <i class="fas fa-times-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="bulk-actions" id="bulk-actions">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-0"><span id="selected-count">0</span> requests selected</p>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn-success btn-sm mr-2" onclick="bulkApprove()">
                            <i class="fas fa-check"></i> Bulk Approve
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="showBulkRejectModal()">
                            <i class="fas fa-times"></i> Bulk Reject
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm ml-2" onclick="clearSelection()">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Certificate Requests DataTable -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold text-white">
                        <i class="fas fa-certificate mr-2"></i>All Certificate Requests
                    </h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-light" onclick="refreshTable()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <button type="button" class="btn btn-light dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" onclick="filterByStatus('all')">All Requests</a>
                            <a class="dropdown-item" href="#" onclick="filterByStatus('pending')">Pending Only</a>
                            <a class="dropdown-item" href="#" onclick="filterByStatus('approved')">Approved Only</a>
                            <a class="dropdown-item" href="#" onclick="filterByStatus('rejected')">Rejected Only</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover mb-0" id="certificateRequestsTable">
                        <thead>
                            <tr>
                                <th width="50">
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th width="250">Student & Franchise</th>
                                <th width="150">Course</th>
                                <th width="120">Amount</th>
                                <th width="100">Status</th>
                                <th width="120">Request Date</th>
                                <th width="150" class="text-center">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Approve Certificate Request</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to approve this certificate request?</p>
                <div class="form-group">
                    <label for="approve-notes">Admin Notes (Optional)</label>
                    <textarea class="form-control" id="approve-notes" rows="2" 
                              placeholder="Add any notes..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="confirmApprove()">
                    <i class="fas fa-check"></i> Approve
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Reject Certificate Request</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="rejection-reason">Rejection Reason <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="rejection-reason" rows="3" 
                              placeholder="Provide reason for rejection..." required></textarea>
                </div>
                <div class="form-group">
                    <label for="reject-notes">Admin Notes (Optional)</label>
                    <textarea class="form-control" id="reject-notes" rows="2" 
                              placeholder="Add any additional notes..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmReject()">
                    <i class="fas fa-times"></i> Reject
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Reject Modal -->
<div class="modal fade" id="bulkRejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Bulk Reject Requests</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="bulk-rejection-reason">Rejection Reason <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="bulk-rejection-reason" rows="3"
                              placeholder="Provide reason for bulk rejection..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmBulkReject()">
                    <i class="fas fa-times"></i> Reject Selected
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

<script>
let currentRequestId = null;

$(document).ready(function() {
    window.certificateRequestsTable = $('#certificateRequestsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.certificate-requests.index') }}",
        columns: [
            {
                data: 'id',
                name: 'id',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return `<input type="checkbox" class="select-row" value="${data}">`;
                }
            },
            { data: 'student_info', name: 'student.name', orderable: false },
            { data: 'course_info', name: 'course.name', orderable: false },
            { data: 'amount_info', name: 'amount', orderable: false }, // ✅ FIXED!
            { data: 'status_badge', name: 'status', orderable: false },
            { data: 'request_date', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[5, 'desc']],
        responsive: true,
        pageLength: 25,
        language: {
            processing: '<i class="fas fa-spinner fa-spin fa-2x"></i><br>Loading...',
            emptyTable: "No certificate requests found",
            zeroRecords: "No matching requests found"
        },
        drawCallback: function() {
            updateBulkActions();
            $('[data-toggle="tooltip"]').tooltip();
        }
    });

    // Select all functionality
    $('#select-all').on('change', function() {
        $('.select-row').prop('checked', this.checked);
        updateBulkActions();
    });

    // Individual checkbox functionality
    $(document).on('change', '.select-row', function() {
        updateBulkActions();
    });
});

function updateBulkActions() {
    const selected = $('.select-row:checked').length;
    $('#selected-count').text(selected);

    if (selected > 0) {
        $('#bulk-actions').addClass('show');
    } else {
        $('#bulk-actions').removeClass('show');
    }

    const total = $('.select-row').length;
    $('#select-all').prop('checked', selected === total && total > 0);
}

function clearSelection() {
    $('.select-row, #select-all').prop('checked', false);
    updateBulkActions();
}

function refreshTable() {
    certificateRequestsTable.ajax.reload();
    showToast('success', 'Table refreshed');
}

function filterByStatus(status) {
    if (status === 'all') {
        certificateRequestsTable.search('').draw();
    } else {
        certificateRequestsTable.search(status).draw();
    }
}

// ✅ FIXED: Individual approve
function showApproveModal(requestId) {
    currentRequestId = requestId;
    $('#approveModal').modal('show');
}

function confirmApprove() {
    const notes = $('#approve-notes').val();
    
    $.post(`/admin/certificate-requests/${currentRequestId}/approve`, {
        _token: '{{ csrf_token() }}',
        notes: notes
    }).done(function(response) {
        if (response.success) {
            $('#approveModal').modal('hide');
            showToast('success', response.message);
            certificateRequestsTable.ajax.reload();
        } else {
            showToast('error', response.message);
        }
    }).fail(function(xhr) {
        showToast('error', xhr.responseJSON?.message || 'Error approving request');
    });
}

// ✅ FIXED: Individual reject
function showRejectModal(requestId) {
    currentRequestId = requestId;
    $('#rejectModal').modal('show');
}

function confirmReject() {
    const reason = $('#rejection-reason').val();
    const notes = $('#reject-notes').val();

    if (!reason.trim()) {
        showToast('error', 'Please provide a rejection reason');
        return;
    }

    $.post(`/admin/certificate-requests/${currentRequestId}/reject`, {
        _token: '{{ csrf_token() }}',
        reason: reason,
        notes: notes
    }).done(function(response) {
        if (response.success) {
            $('#rejectModal').modal('hide');
            $('#rejection-reason').val('');
            $('#reject-notes').val('');
            showToast('success', response.message);
            certificateRequestsTable.ajax.reload();
        } else {
            showToast('error', response.message);
        }
    }).fail(function(xhr) {
        showToast('error', xhr.responseJSON?.message || 'Error rejecting request');
    });
}

// Bulk approve
function bulkApprove() {
    const selected = getSelectedIds();
    if (selected.length === 0) {
        showToast('error', 'Please select requests to approve');
        return;
    }

    if (confirm(`Are you sure you want to approve ${selected.length} certificate request(s)?`)) {
        $.post("{{ route('admin.certificate-requests.bulk-action') }}", {
            _token: '{{ csrf_token() }}',
            action: 'approve',
            request_ids: selected
        }).done(function(response) {
            if (response.success) {
                showToast('success', response.message);
                certificateRequestsTable.ajax.reload();
                clearSelection();
            } else {
                showToast('error', response.message);
            }
        }).fail(function() {
            showToast('error', 'Error processing bulk approval');
        });
    }
}

// Bulk reject
function showBulkRejectModal() {
    const selected = getSelectedIds();
    if (selected.length === 0) {
        showToast('error', 'Please select requests to reject');
        return;
    }
    $('#bulkRejectModal').modal('show');
}

function confirmBulkReject() {
    const selected = getSelectedIds();
    const reason = $('#bulk-rejection-reason').val();

    if (!reason.trim()) {
        showToast('error', 'Please provide a rejection reason');
        return;
    }

    $.post("{{ route('admin.certificate-requests.bulk-action') }}", {
        _token: '{{ csrf_token() }}',
        action: 'reject',
        request_ids: selected,
        reason: reason
    }).done(function(response) {
        if (response.success) {
            showToast('success', response.message);
            certificateRequestsTable.ajax.reload();
            clearSelection();
            $('#bulkRejectModal').modal('hide');
            $('#bulk-rejection-reason').val('');
        } else {
            showToast('error', response.message);
        }
    }).fail(function() {
        showToast('error', 'Error processing bulk rejection');
    });
}

function getSelectedIds() {
    return $('.select-row:checked').map(function() {
        return $(this).val();
    }).get();
}

function exportRequests() {
    window.location.href = "{{ route('admin.certificate-requests.export') }}";
}

function showToast(type, message) {
    const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';

    const toast = $(`
        <div class="position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
            <div class="alert ${bgClass} text-white alert-dismissible fade show shadow-lg">
                <i class="fas ${icon} mr-2"></i> ${message}
                <button type="button" class="close text-white" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        </div>
    `);

    $('body').append(toast);
    setTimeout(() => toast.find('.alert').alert('close'), 5000);
}

// Timeline modal (optional)
function showTimeline(requestId) {
    window.location.href = `/admin/certificate-requests/${requestId}`;
}
</script>
@endsection
