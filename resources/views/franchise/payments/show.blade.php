@extends('layouts.custom-admin')

@section('page-title', 'Payment Details')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-receipt"></i> Payment Details
                    </h5>
                    <div class="card-tools">
                        @if($payment->status == 'pending')
                            <a href="{{ route('franchise.payments.pay', $payment->id) }}"
                               class="btn btn-success btn-sm">
                                <i class="fas fa-credit-card"></i> Complete Payment
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-user"></i> Student Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $payment->student->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $payment->student->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $payment->student->phone ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6><i class="fas fa-credit-card"></i> Payment Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Order ID:</strong></td>
                                    <td>{{ $payment->order_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Amount:</strong></td>
                                    <td class="text-success"><strong>{{ $payment->formatted_amount }}</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $payment->status_badge }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Gateway:</strong></td>
                                    <td>{{ ucfirst($payment->gateway) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Course:</strong></td>
                                    <td>{{ $payment->course ? $payment->course->name : 'Certificate Fee' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                @if($payment->paid_at)
                                <tr>
                                    <td><strong>Paid At:</strong></td>
                                    <td>{{ $payment->paid_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if($payment->gateway_payment_id)
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6><i class="fas fa-info-circle"></i> Transaction Details</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Payment ID:</strong></td>
                                        <td>{{ $payment->gateway_payment_id }}</td>
                                    </tr>
                                    @if($payment->gateway_order_id)
                                    <tr>
                                        <td><strong>Gateway Order ID:</strong></td>
                                        <td>{{ $payment->gateway_order_id }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="row mt-3">
                        <div class="col-12">
                            <a href="{{ route('franchise.payments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Payments
                            </a>

                            @if($payment->status == 'completed')
                                <a href="{{ route('franchise.certificate-requests.create') }}" class="btn btn-primary ml-2">
                                    <i class="fas fa-certificate"></i> Request Certificate
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
