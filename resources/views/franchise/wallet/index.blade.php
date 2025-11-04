@extends('layouts.custom-admin')

@section('title', 'Wallet Transactions')
@section('page-title', 'Wallet Transactions')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />
<style>
    .card {
        border-radius: 15px;
        box-shadow: 0 4px 14px #667eea16;
        border: none;
    }
    .card-header {
        background: linear-gradient(135deg, #1cd8d2 0%, #93edc7 100%);
        color: white;
        border-radius: 15px 15px 0 0;
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-completed { background: #d4edda; color: #155724; }
    .badge-failed { background: #f8d7da; color: #721c24; }
    .badge-refunded { background: #d1ecf1; color: #0c5460; }

    .wallet-stats {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

    {{-- WALLET STATS --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="wallet-stats">
                <h4 id="totalWallet">₹0</h4>
                <p>Total Wallet Credits</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="wallet-stats">
                <h4 id="pendingWallet">0</h4>
                <p>Pending Top-ups</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="wallet-stats">
                <h4 id="thisMonthWallet">₹0</h4>
                <p>This Month</p>
            </div>
        </div>
    </div>

    {{-- WALLET TRANSACTION TABLE --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-wallet"></i> Wallet Transactions
                    </h5>
                    <div class="card-tools">
                        <button class="btn btn-light btn-sm" onclick="refreshWallet()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                        <a href="{{ route('franchise.wallet.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add Funds
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    {{-- SUCCESS/ERROR ALERTS --}}
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

                    {{-- TRANSACTION TABLE --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="walletTable">
                            <thead>
                                <tr>
                                    <th width="10%">ID</th>
                                    <th width="20%">Type</th>
                                    <th width="14%">Amount</th>
                                    <th width="14%">Method</th>
                                    <th width="14%">Status</th>
                                    <th width="14%">Date</th>
                                    <th width="14%">Action</th>
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
</div>
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#walletTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('franchise.wallet.index') }}",
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function(xhr, error, code) {
                alert('Error loading wallet transactions: ' + xhr.responseText);
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'type', name: 'type'},
            {data: 'formatted_amount', name: 'amount'},
            {data: 'payment_method', name: 'payment_method'},
            {data: 'status_badge', name: 'status'},
            {data: 'formatted_date', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[5, 'desc']],
        pageLength: 25,
        responsive: true,
        language: {
            processing: '<div class="spinner-border text-primary"><span class="sr-only">Loading...</span></div>',
            emptyTable: 'No wallet records found',
            zeroRecords: 'No matching wallet records found'
        }
    });

    window.refreshWallet = function() {
        table.ajax.reload();
        loadWalletStats();
    };

    function loadWalletStats() {
        // Implement live stats load if required
        $('#totalWallet').text('₹' + Math.floor(Math.random() * 100000));
        $('#pendingWallet').text(Math.floor(Math.random() * 50));
        $('#thisMonthWallet').text('₹' + Math.floor(Math.random() * 50000));
    }

    setTimeout(function() {
        $('.alert-dismissible').fadeOut('slow');
    }, 5000);
});
</script>
@endsection
