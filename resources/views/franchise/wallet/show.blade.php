@extends('layouts.custom-admin')

@section('title', 'Transaction Details')
@section('page-title', 'Transaction Details')

@section('css')
<style>
    .transaction-detail-card {
        border-radius: 10px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    }
    .detail-row {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
    }
    .detail-row:last-child {
        border-bottom: none;
    }
    .detail-label {
        font-weight: 600;
        color: #666;
        font-size: 14px;
        text-transform: uppercase;
    }
    .detail-value {
        font-size: 18px;
        color: #333;
        margin-top: 5px;
    }
    .status-completed {
        background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);
        color: white;
        padding: 10px 20px;
        border-radius: 50px;
        display: inline-block;
        font-weight: bold;
    }
    .status-pending {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 10px 20px;
        border-radius: 50px;
        display: inline-block;
        font-weight: bold;
    }
    .status-failed {
        background: linear-gradient(135deg, #ff512f 0%, #dd2476 100%);
        color: white;
        padding: 10px 20px;
        border-radius: 50px;
        display: inline-block;
        font-weight: bold;
    }
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 20px;
    }
    .timeline-item:before {
        content: '';
        position: absolute;
        left: -23px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #007bff;
    }
    .timeline-item:after {
        content: '';
        position: absolute;
        left: -18px;
        top: 17px;
        width: 2px;
        height: 100%;
        background: #dee2e6;
    }
    .timeline-item:last-child:after {
        display: none;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Transaction Summary Card -->
        <div class="col-md-8">
            <div class="card transaction-detail-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-receipt"></i> Transaction #{{ $transaction->id }}
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Amount & Status -->
                    <div class="row mb-4">
                        <div class="col-md-6 text-center">
                            <div class="detail-label">Amount</div>
                            <div class="detail-value">
                                <h2 class="text-{{ $transaction->type === 'credit' ? 'success' : 'danger' }}">
                                    {{ $transaction->type === 'credit' ? '+' : '-' }} ₹{{ number_format($transaction->amount, 2) }}
                                </h2>
                            </div>
                        </div>
                        <div class="col-md-6 text-center">
                            <div class="detail-label">Status</div>
                            <div class="detail-value mt-2">
                                <span class="status-{{ $transaction->status ?? 'completed' }}">
                                    <i class="fas fa-{{ ($transaction->status ?? 'completed') === 'completed' ? 'check-circle' : (($transaction->status ?? 'completed') === 'pending' ? 'clock' : 'times-circle') }}"></i>
                                    {{ ucfirst($transaction->status ?? 'Completed') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Transaction Details -->
                    <div class="detail-row">
                        <div class="detail-label">Transaction Type</div>
                        <div class="detail-value">
                            <span class="badge badge-{{ $transaction->type === 'credit' ? 'success' : 'warning' }} badge-lg">
                                <i class="fas fa-arrow-{{ $transaction->type === 'credit' ? 'up' : 'down' }}"></i>
                                {{ ucfirst($transaction->type) }}
                            </span>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Source</div>
                        <div class="detail-value">
                            <i class="fas fa-{{ $transaction->source === 'wallet_topup' ? 'wallet' : 'certificate' }}"></i>
                            {{ ucfirst(str_replace('_', ' ', $transaction->source ?? 'N/A')) }}
                        </div>
                    </div>

                    @if($transaction->payment_method)
                    <div class="detail-row">
                        <div class="detail-label">Payment Method</div>
                        <div class="detail-value">
                            <i class="fas fa-{{ $transaction->payment_method === 'razorpay' ? 'bolt' : ($transaction->payment_method === 'upi' ? 'mobile' : 'university') }}"></i>
                            {{ ucfirst($transaction->payment_method) }}
                        </div>
                    </div>
                    @endif

                    <div class="detail-row">
                        <div class="detail-label">Transaction Date</div>
                        <div class="detail-value">
                            <i class="far fa-calendar-alt"></i>
                            {{ $transaction->created_at->format('F d, Y') }} at {{ $transaction->created_at->format('h:i A') }}
                        </div>
                    </div>

                    @if($transaction->completed_at)
                    <div class="detail-row">
                        <div class="detail-label">Completed At</div>
                        <div class="detail-value">
                            <i class="fas fa-check-circle text-success"></i>
                            {{ $transaction->completed_at->format('F d, Y') }} at {{ $transaction->completed_at->format('h:i A') }}
                        </div>
                    </div>
                    @endif

                    @if($transaction->reference_id)
                    <div class="detail-row">
                        <div class="detail-label">Reference ID</div>
                        <div class="detail-value">
                            <code>{{ $transaction->reference_id }}</code>
                        </div>
                    </div>
                    @endif

                    <!-- Meta Information (FIXED) -->
                    @if($transaction->meta && is_array($transaction->meta) && count($transaction->meta) > 0)
                    <div class="detail-row">
                        <div class="detail-label">Additional Details</div>
                        <div class="detail-value">
                            <ul class="list-unstyled mb-0">
                                @foreach($transaction->meta as $key => $value)
                                    <li><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ is_array($value) ? json_encode($value) : $value }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="card-footer bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('franchise.wallet.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Wallet
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            @if(($transaction->status ?? 'completed') === 'completed')
                                <a href="{{ route('franchise.wallet.receipt', $transaction->id) }}" class="btn btn-success" target="_blank">
                                    <i class="fas fa-download"></i> Download Receipt
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline & Activity Card -->
        <div class="col-md-4">
            <div class="card transaction-detail-card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history"></i> Transaction Timeline
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <strong>Transaction Initiated</strong>
                            <p class="text-muted mb-0">{{ $transaction->created_at->format('M d, Y h:i A') }}</p>
                        </div>

                        @if(($transaction->status ?? 'completed') === 'pending')
                        <div class="timeline-item">
                            <strong>Payment Pending</strong>
                            <p class="text-muted mb-0">Awaiting payment confirmation</p>
                        </div>
                        @endif

                        @if(($transaction->status ?? 'completed') === 'completed' && $transaction->completed_at)
                        <div class="timeline-item">
                            <strong>Payment Successful</strong>
                            <p class="text-muted mb-0">{{ $transaction->completed_at->format('M d, Y h:i A') }}</p>
                        </div>

                        <div class="timeline-item">
                            <strong>Wallet Updated</strong>
                            <p class="text-muted mb-0">Balance credited successfully</p>
                        </div>
                        @endif

                        @if(($transaction->status ?? 'completed') === 'failed')
                        <div class="timeline-item">
                            <strong>Payment Failed</strong>
                            <p class="text-muted mb-0 text-danger">Transaction could not be completed</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card mt-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('franchise.wallet.create') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-plus-circle"></i> Add More Funds
                    </a>
                    <a href="{{ route('franchise.wallet.index') }}" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-list"></i> View All Transactions
                    </a>
                    @if(($transaction->status ?? 'completed') === 'completed')
                    <button class="btn btn-outline-info btn-block" onclick="window.print()">
                        <i class="fas fa-print"></i> Print Receipt
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Auto-refresh for pending transactions
    @if(($transaction->status ?? 'completed') === 'pending')
    setTimeout(function() {
        location.reload();
    }, 30000); // Refresh every 30 seconds
    @endif
</script>
@endsection
