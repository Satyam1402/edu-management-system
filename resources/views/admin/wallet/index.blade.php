@extends('layouts.custom-admin')

@section('page-title', 'Wallet Management Dashboard')

@section('css')
<style>
    .stat-card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
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
                    <h4 class="mb-1">Wallet Management</h4>
                    <p class="text-muted mb-0">Monitor and manage all franchise wallet transactions</p>
                </div>
                <div>
                    <a href="{{ route('admin.wallet.manual-transaction') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle mr-2"></i>Manual Transaction
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2">Total Balance</p>
                            <h3 class="mb-0 font-weight-bold">₹{{ number_format($stats['total_balance'], 2) }}</h3>
                            <small class="text-success"><i class="fas fa-arrow-up"></i> All Franchises</small>
                        </div>
                        <div class="stat-icon bg-primary text-white">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2">Total Credits</p>
                            <h3 class="mb-0 font-weight-bold text-success">₹{{ number_format($stats['total_credits'], 2) }}</h3>
                            <small class="text-muted">Lifetime</small>
                        </div>
                        <div class="stat-icon bg-success text-white">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2">Total Debits</p>
                            <h3 class="mb-0 font-weight-bold text-danger">₹{{ number_format($stats['total_debits'], 2) }}</h3>
                            <small class="text-muted">Lifetime</small>
                        </div>
                        <div class="stat-icon bg-danger text-white">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2">Pending Recharges</p>
                            <h3 class="mb-0 font-weight-bold text-warning">{{ $stats['pending_recharges'] }}</h3>
                            <small class="text-muted">Awaiting approval</small>
                        </div>
                        <div class="stat-icon bg-warning text-white">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title font-weight-bold mb-3">Quick Actions</h6>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.wallet.transactions') }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-list mr-2"></i>All Transactions
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.wallet.recharge-requests') }}" class="btn btn-outline-warning btn-block">
                                <i class="fas fa-inbox mr-2"></i>Recharge Requests
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.wallet.audit-logs') }}" class="btn btn-outline-info btn-block">
                                <i class="fas fa-history mr-2"></i>Audit Logs
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.wallet.manual-transaction') }}" class="btn btn-outline-success btn-block">
                                <i class="fas fa-plus mr-2"></i>Add Transaction
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Transactions -->
        <div class="col-lg-7 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 font-weight-bold">Recent Transactions</h6>
                        <a href="{{ route('admin.wallet.transactions') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Franchise</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $transaction)
                                <tr>
                                    <td>
                                        <strong>{{ $transaction->wallet->franchise->name }}</strong>
                                    </td>
                                    <td>
                                        @if($transaction->type === 'credit')
                                            <span class="badge badge-success"><i class="fas fa-arrow-up"></i> Credit</span>
                                        @else
                                            <span class="badge badge-danger"><i class="fas fa-arrow-down"></i> Debit</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="{{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                            {{ $transaction->type === 'credit' ? '+' : '-' }}₹{{ number_format($transaction->amount, 2) }}
                                        </strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $transaction->created_at->format('M d, g:i A') }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No recent transactions</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Franchises -->
        <div class="col-lg-5 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0 font-weight-bold">Top Wallet Balances</h6>
                </div>
                <div class="card-body">
                    @forelse($topFranchises as $franchise)
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <strong>{{ $franchise->franchise->name }}</strong><br>
                            <small class="text-muted">{{ $franchise->franchise->city ?? 'N/A' }}</small>
                        </div>
                        <div class="text-right">
                            <h5 class="mb-0 text-success font-weight-bold">₹{{ number_format($franchise->balance, 2) }}</h5>
                            <a href="{{ route('admin.wallet.franchise-details', $franchise->franchise_id) }}" class="btn btn-sm btn-link p-0">
                                View Details
                            </a>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center">No data available</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    console.log('Wallet Dashboard Loaded');
</script>
@endsection
