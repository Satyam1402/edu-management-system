{{-- resources/views/admin/payments/index.blade.php --}}
@extends('layouts.custom-admin')

@section('title', 'Payments')
@section('page-title', 'Payment Management')

@section('content')
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card text-white" style="background: linear-gradient(45deg, #28a745, #1e7e34);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">₹{{ number_format($totalRevenue) }}</h4>
                            <p class="mb-0">Total Revenue</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-rupee-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card text-white" style="background: linear-gradient(45deg, #ffc107, #d39e00);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">₹{{ number_format($pendingPayments) }}</h4>
                            <p class="mb-0">Pending Payments</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card text-white" style="background: linear-gradient(45deg, #17a2b8, #138496);">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ \App\Models\Payment::count() }}</h4>
                            <p class="mb-0">Total Transactions</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-credit-card fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow" style="border: none; border-radius: 10px;">
                <div class="card-header" style="background: linear-gradient(45deg, #28a745, #1e7e34); color: white; border-radius: 10px 10px 0 0;">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-credit-card mr-2"></i> All Payments
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.payments.create') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-plus mr-1"></i> Add Payment
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead style="background: #f8f9fa;">
                                <tr>
                                    <th class="pl-4">Payment ID</th>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Amount</th>
                                    <th>Payment Type</th>
                                    <th>Method</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="pr-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                <tr>
                                    <td class="pl-4">
                                        <strong class="text-primary">{{ $payment->payment_id }}</strong>
                                    </td>
                                    <td>
                                        <div>
                                            <strong class="text-dark">{{ $payment->student->name ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">{{ $payment->student->student_id ?? 'N/A' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info px-2 py-1">{{ $payment->course->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-success">₹{{ number_format($payment->amount) }}</strong>
                                    </td>
                                    <td>
                                        <span class="text-capitalize">{{ str_replace('_', ' ', $payment->payment_type) }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $methodIcons = [
                                                'cash' => 'fas fa-money-bill-wave text-success',
                                                'card' => 'fas fa-credit-card text-primary',
                                                'upi' => 'fas fa-mobile-alt text-warning',
                                                'bank_transfer' => 'fas fa-university text-info'
                                            ];
                                        @endphp
                                        <i class="{{ $methodIcons[$payment->payment_method] ?? 'fas fa-money-bill' }} mr-1"></i>
                                        {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                    </td>
                                    <td>
                                        <div class="text-muted">
                                            {{ $payment->payment_date->format('M d, Y') }}<br>
                                            <small>{{ $payment->payment_date->format('h:i A') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'completed' => 'success',
                                                'pending' => 'warning',
                                                'failed' => 'danger',
                                                'refunded' => 'info'
                                            ];
                                        @endphp
                                        <span class="badge badge-{{ $statusColors[$payment->status] ?? 'secondary' }} px-3 py-1">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td class="pr-4">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-info btn-sm" title="View Receipt">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                            @if($payment->status === 'pending')
                                            <button type="button" class="btn btn-success btn-sm" title="Mark Complete" onclick="updatePaymentStatus({{ $payment->id }}, 'completed')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-credit-card fa-3x mb-3"></i>
                                            <h5>No Payments Found</h5>
                                            <p>Start recording payments from students!</p>
                                            <a href="{{ route('admin.payments.create') }}" class="btn btn-success">
                                                <i class="fas fa-plus mr-1"></i> Add Payment
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function updatePaymentStatus(paymentId, status) {
            if (confirm('Are you sure you want to update this payment status?')) {
                // Implementation for updating payment status
                alert('Payment status update feature coming soon!');
            }
        }
    </script>
@endsection
