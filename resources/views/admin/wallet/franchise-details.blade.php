@extends('layouts.custom-admin')

@section('page-title', 'Franchise Wallet Details')

@section('css')
<style>
    .wallet-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
    }
    .transaction-card {
        border-left: 4px solid;
        margin-bottom: 15px;
    }
    .transaction-card.credit { border-color: #28a745; }
    .transaction-card.debit { border-color: #dc3545; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Wallet Header -->
    <div class="wallet-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-2">{{ $franchise->name }}</h3>
                <p class="mb-0"><i class="fas fa-map-marker-alt mr-2"></i>{{ $franchise->city ?? 'N/A' }}</p>
            </div>
            <div class="text-right">
                <p class="mb-1 opacity-75">Current Balance</p>
                <h2 class="mb-0 font-weight-bold">₹{{ number_format($franchise->wallet->balance ?? 0, 2) }}</h2>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Total Credits</h6>
                    <h3 class="font-weight-bold text-success mb-0">₹{{ number_format($stats['total_credits'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Total Debits</h6>
                    <h3 class="font-weight-bold text-danger mb-0">₹{{ number_format($stats['total_debits'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Total Transactions</h6>
                    <h3 class="font-weight-bold text-primary mb-0">{{ $stats['transaction_count'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0 font-weight-bold">Transaction History</h5>
        </div>
        <div class="card-body">
            @forelse($transactions as $transaction)
            <div class="card transaction-card {{ $transaction->type }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">
                                @if($transaction->type === 'credit')
                                    <span class="badge badge-success"><i class="fas fa-arrow-up"></i> Credit</span>
                                @else
                                    <span class="badge badge-danger"><i class="fas fa-arrow-down"></i> Debit</span>
                                @endif
                            </h6>
                            <p class="mb-1">{{ $transaction->description }}</p>
                            <small class="text-muted">{{ $transaction->created_at->format('M d, Y g:i A') }}</small>
                        </div>
                        <div class="text-right">
                            <h4 class="mb-1 font-weight-bold {{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->type === 'credit' ? '+' : '-' }}₹{{ number_format($transaction->amount, 2) }}
                            </h4>
                            <small class="text-muted">Balance: ₹{{ number_format($transaction->balance_after, 2) }}</small>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-center text-muted py-5">No transactions found</p>
            @endforelse
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('admin.wallet.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>
    </div>
</div>
@endsection
