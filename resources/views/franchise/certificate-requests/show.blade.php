@extends('layouts.custom-admin')

@section('title', 'Certificate Request Details')
@section('page-title', 'Certificate Request')

@section('css')
<style>
    .detail-card {
        border-radius: 10px;
        overflow: hidden;
    }
    .status-timeline {
        position: relative;
        padding-left: 40px;
    }
    .status-timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e0e0e0;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 30px;
    }
    .timeline-marker {
        position: absolute;
        left: -32px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    .timeline-marker.active {
        animation: pulse 2s infinite;
    }
    .timeline-marker.completed {
        background: #28a745;
    }
    .timeline-marker.current {
        background: #ffc107;
    }
    .timeline-marker.pending {
        background: #6c757d;
    }
    .payment-action-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 30px;
        text-align: center;
    }
    .info-row {
        padding: 12px 0;
        border-bottom: 1px solid #e0e0e0;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Certificate Request #{{ $certificateRequest->id }}</h2>
            <p class="text-muted mb-0">View detailed information about this certificate request</p>
        </div>
        <a href="{{ route('franchise.certificate-requests.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    <div class="row">
        <!-- Left Column - Main Information -->
        <div class="col-lg-8">
            <!-- Status Card -->
            <div class="card shadow mb-4 detail-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle mr-2"></i>Request Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="text-muted mb-2">Current Status</h6>
                            {!! $certificateRequest->status_badge !!}
                        </div>
                        <div class="text-right">
                            <h6 class="text-muted mb-2">Payment Status</h6>
                            {!! $certificateRequest->payment_status_badge !!}
                        </div>
                    </div>

                    @if($certificateRequest->status === 'rejected')
                        <div class="alert alert-danger mt-3">
                            <h6 class="alert-heading">
                                <i class="fas fa-times-circle mr-2"></i>Request Rejected
                            </h6>
                            <p class="mb-0">
                                <strong>Reason:</strong>
                                {{ $certificateRequest->rejection_reason ?? 'Not specified' }}
                            </p>
                            @if($certificateRequest->admin_notes)
                                <hr>
                                <p class="mb-0">
                                    <strong>Admin Notes:</strong> {{ $certificateRequest->admin_notes }}
                                </p>
                            @endif
                        </div>
                    @endif

                    @if($certificateRequest->status === 'completed')
                        <div class="alert alert-success mt-3">
                            <h6 class="alert-heading">
                                <i class="fas fa-check-circle mr-2"></i>Certificate Issued
                            </h6>
                            <p class="mb-2">
                                <strong>Certificate Number:</strong>
                                {{ $certificateRequest->certificate_number }}
                            </p>
                            <p class="mb-0">
                                <strong>Issued Date:</strong>
                                {{ $certificateRequest->issued_date ? $certificateRequest->issued_date->format('d M Y') : 'N/A' }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Student Information -->
            <div class="card shadow mb-4 detail-card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-graduate mr-2"></i>Student Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-row d-flex justify-content-between">
                        <strong>Student Name:</strong>
                        <span>{{ $certificateRequest->student->full_name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row d-flex justify-content-between">
                        <strong>Email:</strong>
                        <span>{{ $certificateRequest->student->email ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row d-flex justify-content-between">
                        <strong>Phone:</strong>
                        <span>{{ $certificateRequest->student->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row d-flex justify-content-between">
                        <strong>Student ID:</strong>
                        <span>{{ $certificateRequest->student->student_id ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Course Information -->
            <div class="card shadow mb-4 detail-card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-book mr-2"></i>Course Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-row d-flex justify-content-between">
                        <strong>Course Name:</strong>
                        <span>{{ $certificateRequest->course->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row d-flex justify-content-between">
                        <strong>Course Code:</strong>
                        <span>{{ $certificateRequest->course->code ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row d-flex justify-content-between">
                        <strong>Certificate Type:</strong>
                        <span>{{ $certificateRequest->certificate_type }}</span>
                    </div>
                    @if($certificateRequest->notes)
                        <div class="info-row">
                            <strong class="d-block mb-2">Additional Notes:</strong>
                            <p class="text-muted mb-0">{{ $certificateRequest->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Timeline -->
            <div class="card shadow mb-4 detail-card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history mr-2"></i>Request Timeline
                    </h5>
                </div>
                <div class="card-body">
                    <div class="status-timeline">
                        <!-- Request Submitted -->
                        <div class="timeline-item">
                            <div class="timeline-marker completed">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Request Submitted</h6>
                                <p class="text-muted small mb-0">
                                    {{ $certificateRequest->requested_at
                                        ? $certificateRequest->requested_at->format('d M Y, h:i A')
                                        : $certificateRequest->created_at->format('d M Y, h:i A') }}
                                </p>
                            </div>
                        </div>

                        <!-- Admin Review -->
                        @if($certificateRequest->status !== 'pending')
                            <div class="timeline-item">
                                <div class="timeline-marker {{ $certificateRequest->status === 'rejected' ? 'bg-danger' : 'completed' }}">
                                    <i class="fas {{ $certificateRequest->status === 'rejected' ? 'fa-times' : 'fa-check' }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">
                                        {{ $certificateRequest->status === 'rejected' ? 'Request Rejected' : 'Request Approved' }}
                                    </h6>
                                    <p class="text-muted small mb-1">
                                        @if($certificateRequest->approved_at)
                                            {{ $certificateRequest->approved_at->format('d M Y, h:i A') }}
                                        @elseif($certificateRequest->rejected_at)
                                            {{ $certificateRequest->rejected_at->format('d M Y, h:i A') }}
                                        @endif
                                    </p>
                                    @if($certificateRequest->approvedBy)
                                        <p class="text-muted small mb-0">
                                            By: {{ $certificateRequest->approvedBy->name }}
                                        </p>
                                    @endif
                                    @if($certificateRequest->rejectedBy)
                                        <p class="text-muted small mb-0">
                                            By: {{ $certificateRequest->rejectedBy->name }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="timeline-item">
                                <div class="timeline-marker current active">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Pending Admin Review</h6>
                                    <p class="text-muted small mb-0">Waiting for admin approval</p>
                                </div>
                            </div>
                        @endif

                        <!-- Payment -->
                        @if($certificateRequest->payment_status === 'paid')
                            <div class="timeline-item">
                                <div class="timeline-marker completed">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Payment Completed</h6>
                                    <p class="text-muted small mb-0">
                                        {{ $certificateRequest->paid_at ? $certificateRequest->paid_at->format('d M Y, h:i A') : 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        @elseif($certificateRequest->status === 'approved')
                            <div class="timeline-item">
                                <div class="timeline-marker current active">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Awaiting Payment</h6>
                                    <p class="text-muted small mb-0">Payment confirmation required</p>
                                </div>
                            </div>
                        @endif

                        <!-- Certificate Generation -->
                        @if($certificateRequest->status === 'completed')
                            <div class="timeline-item">
                                <div class="timeline-marker completed">
                                    <i class="fas fa-certificate"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Certificate Generated</h6>
                                    <p class="text-muted small mb-0">
                                        {{ $certificateRequest->processed_at ? $certificateRequest->processed_at->format('d M Y, h:i A') : 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Actions & Payment -->
        <div class="col-lg-4">
            <!-- Payment Action Card (if approved and not paid) -->
            @if($certificateRequest->canBePaid())
                <div class="payment-action-card shadow-lg mb-4">
                    <i class="fas fa-money-bill-wave fa-4x mb-3" style="opacity: 0.8;"></i>
                    <h4 class="mb-3">Payment Required</h4>
                    <p class="mb-4">Your request has been approved! Complete the payment to proceed.</p>

                    <div class="bg-white text-dark rounded p-3 mb-4">
                        <h2 class="mb-0 text-success">
                            ₹{{ number_format($certificateRequest->amount, 2) }}
                        </h2>
                        <small class="text-muted">Certificate Fee</small>
                    </div>

                    <form action="{{ route('franchise.certificate-requests.pay', $certificateRequest) }}"
                          method="POST"
                          id="paymentForm">
                        @csrf
                        <button type="button"
                                class="btn btn-light btn-lg btn-block"
                                onclick="confirmPayment()">
                            <i class="fas fa-check-circle mr-2"></i>Pay Now
                        </button>
                    </form>

                    <small class="d-block mt-3" style="opacity: 0.8;">
                        <i class="fas fa-shield-alt mr-1"></i>Secure payment via wallet
                    </small>
                </div>
            @endif

            <!-- Download Certificate (if completed) -->
            @if($certificateRequest->status === 'completed')
                <div class="card shadow mb-4"
                     style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-certificate fa-4x mb-3" style="opacity: 0.8;"></i>
                        <h4 class="mb-3">Certificate Ready!</h4>
                        <p class="mb-4">Your certificate has been generated and is ready to download.</p>

                        <a href="{{ route('franchise.certificate-requests.download', $certificateRequest->id) }}"
                           class="btn btn-light btn-lg btn-block">
                            <i class="fas fa-download mr-2"></i>Download Certificate
                        </a>

                        <div class="bg-white text-dark rounded p-2 mt-3">
                            <small class="mb-0">
                                <strong>Certificate #:</strong> {{ $certificateRequest->certificate_number }}
                            </small>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Financial Details -->
            <div class="card shadow mb-4 detail-card">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-money-check-alt mr-2"></i>Financial Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="info-row d-flex justify-content-between">
                        <strong>Certificate Fee:</strong>
                        <span class="text-success font-weight-bold">
                            ₹{{ number_format($certificateRequest->amount, 2) }}
                        </span>
                    </div>
                    <div class="info-row d-flex justify-content-between">
                        <strong>Payment Status:</strong>
                        <span>
                            @if($certificateRequest->payment_status === 'paid')
                                <span class="badge badge-success">Paid</span>
                            @else
                                <span class="badge badge-warning">Pending</span>
                            @endif
                        </span>
                    </div>
                    @if($certificateRequest->paid_at)
                        <div class="info-row d-flex justify-content-between">
                            <strong>Paid On:</strong>
                            <span>{{ $certificateRequest->paid_at->format('d M Y') }}</span>
                        </div>
                    @endif
                    @if($certificateRequest->walletTransaction)
                        <div class="info-row d-flex justify-content-between">
                            <strong>Transaction ID:</strong>
                            <span class="small">{{ $certificateRequest->walletTransaction->id }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Request Information -->
            <div class="card shadow mb-4 detail-card">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-file-alt mr-2"></i>Request Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="info-row d-flex justify-content-between">
                        <strong>Request ID:</strong>
                        <span>#{{ $certificateRequest->id }}</span>
                    </div>
                    <div class="info-row d-flex justify-content-between">
                        <strong>Submitted:</strong>
                        <span>{{ $certificateRequest->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="info-row d-flex justify-content-between">
                        <strong>Last Updated:</strong>
                        <span>{{ $certificateRequest->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmPayment() {
    const amount = {{ $certificateRequest->amount }};
    const walletBalance = {{ Auth::user()->franchise->wallet->balance ?? 0 }};
    const balanceAfter = walletBalance - amount;

    if (balanceAfter < 0) {
        Swal.fire({
            icon: 'error',
            title: 'Insufficient Balance',
            html: `
                <p>Your current wallet balance is <strong>₹${walletBalance.toFixed(2)}</strong></p>
                <p>Required amount: <strong>₹${amount.toFixed(2)}</strong></p>
                <p class="text-danger">You need to add <strong>₹${Math.abs(balanceAfter).toFixed(2)}</strong> more to your wallet.</p>
            `,
            confirmButtonText: 'Add Funds',
            showCancelButton: true,
            confirmButtonColor: '#28a745'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('franchise.wallet.index') }}";
            }
        });
        return;
    }

    Swal.fire({
        title: 'Confirm Payment',
        html: `
            <div class="text-left">
                <p><strong>Student:</strong> {{ $certificateRequest->student->full_name }}</p>
                <p><strong>Course:</strong> {{ $certificateRequest->course->name }}</p>
                <hr>
                <p><strong>Amount to Pay:</strong> <span class="text-danger">₹${amount.toFixed(2)}</span></p>
                <p><strong>Current Balance:</strong> ₹${walletBalance.toFixed(2)}</p>
                <p><strong>Balance After:</strong> <span class="text-success">₹${balanceAfter.toFixed(2)}</span></p>
            </div>
            <div class="alert alert-warning mt-3">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                This action will deduct ₹${amount.toFixed(2)} from your wallet.
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Pay Now',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Processing Payment...',
                html: 'Please wait while we process your payment.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            document.getElementById('paymentForm').submit();
        }
    });
}
</script>
@endsection
