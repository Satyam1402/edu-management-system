@extends('layouts.custom-admin')

@section('page-title', 'Certificate Request Details')

@section('css')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --danger-gradient: linear-gradient(135deg, #ff4757 0%, #ff3742 100%);
    }

    .main-card {
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        border: none;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .main-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }

    .card-header {
        background: var(--primary-gradient);
        color: white;
        border-radius: 20px 20px 0 0!important;
        padding: 2rem;
        border: none;
        position: relative;
        overflow: hidden;
    }

    .card-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 150px;
        height: 150px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transform: translate(50px, -50px);
    }

    .status-badge {
        padding: 12px 24px;
        border-radius: 30px;
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        position: relative;
        z-index: 2;
    }

    .status-pending {
        background: var(--warning-gradient);
        color: white;
        animation: pulse 2s infinite;
    }

    .status-processing {
        background: var(--info-gradient);
        color: white;
        animation: shimmer 1.5s infinite;
    }

    .status-approved {
        background: var(--success-gradient);
        color: white;
    }

    .status-rejected {
        background: var(--danger-gradient);
        color: white;
    }

    .status-completed {
        background: var(--primary-gradient);
        color: white;
        animation: glow 2s infinite alternate;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    @keyframes shimmer {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }

    @keyframes glow {
        0% { box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); }
        100% { box-shadow: 0 4px 25px rgba(102, 126, 234, 0.6); }
    }

    .info-section {
        background: linear-gradient(145deg, #f8f9fa 0%, #ffffff 100%);
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        border: 1px solid #e9ecef;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }

    .info-section:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .section-title {
        color: #2d3748;
        font-weight: 700;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.1rem;
    }

    .section-title i {
        padding: 8px;
        border-radius: 10px;
        background: var(--primary-gradient);
        color: white;
        box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 1rem 0;
        border-bottom: 1px solid #f0f4f8;
        transition: all 0.3s ease;
    }

    .info-row:hover {
        background-color: #f8f9ff;
        padding-left: 1rem;
        padding-right: 1rem;
        margin-left: -1rem;
        margin-right: -1rem;
        border-radius: 8px;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #4a5568;
        min-width: 200px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-label i {
        color: #667eea;
        width: 16px;
    }

    .info-value {
        flex: 1;
        text-align: right;
        word-wrap: break-word;
        font-weight: 500;
    }

    .sidebar-card {
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        border: none;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .quick-stats {
        background: var(--primary-gradient);
        color: white;
        padding: 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .quick-stats::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transform: translate(30px, -30px);
    }

    .stat-item {
        margin-bottom: 1.2rem;
        position: relative;
        z-index: 2;
    }

    .stat-item:last-child {
        margin-bottom: 0;
    }

    .stat-label {
        font-size: 0.85rem;
        opacity: 0.8;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    /* Timeline Enhancements */
    .timeline {
        position: relative;
        padding-left: 40px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 0;
        bottom: 0;
        width: 4px;
        background: var(--primary-gradient);
        border-radius: 4px;
        box-shadow: 0 0 10px rgba(102, 126, 234, 0.3);
    }

    .timeline-item {
        position: relative;
        margin-bottom: 2rem;
        animation: slideInLeft 0.6s ease-out;
        animation-fill-mode: both;
    }

    .timeline-item:nth-child(1) { animation-delay: 0.1s; }
    .timeline-item:nth-child(2) { animation-delay: 0.2s; }
    .timeline-item:nth-child(3) { animation-delay: 0.3s; }
    .timeline-item:nth-child(4) { animation-delay: 0.4s; }

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .timeline-icon {
        position: absolute;
        left: -30px;
        top: 12px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--primary-gradient);
        border: 4px solid #fff;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.2);
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .timeline-icon.success {
        background: var(--success-gradient);
        box-shadow: 0 0 0 4px rgba(17, 153, 142, 0.2);
    }

    .timeline-icon.warning {
        background: var(--warning-gradient);
        box-shadow: 0 0 0 4px rgba(240, 147, 251, 0.2);
    }

    .timeline-icon.danger {
        background: var(--danger-gradient);
        box-shadow: 0 0 0 4px rgba(255, 71, 87, 0.2);
    }

    .timeline-content {
        background: #fff;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
        position: relative;
    }

    .timeline-content:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }

    .timeline-content h6 {
        margin-bottom: 0.5rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Enhanced Buttons */
    .btn-custom {
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }

    .btn-primary-custom {
        background: var(--primary-gradient);
        color: white;
    }

    .btn-success-custom {
        background: var(--success-gradient);
        color: white;
    }

    .btn-danger-custom {
        background: var(--danger-gradient);
        color: white;
    }

    .btn-info-custom {
        background: var(--info-gradient);
        color: white;
    }

    .btn-secondary-custom {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        color: white;
    }

    /* Alert Enhancements */
    .alert-custom {
        border-radius: 12px;
        border: none;
        padding: 1.2rem 1.5rem;
        font-weight: 500;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    /* Wallet Transaction Info */
    .wallet-transaction-card {
        background: var(--info-gradient);
        color: white;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .transaction-detail {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .transaction-detail:last-child {
        margin-bottom: 0;
    }

    /* Certificate Preview */
    .certificate-preview {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 2px dashed #dee2e6;
        border-radius: 15px;
        padding: 2rem;
        text-align: center;
        color: #6c757d;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .info-row {
            flex-direction: column;
            gap: 0.5rem;
        }

        .info-value {
            text-align: left;
        }

        .stat-value {
            font-size: 1.5rem;
        }

        .timeline {
            padding-left: 30px;
        }

        .timeline-icon {
            left: -25px;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    {{-- SUCCESS/ERROR MESSAGES --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show alert-custom">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show alert-custom">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- MAIN CONTENT --}}
        <div class="col-lg-8">
            <div class="card main-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="fas fa-certificate me-2"></i> Certificate Request #{{ str_pad($certificateRequest->id, 6, '0', STR_PAD_LEFT) }}
                        </h4>
                        <small class="opacity-75">Submitted {{ $certificateRequest->created_at->format('M d, Y \a\t H:i A') }}</small>
                    </div>
                    <span class="status-badge status-{{ $certificateRequest->status }}">
                        <i class="fas fa-{{ $statusIcon }}"></i>

                        {{ ucfirst($certificateRequest->status) }}
                    </span>
                </div>

                <div class="card-body">
                    {{-- STUDENT INFORMATION --}}
                    <div class="info-section">
                        <div class="section-title">
                            <i class="fas fa-user-graduate"></i>
                            Student Information
                        </div>

                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-id-card"></i> Full Name:
                            </span>
                            <span class="info-value">
                                <strong>{{ $certificateRequest->student->name }}</strong>
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-envelope"></i> Email Address:
                            </span>
                            <span class="info-value">
                                <a href="mailto:{{ $certificateRequest->student->email }}" class="text-primary text-decoration-none">
                                    {{ $certificateRequest->student->email }}
                                </a>
                            </span>
                        </div>

                        @if($certificateRequest->student->phone)
                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-phone"></i> Phone Number:
                            </span>
                            <span class="info-value">
                                <a href="tel:{{ $certificateRequest->student->phone }}" class="text-primary text-decoration-none">
                                    {{ $certificateRequest->student->phone }}
                                </a>
                            </span>
                        </div>
                        @endif

                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-calendar-plus"></i> Student Since:
                            </span>
                            <span class="info-value">
                                {{ $certificateRequest->student->created_at->format('M d, Y') }}
                                <small class="text-muted">({{ $certificateRequest->student->created_at->diffForHumans() }})</small>
                            </span>
                        </div>
                    </div>

                    {{-- CERTIFICATE REQUEST INFORMATION --}}
                    <div class="info-section">
                        <div class="section-title">
                            <i class="fas fa-file-alt"></i>
                            Certificate Details
                        </div>

                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-award"></i> Certificate Type:
                            </span>
                            <span class="info-value">
                                <span class="badge bg-info text-white px-3 py-2">
                                    {{ $certificateRequest->certificate_type ?? 'Standard Certificate' }}
                                </span>
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-book"></i> Course:
                            </span>
                            <span class="info-value">
                                <strong>{{ $certificateRequest->course->name ?? 'No Course Specified' }}</strong>
                                @if($certificateRequest->course && $certificateRequest->course->description)
                                <br><small class="text-muted">{{ Str::limit($certificateRequest->course->description, 50) }}</small>
                                @endif
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-money-bill-wave"></i> Certificate Fee:
                            </span>
                            <span class="info-value">
                                <strong class="text-success fs-5">₹{{ number_format($certificateRequest->amount ?? 0, 2) }}</strong>
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-calendar-check"></i> Request Date:
                            </span>
                            <span class="info-value">
                                {{ $certificateRequest->created_at->format('M d, Y') }}
                                <small class="text-muted">at {{ $certificateRequest->created_at->format('h:i A') }}</small>
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-flag"></i> Current Status:
                            </span>
                            <span class="info-value">
                                <span class="status-badge status-{{ $certificateRequest->status }}">
                                    <i class="fas fa-{{ $statusIcon }}"></i>
                                    {{ ucfirst($certificateRequest->status) }}
                                </span>
                            </span>
                        </div>

                        @if($certificateRequest->notes)
                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-sticky-note"></i> Special Notes:
                            </span>
                            <span class="info-value">
                                <div class="bg-light p-3 rounded">
                                    <em>"{{ $certificateRequest->notes }}"</em>
                                </div>
                            </span>
                        </div>
                        @endif
                    </div>

                    {{-- WALLET TRANSACTION INFORMATION --}}
                    @if($certificateRequest->walletTransaction)
                    <div class="info-section">
                        <div class="section-title">
                            <i class="fas fa-wallet"></i>
                            Wallet Transaction
                        </div>

                        <div class="wallet-transaction-card">
                            <div class="transaction-detail">
                                <span>Transaction ID:</span>
                                <strong>#{{ $certificateRequest->walletTransaction->id }}</strong>
                            </div>
                            <div class="transaction-detail">
                                <span>Amount Deducted:</span>
                                <strong>₹{{ number_format($certificateRequest->walletTransaction->amount, 2) }}</strong>
                            </div>
                            <div class="transaction-detail">
                                <span>Transaction Date:</span>
                                <strong>{{ $certificateRequest->walletTransaction->created_at->format('M d, Y H:i A') }}</strong>
                            </div>
                            <div class="transaction-detail">
                                <span>Description:</span>
                                <small>{{ $certificateRequest->walletTransaction->description }}</small>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- ADMIN APPROVAL INFORMATION --}}
                    @if($certificateRequest->approved_by || $certificateRequest->rejected_by)
                    <div class="info-section">
                        <div class="section-title">
                            <i class="fas fa-user-shield"></i>
                            Admin Review
                        </div>

                        @if($certificateRequest->approved_by)
                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-check-circle text-success"></i> Approved By:
                            </span>
                            <span class="info-value">
                                <strong>{{ $certificateRequest->approvedBy->name ?? 'Admin' }}</strong>
                                <br><small class="text-muted">{{ $certificateRequest->approved_at->format('M d, Y H:i A') }}</small>
                            </span>
                        </div>
                        @endif

                        @if($certificateRequest->rejected_by)
                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-times-circle text-danger"></i> Rejected By:
                            </span>
                            <span class="info-value">
                                <strong>{{ $certificateRequest->rejectedBy->name ?? 'Admin' }}</strong>
                                <br><small class="text-muted">{{ $certificateRequest->rejected_at->format('M d, Y H:i A') }}</small>
                            </span>
                        </div>

                        @if($certificateRequest->rejection_reason)
                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-comment-alt"></i> Rejection Reason:
                            </span>
                            <span class="info-value">
                                <div class="bg-danger-light p-3 rounded text-dark">
                                    "{{ $certificateRequest->rejection_reason }}"
                                </div>
                            </span>
                        </div>
                        @endif
                        @endif

                        @if($certificateRequest->admin_notes)
                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-notes-medical"></i> Admin Notes:
                            </span>
                            <span class="info-value">
                                <div class="bg-light p-3 rounded">
                                    "{{ $certificateRequest->admin_notes }}"
                                </div>
                            </span>
                        </div>
                        @endif
                    </div>
                    @endif

                    {{-- CERTIFICATE INFORMATION (IF COMPLETED) --}}
                    @if($certificateRequest->status === 'completed' && $certificateRequest->certificate_number)
                    <div class="info-section">
                        <div class="section-title">
                            <i class="fas fa-certificate"></i>
                            Certificate Information
                        </div>

                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-hashtag"></i> Certificate Number:
                            </span>
                            <span class="info-value">
                                <strong class="text-primary">{{ $certificateRequest->certificate_number }}</strong>
                            </span>
                        </div>

                        @if($certificateRequest->issued_date)
                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-calendar-check"></i> Issue Date:
                            </span>
                            <span class="info-value">
                                <strong>{{ $certificateRequest->issued_date->format('M d, Y') }}</strong>
                            </span>
                        </div>
                        @endif

                        <div class="certificate-preview mt-3">
                            <i class="fas fa-certificate fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Certificate Ready</h5>
                            <p class="text-muted mb-0">Your certificate has been generated and is ready for download.</p>
                        </div>
                    </div>
                    @endif

                    {{-- ACTION BUTTONS --}}
                    <div class="mt-4 d-flex flex-wrap gap-3">
                        <a href="{{ route('franchise.certificate-requests.index') }}" class="btn btn-secondary-custom">
                            <i class="fas fa-arrow-left"></i> Back to Requests
                        </a>

                        @if($certificateRequest->status === 'pending')
                            <button class="btn btn-danger-custom" onclick="showCancelModal({{ $certificateRequest->id }})">
                                <i class="fas fa-times"></i> Cancel Request
                            </button>
                        @endif

                        @if($certificateRequest->status === 'completed')
                            <button class="btn btn-success-custom" onclick="downloadCertificate({{ $certificateRequest->id }})">
                                <i class="fas fa-download"></i> Download Certificate
                            </button>
                        @endif

                        <a href="{{ route('franchise.certificate-requests.create') }}" class="btn btn-primary-custom">
                            <i class="fas fa-plus"></i> New Request
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- SIDEBAR --}}
        <div class="col-lg-4">
            {{-- QUICK STATS --}}
            <div class="card sidebar-card">
                <div class="quick-stats">
                    <div class="stat-item">
                        <div class="stat-label">Request ID</div>
                        <div class="stat-value">#{{ str_pad($certificateRequest->id, 6, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Days Since Request</div>
                        <div class="stat-value">
                            {{ $certificateRequest->created_at->diffInDays(now()) }} {{ Str::plural('day', $certificateRequest->created_at->diffInDays(now())) }}
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Certificate Fee</div>
                        <div class="stat-value">₹{{ number_format($certificateRequest->amount ?? 0) }}</div>
                    </div>
                </div>
            </div>

            {{-- REQUEST TIMELINE --}}
            <div class="card sidebar-card">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-dark">
                        <i class="fas fa-history me-2"></i> Request Timeline
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        {{-- REQUEST SUBMITTED --}}
                        <div class="timeline-item">
                            <div class="timeline-icon">
                                <i class="fas fa-paper-plane fa-xs text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>
                                    <i class="fas fa-paper-plane text-primary"></i> Request Submitted
                                </h6>
                                <p class="mb-1 text-muted small">Certificate request created and payment processed from wallet</p>
                                <small class="text-primary fw-bold">
                                    {{ $certificateRequest->created_at->format('M d, Y H:i A') }}
                                </small>
                            </div>
                        </div>

                        {{-- DYNAMIC STATUS TIMELINE --}}
                        @if($certificateRequest->status === 'processing')
                        <div class="timeline-item">
                            <div class="timeline-icon warning">
                                <i class="fas fa-cogs fa-xs text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="text-info">
                                    <i class="fas fa-cogs"></i> Under Processing
                                </h6>
                                <p class="mb-1 text-muted small">Request is being processed by admin team</p>
                                <small class="text-info fw-bold">In Progress...</small>
                            </div>
                        </div>
                        @endif

                        @if($certificateRequest->status === 'approved')
                        <div class="timeline-item">
                            <div class="timeline-icon success">
                                <i class="fas fa-check fa-xs text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="text-success">
                                    <i class="fas fa-check-circle"></i> Request Approved
                                </h6>
                                <p class="mb-1 text-muted small">Admin approved your certificate request</p>
                                <small class="text-success fw-bold">
                                    {{ $certificateRequest->approved_at ? $certificateRequest->approved_at->format('M d, Y H:i A') : 'Recently' }}
                                </small>
                                @if($certificateRequest->approvedBy)
                                <br><small class="text-muted">by {{ $certificateRequest->approvedBy->name }}</small>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($certificateRequest->status === 'rejected')
                        <div class="timeline-item">
                            <div class="timeline-icon danger">
                                <i class="fas fa-times fa-xs text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="text-danger">
                                    <i class="fas fa-times-circle"></i> Request Rejected
                                </h6>
                                <p class="mb-1 text-muted small">Admin rejected your certificate request</p>
                                @if($certificateRequest->rejection_reason)
                                <p class="mb-1 small text-danger"><strong>Reason:</strong> {{ $certificateRequest->rejection_reason }}</p>
                                @endif
                                <small class="text-danger fw-bold">
                                    {{ $certificateRequest->rejected_at ? $certificateRequest->rejected_at->format('M d, Y H:i A') : 'Recently' }}
                                </small>
                                @if($certificateRequest->rejectedBy)
                                <br><small class="text-muted">by {{ $certificateRequest->rejectedBy->name }}</small>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($certificateRequest->status === 'completed')
                        <div class="timeline-item">
                            <div class="timeline-icon success">
                                <i class="fas fa-certificate fa-xs text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="text-success">
                                    <i class="fas fa-certificate"></i> Certificate Generated
                                </h6>
                                <p class="mb-1 text-muted small">Certificate has been generated and is ready for download</p>
                                <small class="text-success fw-bold">
                                    {{ $certificateRequest->issued_date ? $certificateRequest->issued_date->format('M d, Y H:i A') : $certificateRequest->updated_at->format('M d, Y H:i A') }}
                                </small>
                                @if($certificateRequest->certificate_number)
                                <br><small class="text-muted">Certificate #{{ $certificateRequest->certificate_number }}</small>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($certificateRequest->status === 'pending')
                        <div class="timeline-item">
                            <div class="timeline-icon warning">
                                <i class="fas fa-clock fa-xs text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="text-warning">
                                    <i class="fas fa-clock"></i> Awaiting Review
                                </h6>
                                <p class="mb-1 text-muted small">Request is queued for admin review</p>
                                <small class="text-warning fw-bold">Pending...</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- QUICK ACTIONS --}}
            <div class="card sidebar-card">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-dark">
                        <i class="fas fa-tools me-2"></i> Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('franchise.students.show', $certificateRequest->student) }}"
                           class="btn btn-outline-primary btn-sm btn-custom">
                            <i class="fas fa-user"></i> View Student Profile
                        </a>

                        @if($certificateRequest->course)
                        <a href="{{ route('franchise.courses.show', $certificateRequest->course) }}"
                           class="btn btn-outline-info btn-sm btn-custom">
                            <i class="fas fa-book"></i> View Course Details
                        </a>
                        @endif

                        <a href="{{ route('franchise.wallet.index') }}"
                           class="btn btn-outline-success btn-sm btn-custom">
                            <i class="fas fa-wallet"></i> View Wallet Transactions
                        </a>

                        <a href="{{ route('franchise.certificate-requests.create') }}"
                           class="btn btn-outline-primary btn-sm btn-custom">
                            <i class="fas fa-plus"></i> Create New Request
                        </a>

                        <button class="btn btn-outline-secondary btn-sm btn-custom" onclick="printDetails()">
                            <i class="fas fa-print"></i> Print Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CANCEL REQUEST MODAL --}}
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="cancelModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Cancel Certificate Request
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this certificate request?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Note:</strong> The amount will be refunded to your wallet.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Request</button>
                <button type="button" class="btn btn-danger" onclick="confirmCancel()">
                    <i class="fas fa-times me-1"></i>Cancel Request
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Auto-hide alerts after 6 seconds
    setTimeout(() => $('.alert-dismissible').fadeOut('slow'), 6000);
});

function showCancelModal(requestId) {
    $('#cancelModal').modal('show');
    window.currentRequestId = requestId;
}

function confirmCancel() {
    if (window.currentRequestId) {
        // Show loading state
        const btn = $('.modal-footer .btn-danger');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Canceling...');

        // Implement actual cancel functionality
        // For now, just show a message
        alert('Cancel functionality will be implemented. Wallet refund will be processed automatically.');
        $('#cancelModal').modal('hide');

        // Future implementation:
        // $.post(`/franchise/certificate-requests/${window.currentRequestId}/cancel`)
        //     .done(function(response) {
        //         location.reload();
        //     })
        //     .fail(function() {
        //         alert('Failed to cancel request. Please try again.');
        //     });
    }
}

function downloadCertificate(requestId) {
    // Show loading state
    const btn = event.target;
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Preparing Download...';
    btn.disabled = true;

    // Simulate download preparation
    setTimeout(() => {
        btn.innerHTML = originalHtml;
        btn.disabled = false;

        // Future implementation:
        // window.location.href = `/franchise/certificate-requests/${requestId}/download`;
        alert('Certificate download will be implemented when certificates are generated by admin.');
    }, 2000);
}

function printDetails() {
    window.print();
}

// Add some interactive elements
document.addEventListener('DOMContentLoaded', function() {
    // Animate timeline items on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.timeline-item').forEach(item => {
        item.style.animationPlayState = 'paused';
        observer.observe(item);
    });
});

// Helper function for status icons (you can add this to your controller or as a helper)
function getStatusIcon(status) {
    const icons = {
        'pending': 'clock',
        'processing': 'cogs',
        'approved': 'check-circle',
        'rejected': 'times-circle',
        'completed': 'certificate'
    };
    return icons[status] || 'question-circle';
}
</script>
@endsection
