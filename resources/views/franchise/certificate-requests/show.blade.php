@extends('layouts.custom-admin')

@section('page-title', 'Certificate Request Details')

@section('css')
<style>
    .card {
        border-radius: 15px;
        box-shadow: 0 4px 14px #667eea16;
        border: none;
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 8px 25px #667eea20;
    }
    .card-header {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border-radius: 15px 15px 0 0;
    }
    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: 2px solid transparent;
    }
    .status-pending {
        background-color: #fff3cd;
        color: #856404;
        border-color: #ffeaa7;
    }
    .status-approved {
        background-color: #d4edda;
        color: #155724;
        border-color: #c3e6cb;
    }
    .status-rejected {
        background-color: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
    }
    .status-completed {
        background-color: #cce5f7;
        color: #004085;
        border-color: #b8daff;
    }
    .info-section {
        background: linear-gradient(145deg, #f8f9fa 0%, #ffffff 100%);
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 20px;
        border: 1px solid #e9ecef;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .info-label {
        font-weight: 600;
        color: #495057;
        min-width: 180px;
        display: flex;
        align-items: center;
    }
    .info-label i {
        margin-right: 8px;
        color: #667eea;
    }
    .info-value {
        flex: 1;
        text-align: right;
        word-wrap: break-word;
    }
    .timeline {
        position: relative;
        padding-left: 35px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
        border-radius: 3px;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 25px;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -25px;
        top: 8px;
        width: 15px;
        height: 15px;
        border-radius: 50%;
        background: #667eea;
        border: 3px solid #fff;
        box-shadow: 0 0 0 3px #667eea20;
        z-index: 2;
    }
    .timeline-content {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-left: 4px solid #667eea;
    }
    .btn-custom {
        border-radius: 25px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .alert-custom {
        border-radius: 10px;
        border: none;
        padding: 15px 20px;
    }
    .quick-stats {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        margin-bottom: 20px;
    }
    .stat-item {
        margin-bottom: 10px;
    }
    .stat-item:last-child {
        margin-bottom: 0;
    }
    .stat-label {
        font-size: 12px;
        opacity: 0.8;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .stat-value {
        font-size: 18px;
        font-weight: 700;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    {{-- SUCCESS/ERROR MESSAGES --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show alert-custom">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show alert-custom">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            {{-- REQUEST DETAILS CARD --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-certificate"></i> Certificate Request #{{ $certificateRequest->id }}
                    </h5>
                    <span class="status-badge status-{{ $certificateRequest->status }}">
                        {{ ucfirst($certificateRequest->status) }}
                    </span>
                </div>
                <div class="card-body">

                    {{-- STUDENT INFORMATION --}}
                    <div class="info-section">
                        <h6 class="mb-3">
                            <i class="fas fa-user text-primary"></i> Student Information
                        </h6>
                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-id-card"></i> Name:
                            </span>
                            <span class="info-value">
                                <strong>{{ $certificateRequest->student->name }}</strong>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-envelope"></i> Email:
                            </span>
                            <span class="info-value">
                                <a href="mailto:{{ $certificateRequest->student->email }}" class="text-primary">
                                    {{ $certificateRequest->student->email }}
                                </a>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-phone"></i> Phone:
                            </span>
                            <span class="info-value">
                                {{ $certificateRequest->student->phone ?? 'Not provided' }}
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-calendar"></i> Enrolled:
                            </span>
                            <span class="info-value">
                                {{ $certificateRequest->student->created_at->format('M d, Y') }}
                            </span>
                        </div>
                    </div>

                    {{-- REQUEST INFORMATION --}}
                    <div class="info-section">
                        <h6 class="mb-3">
                            <i class="fas fa-file-alt text-info"></i> Request Information
                        </h6>
                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-award"></i> Certificate Type:
                            </span>
                            <span class="info-value">
                                {{ $certificateRequest->certificate_type ?? 'General Certificate' }}
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-book"></i> Course:
                            </span>
                            <span class="info-value">
                                {{ $certificateRequest->course->name ?? 'General Course' }}
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-clock"></i> Requested Date:
                            </span>
                            <span class="info-value">
                                {{ $certificateRequest->requested_at ? $certificateRequest->requested_at->format('M d, Y H:i A') : $certificateRequest->created_at->format('M d, Y H:i A') }}
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-info-circle"></i> Status:
                            </span>
                            <span class="info-value">
                                <span class="status-badge status-{{ $certificateRequest->status }}">
                                    {{ ucfirst($certificateRequest->status) }}
                                </span>
                            </span>
                        </div>
                        @if($certificateRequest->special_note)
                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-sticky-note"></i> Special Notes:
                            </span>
                            <span class="info-value">
                                <em>"{{ $certificateRequest->special_note }}"</em>
                            </span>
                        </div>
                        @endif
                    </div>

                    {{-- PAYMENT INFORMATION --}}
                    @if($certificateRequest->payment)
                    <div class="info-section">
                        <h6 class="mb-3">
                            <i class="fas fa-credit-card text-success"></i> Payment Information
                        </h6>
                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-hashtag"></i> Payment ID:
                            </span>
                            <span class="info-value">
                                <code>#{{ $certificateRequest->payment->id }}</code>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-rupee-sign"></i> Amount:
                            </span>
                            <span class="info-value">
                                <strong class="text-success">₹{{ number_format($certificateRequest->payment->amount, 2) }}</strong>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-credit-card"></i> Gateway:
                            </span>
                            <span class="info-value">
                                {{ ucfirst($certificateRequest->payment->gateway ?? 'Manual') }}
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-check-circle"></i> Payment Status:
                            </span>
                            <span class="info-value">
                                <span class="badge badge-success">
                                    {{ ucfirst($certificateRequest->payment->status) }}
                                </span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-calendar-check"></i> Payment Date:
                            </span>
                            <span class="info-value">
                                {{ $certificateRequest->payment->paid_at ? $certificateRequest->payment->paid_at->format('M d, Y H:i A') : $certificateRequest->payment->created_at->format('M d, Y H:i A') }}
                            </span>
                        </div>
                        @if($certificateRequest->payment->gateway_payment_id)
                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-receipt"></i> Transaction ID:
                            </span>
                            <span class="info-value">
                                odede>{{ $certificateRequest->payment->gateway_payment_id }}</code>
                            </span>
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="info-section">
                        <h6 class="mb-3">
                            <i class="fas fa-exclamation-triangle text-warning"></i> Payment Information
                        </h6>
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle"></i>
                            No payment information is associated with this certificate request.
                        </div>
                    </div>
                    @endif

                    {{-- ACTION BUTTONS --}}
                    <div class="mt-4">
                        <a href="{{ route('franchise.certificate-requests.index') }}" class="btn btn-secondary btn-custom">
                            <i class="fas fa-arrow-left"></i> Back to Requests
                        </a>

                        @if($certificateRequest->status === 'pending')
                            <button class="btn btn-outline-danger btn-custom ml-2" onclick="confirmCancelRequest({{ $certificateRequest->id }})">
                                <i class="fas fa-times"></i> Cancel Request
                            </button>
                        @endif

                        @if($certificateRequest->status === 'approved')
                            <span class="btn btn-success btn-custom ml-2 disabled">
                                <i class="fas fa-check-circle"></i> Request Approved
                            </span>
                        @endif

                        @if($certificateRequest->status === 'completed')
                            <a href="#" class="btn btn-primary btn-custom ml-2" onclick="downloadCertificate({{ $certificateRequest->id }})">
                                <i class="fas fa-download"></i> Download Certificate
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            {{-- QUICK STATS --}}
            <div class="quick-stats">
                <div class="stat-item">
                    <div class="stat-label">Request ID</div>
                    <div class="stat-value">#{{ $certificateRequest->id }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Days Since Request</div>
                    <div class="stat-value">
                        {{ $certificateRequest->created_at->diffInDays(now()) }}
                    </div>
                </div>
                @if($certificateRequest->payment)
                <div class="stat-item">
                    <div class="stat-label">Amount Paid</div>
                    <div class="stat-value">₹{{ number_format($certificateRequest->payment->amount) }}</div>
                </div>
                @endif
            </div>

            {{-- STATUS TIMELINE --}}
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-history"></i> Request Timeline
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        {{-- REQUEST SUBMITTED --}}
                        <div class="timeline-item">
                            <div class="timeline-content">
                                <h6 class="mb-1">
                                    <i class="fas fa-paper-plane text-primary"></i> Request Submitted
                                </h6>
                                <p class="mb-1 text-muted small">Certificate request submitted for review</p>
                                <small class="text-primary font-weight-bold">
                                    {{ $certificateRequest->created_at->format('M d, Y H:i A') }}
                                </small>
                            </div>
                        </div>

                        {{-- STATUS BASED TIMELINE --}}
                        @if($certificateRequest->status === 'approved')
                        <div class="timeline-item">
                            <div class="timeline-content">
                                <h6 class="mb-1 text-success">
                                    <i class="fas fa-check-circle"></i> Request Approved
                                </h6>
                                <p class="mb-1 text-muted small">Admin approved your certificate request</p>
                                <small class="text-success font-weight-bold">
                                    {{ $certificateRequest->updated_at->format('M d, Y H:i A') }}
                                </small>
                            </div>
                        </div>
                        @elseif($certificateRequest->status === 'rejected')
                        <div class="timeline-item">
                            <div class="timeline-content">
                                <h6 class="mb-1 text-danger">
                                    <i class="fas fa-times-circle"></i> Request Rejected
                                </h6>
                                <p class="mb-1 text-muted small">Admin rejected your certificate request</p>
                                <small class="text-danger font-weight-bold">
                                    {{ $certificateRequest->updated_at->format('M d, Y H:i A') }}
                                </small>
                            </div>
                        </div>
                        @elseif($certificateRequest->status === 'completed')
                        <div class="timeline-item">
                            <div class="timeline-content">
                                <h6 class="mb-1 text-success">
                                    <i class="fas fa-certificate"></i> Certificate Generated
                                </h6>
                                <p class="mb-1 text-muted small">Certificate has been generated and is ready for download</p>
                                <small class="text-success font-weight-bold">
                                    {{ $certificateRequest->updated_at->format('M d, Y H:i A') }}
                                </small>
                            </div>
                        </div>
                        @else
                        <div class="timeline-item">
                            <div class="timeline-content">
                                <h6 class="mb-1 text-warning">
                                    <i class="fas fa-clock"></i> Under Review
                                </h6>
                                <p class="mb-1 text-muted small">Request is being reviewed by admin</p>
                                <small class="text-warning font-weight-bold">Pending...</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- QUICK ACTIONS --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-tools"></i> Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(Route::has('franchise.students.show'))
                        <a href="{{ route('franchise.students.show', $certificateRequest->student) }}" class="btn btn-outline-primary btn-sm btn-custom">
                            <i class="fas fa-user"></i> View Student Details
                        </a>
                        @endif

                        @if($certificateRequest->payment && Route::has('franchise.payments.show'))
                        <a href="{{ route('franchise.payments.show', $certificateRequest->payment) }}" class="btn btn-outline-success btn-sm btn-custom">
                            <i class="fas fa-receipt"></i> View Payment Receipt
                        </a>
                        @endif

                        <a href="{{ route('franchise.certificate-requests.create') }}" class="btn btn-outline-info btn-sm btn-custom">
                            <i class="fas fa-plus"></i> New Certificate Request
                        </a>

                        <a href="{{ route('franchise.payments.create') }}" class="btn btn-outline-warning btn-sm btn-custom">
                            <i class="fas fa-credit-card"></i> Make New Payment
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert-dismissible').fadeOut('slow');
    }, 5000);
});

function confirmCancelRequest(requestId) {
    if (confirm('Are you sure you want to cancel this certificate request? This action cannot be undone.')) {
        // You can implement the actual cancel functionality here
        // For now, just show a message
        alert('Cancel functionality will be implemented in the next update.');

        // Future implementation:
        // window.location.href = `/franchise/certificate-requests/${requestId}/cancel`;
    }
}

function downloadCertificate(requestId) {
    // Implement certificate download functionality
    alert('Certificate download functionality will be implemented when certificates are generated.');

    // Future implementation:
    // window.location.href = `/franchise/certificate-requests/${requestId}/download`;
}
</script>
@endsection
