@extends('layouts.custom-admin')

@section('title', 'Wallet')
@section('page-title', 'Wallet Balance')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />
<style>
    .card {
        border-radius: 15px;
        box-shadow: 0 4px 14px #667eea16;
        border: none;
    }
    .card-header {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        border-radius: 15px 15px 0 0;
    }
    .wallet-stats {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 14px #667eea16;
    }
    .wallet-balance {
        font-size: 2.1rem;
        font-weight: bold;
    }
    .table th, .table td {
        vertical-align: middle !important;
    }
    .badge-credit { background: #d4edda; color: #155724; }
    .badge-debit { background: #f8d7da; color: #721c24; }
</style>
@endsection

@section('content')
<div class="container-fluid">

    {{-- WALLET STATS --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="wallet-stats">
                <div>Available Balance</div>
                <div class="wallet-balance">₹{{ number_format($wallet->balance, 2) }}</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="wallet-stats" style="background:linear-gradient(135deg,#11998e 0%,#38ef7d 100%);">
                <div>This Month</div>
                <div class="wallet-balance">₹{{ number_format($thisMonthWallet, 2) }}</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="wallet-stats" style="background:linear-gradient(135deg,#ff6a00 0%,#ee0979 100%);">
                <div>Total Credits</div>
                <div class="wallet-balance">+₹{{ number_format($totalCredits, 2) }}</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="wallet-stats" style="background:linear-gradient(135deg,#f857a6 0%,#ff5858 100%);">
                <div>Total Debits</div>
                <div class="wallet-balance">-₹{{ number_format($totalDebits, 2) }}</div>
            </div>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <a href="{{ route('franchise.payments.create') }}" class="btn btn-primary btn-lg w-100">
                <i class="fas fa-plus"></i> Add Funds
            </a>
        </div>
    </div>

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
                <i class="fas fa-wallet"></i> Wallet Transactions
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="walletTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Amount (₹)</th>
                            <th>Source</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $tx->description ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $tx->type === 'credit' ? 'badge-credit' : 'badge-debit' }}">
                                        {{ ucfirst($tx->type) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $tx->type === 'debit' ? '-' : '+' }}₹{{ number_format($tx->amount, 2) }}
                                </td>
                                <td>{{ ucfirst($tx->source) }}</td>
                                <td>{{ $tx->created_at->format('d M Y, h:i a') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No wallet transactions found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Pagination --}}
            <div class="mt-2">
                {!! $transactions->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    setTimeout(function() {
        $('.alert-dismissible').fadeOut('slow');
    }, 5000);
</script>
@endsection
