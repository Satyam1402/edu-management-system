@extends('layouts.custom-admin')

@section('page-title', 'Wallet Recharge Requests')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<style>
    .stats-card {
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        border: 2px solid;
        margin-bottom: 20px;
    }
    .stats-pending { border-color: #ffc107; background: #fff8e1; }
    .stats-approved { border-color: #28a745; background: #e8f5e9; }
    .stats-rejected { border-color: #dc3545; background: #ffebee; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-1">Wallet Recharge Requests</h4>
            <p class="text-muted mb-0">Review and approve franchise wallet recharge requests</p>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stats-card stats-pending">
                <h2 class="mb-2 font-weight-bold text-warning">{{ $stats['pending'] }}</h2>
                <p class="mb-0 text-muted">Pending Requests</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card stats-approved">
                <h2 class="mb-2 font-weight-bold text-success">{{ $stats['approved'] }}</h2>
                <p class="mb-0 text-muted">Approved</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card stats-rejected">
                <h2 class="mb-2 font-weight-bold text-danger">{{ $stats['rejected'] }}</h2>
                <p class="mb-0 text-muted">Rejected</p>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label>Filter by Status</label>
                    <select class="form-control" id="statusFilter">
                        <option value="">All Requests</option>
                        <option value="pending">Pending</option>
                        <option value="verified">Verified</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button class="btn btn-primary btn-block" onclick="applyFilter()">
                        <i class="fas fa-filter"></i> Apply
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recharge Requests Table -->
    <div class="card">
        <div class="card-body">
            <table id="rechargeRequestsTable" class="table table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Franchise</th>
                        <th>Amount</th>
                        <th>Payment Info</th>
                        <th>Status</th>
                        <th>Requested Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Approve Recharge Request</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="approveForm">
                <div class="modal-body">
                    <input type="hidden" id="approve_request_id">
                    <p class="mb-3">Are you sure you want to approve this recharge request?</p>
                    <div class="form-group">
                        <label>Admin Remarks (Optional)</label>
                        <textarea class="form-control" id="approve_remarks" rows="3" placeholder="Add any remarks..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Reject Recharge Request</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <input type="hidden" id="reject_request_id">
                    <div class="form-group">
                        <label>Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reject_remarks" rows="4" placeholder="Enter rejection reason..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script>
let rechargeTable;

$(document).ready(function() {
    rechargeTable = $('#rechargeRequestsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.wallet.recharge-requests") }}',
            data: function(d) {
                d.status = $('#statusFilter').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'franchise_name', name: 'franchise.name' },
            { data: 'amount_formatted', name: 'amount' },
            { data: 'payment_info', name: 'payment_method', orderable: false },
            { data: 'status_badge', name: 'status', orderable: false },
            { data: 'requested_date', name: 'requested_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
    });
});

function applyFilter() {
    rechargeTable.ajax.reload();
}

function approveRecharge(id) {
    $('#approve_request_id').val(id);
    $('#approveModal').modal('show');
}

function rejectRecharge(id) {
    $('#reject_request_id').val(id);
    $('#rejectModal').modal('show');
}

$('#approveForm').submit(function(e) {
    e.preventDefault();
    const id = $('#approve_request_id').val();
    const remarks = $('#approve_remarks').val();
    
    $.ajax({
        url: `/admin/wallet/recharge-requests/${id}/approve`,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            admin_remarks: remarks
        },
        success: function(response) {
            $('#approveModal').modal('hide');
            toastr.success(response.message);
            rechargeTable.ajax.reload();
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Error occurred');
        }
    });
});

$('#rejectForm').submit(function(e) {
    e.preventDefault();
    const id = $('#reject_request_id').val();
    const remarks = $('#reject_remarks').val();
    
    if (!remarks) {
        toastr.error('Please enter rejection reason');
        return;
    }
    
    $.ajax({
        url: `/admin/wallet/recharge-requests/${id}/reject`,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            admin_remarks: remarks
        },
        success: function(response) {
            $('#rejectModal').modal('hide');
            toastr.success(response.message);
            rechargeTable.ajax.reload();
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Error occurred');
        }
    });
});
</script>
@endsection
