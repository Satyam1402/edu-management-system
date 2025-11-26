@extends('layouts.custom-admin')

@section('page-title', 'All Wallet Transactions')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap4.min.css">
<style>
    .filter-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-1">All Wallet Transactions</h4>
            <p class="text-muted mb-0">Complete transaction history for all franchises</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form id="filterForm">
            <div class="row">
                <div class="col-md-3">
                    <label>Franchise</label>
                    <select class="form-control" id="franchise_filter" name="franchise_id">
                        <option value="">All Franchises</option>
                        @foreach($franchises as $franchise)
                            <option value="{{ $franchise->id }}">{{ $franchise->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Transaction Type</label>
                    <select class="form-control" id="type_filter" name="type">
                        <option value="">All Types</option>
                        <option value="credit">Credit</option>
                        <option value="debit">Debit</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Status</label>
                    <select class="form-control" id="status_filter" name="status">
                        <option value="">All Status</option>
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from">
                </div>
                <div class="col-md-2">
                    <label>To Date</label>
                    <input type="date" class="form-control" id="date_to" name="date_to">
                </div>
                <div class="col-md-1">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-primary btn-block" onclick="applyFilters()">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="card">
        <div class="card-body">
            <table id="transactionsTable" class="table table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Franchise</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Balance After</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
<script>
let transactionsTable;

$(document).ready(function() {
    transactionsTable = $('#transactionsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.wallet.transactions") }}',
            data: function(d) {
                d.franchise_id = $('#franchise_filter').val();
                d.type = $('#type_filter').val();
                d.status = $('#status_filter').val();
                d.date_from = $('#date_from').val();
                d.date_to = $('#date_to').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'franchise_name', name: 'franchise_name', orderable: false },
            { data: 'type_badge', name: 'type', orderable: false },
            { data: 'amount_formatted', name: 'amount' },
            { data: 'balance_after_formatted', name: 'balance_after' },
            { data: 'description', name: 'description' },
            { data: 'status_badge', name: 'status', orderable: false },
            { data: 'date_formatted', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true
    });
});

function applyFilters() {
    transactionsTable.ajax.reload();
}

function viewTransaction(id) {
    // Implement view transaction modal
    alert('View transaction: ' + id);
}
</script>
@endsection
