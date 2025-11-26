@extends('layouts.custom-admin')

@section('page-title', 'Certificate Details')

@section('css')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { 
        font-family: 'Inter', sans-serif; 
        background: #f8f9fa;
    }
    
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }
    
    .card-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 18px 25px;
        border: none;
    }
    
    .certificate-display {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        padding: 40px;
        min-height: 500px;
        box-shadow: inset 0 0 30px rgba(0,0,0,0.05);
    }
    
    .certificate-inner {
        background: white;
        border: 5px solid #28a745;
        border-radius: 15px;
        padding: 50px;
        text-align: center;
        position: relative;
        box-shadow: 0 10px 30px rgba(40, 167, 69, 0.15);
    }
    
    .certificate-badge {
        position: absolute;
        top: 15px;
        right: 20px;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .certificate-number {
        position: absolute;
        top: 15px;
        left: 20px;
        font-family: 'Courier New', monospace;
        font-size: 11px;
        color: #6c757d;
        background: #f8f9fa;
        padding: 6px 14px;
        border-radius: 12px;
        border: 1px solid #dee2e6;
    }
    
    .info-section {
        border-bottom: 1px solid #e9ecef;
        padding: 20px 0;
    }
    
    .info-section:last-child {
        border-bottom: none;
    }
    
    .info-section h5 {
        color: #28a745;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .info-section h5 i {
        margin-right: 8px;
        color: #20c997;
    }
    
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 12px;
    }
    
    .info-label {
        color: #6c757d;
        font-size: 13px;
        font-weight: 500;
        min-width: 100px;
    }
    
    .info-value {
        color: #343a40;
        font-size: 14px;
        font-weight: 600;
        text-align: right;
        word-break: break-word;
        max-width: 60%;
    }
    
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 25px;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -26px;
        top: 5px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 3px solid #dee2e6;
        background: white;
    }
    
    .timeline-item.completed::before {
        border-color: #28a745;
        background: #28a745;
    }
    
    .timeline-item.active::before {
        border-color: #ffc107;
        background: #ffc107;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.2); }
    }
    
    .timeline-content {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 15px;
    }
    
    .alert-info-custom {
        background: linear-gradient(135deg, #e7f3ff 0%, #d6ebff 100%);
        border-left: 4px solid #007bff;
        border-radius: 8px;
        padding: 15px 20px;
        margin-bottom: 20px;
    }
    
    @media print {
        .no-print {
            display: none !important;
        }
        .certificate-display {
            box-shadow: none;
            background: white;
        }
        .card {
            box-shadow: none;
            page-break-inside: avoid;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4 no-print">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="mb-2 mb-md-0">
                    <h4 class="text-muted mb-1">Certificate Details</h4>
                    <h6 class="text-primary font-weight-bold">#{{ $certificate->number }}</h6>
                </div>
                <div class="action-buttons">
                    @if($certificate->status === 'issued')
                        <button class="btn btn-info" onclick="window.print()">
                            <i class="fas fa-print mr-2"></i>Print
                        </button>
                        <a href="{{ route('admin.certificates.download', $certificate->id) }}" class="btn btn-success">
                            <i class="fas fa-download mr-2"></i>Download PDF
                        </a>
                    @endif
                    
                    @if($certificate->certificateRequest)
                        <a href="{{ route('admin.certificate-requests.show', $certificate->certificateRequest->id) }}" class="btn btn-primary">
                            <i class="fas fa-clipboard-list mr-2"></i>View Request
                        </a>
                    @endif
                    
                    <a href="{{ route('admin.certificates.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Alert -->
    @if($certificate->status === 'issued' && $certificate->certificateRequest)
    <div class="alert-info-custom no-print">
        <i class="fas fa-info-circle mr-2"></i>
        <strong>Certificate Information:</strong> This certificate was automatically generated from 
        <a href="{{ route('admin.certificate-requests.show', $certificate->certificateRequest->id) }}" class="font-weight-bold">
            Certificate Request #{{ $certificate->certificateRequest->id }}
        </a>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Certificate Display -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 font-weight-bold text-white">
                        <i class="fas fa-certificate mr-2"></i>Certificate Preview
                    </h6>
                </div>
                <div class="card-body p-2">
                    <div class="certificate-display">
                        <div class="certificate-inner">
                            <div class="certificate-badge">
                                {{ ucfirst($certificate->status) }}
                            </div>
                            <div class="certificate-number">
                                {{ $certificate->number }}
                            </div>
                            
                            <div class="mb-4">
                                <i class="fas fa-award text-success" style="font-size: 4rem;"></i>
                            </div>
                            
                            <h2 class="text-success mb-4" style="font-weight: 700; letter-spacing: 2px;">
                                CERTIFICATE OF COMPLETION
                            </h2>
                            
                            <div class="mb-4">
                                <h5 style="color: #6c757d; font-weight: 500;">This is to certify that</h5>
                                <h2 class="text-primary my-3" style="font-weight: 700; border-bottom: 3px solid #007bff; display: inline-block; padding-bottom: 5px;">
                                    {{ $certificate->student->name ?? 'N/A' }}
                                </h2>
                                <h5 style="color: #6c757d; font-weight: 500;">has successfully completed the course</h5>
                                <h3 class="text-success mt-3 mb-4" style="font-weight: 600; background: #e8f5e9; padding: 12px 24px; border-radius: 10px; display: inline-block;">
                                    {{ $certificate->course->name ?? 'N/A' }}
                                </h3>
                            </div>
                            
                            @if($certificate->franchise)
                            <div class="mb-4">
                                <span style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 8px 20px; border-radius: 20px; font-size: 13px; color: #495057;">
                                    <strong>Training Partner:</strong> {{ $certificate->franchise->name }}
                                </span>
                            </div>
                            @endif
                            
                            <hr class="my-4" style="border-color: #28a745; border-width: 2px;">
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <strong style="color: #6c757d; font-size: 12px;">Certificate ID</strong><br>
                                        <span style="color: #343a40; font-weight: 600;">CERT-{{ $certificate->number }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <strong style="color: #6c757d; font-size: 12px;">Issue Date</strong><br>
                                        <span style="color: #343a40; font-weight: 600;">
                                            {{ $certificate->issued_at ? $certificate->issued_at->format('F d, Y') : 'Not issued yet' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <strong style="color: #6c757d; font-size: 12px;">Status</strong><br>
                                        <span style="color: #28a745; font-weight: 600; text-transform: uppercase;">
                                            {{ $certificate->status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            @if($certificate->status === 'issued' && $certificate->issuedBy)
                                <div class="mt-4">
                                    <div style="border-top: 2px solid #343a40; width: 200px; margin: 20px auto 10px;"></div>
                                    <strong style="color: #343a40; font-size: 14px;">{{ $certificate->issuedBy->name }}</strong><br>
                                    <small style="color: #6c757d;">Authorized Signature</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Certificate Information -->
            <div class="card no-print">
                <div class="card-header">
                    <h6 class="mb-0 font-weight-bold text-white">
                        <i class="fas fa-info-circle mr-2"></i>Certificate Information
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Student Details -->
                    <div class="info-section">
                        <h5><i class="fas fa-user-graduate"></i>Student Details</h5>
                        <div class="info-row">
                            <span class="info-label">Name</span>
                            <span class="info-value">{{ $certificate->student->name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email</span>
                            <span class="info-value">{{ $certificate->student->email ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phone</span>
                            <span class="info-value">{{ $certificate->student->phone ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Course Details -->
                    <div class="info-section">
                        <h5><i class="fas fa-book"></i>Course Details</h5>
                        <div class="info-row">
                            <span class="info-label">Course</span>
                            <span class="info-value">{{ $certificate->course->name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Duration</span>
                            <span class="info-value">{{ $certificate->course->duration ?? 'N/A' }}</span>
                        </div>
                        @if($certificate->course->certificate_fee)
                        <div class="info-row">
                            <span class="info-label">Fee</span>
                            <span class="info-value">₹{{ number_format($certificate->course->certificate_fee, 2) }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Franchise Details -->
                    @if($certificate->franchise)
                    <div class="info-section">
                        <h5><i class="fas fa-store"></i>Franchise Details</h5>
                        <div class="info-row">
                            <span class="info-label">Name</span>
                            <span class="info-value">{{ $certificate->franchise->name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Location</span>
                            <span class="info-value">{{ $certificate->franchise->city ?? 'N/A' }}</span>
                        </div>
                    </div>
                    @endif

                    <!-- Certificate Status -->
                    <div class="info-section">
                        <h5><i class="fas fa-clipboard-list"></i>Certificate Status</h5>
                        <div class="info-row">
                            <span class="info-label">Status</span>
                            <span class="info-value">
                                @if($certificate->status === 'issued')
                                    <span class="badge badge-success">Issued</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($certificate->status) }}</span>
                                @endif
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Created</span>
                            <span class="info-value">{{ $certificate->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Issued</span>
                            <span class="info-value">
                                {{ $certificate->issued_at ? $certificate->issued_at->format('M d, Y') : 'Not issued' }}
                            </span>
                        </div>
                        @if($certificate->issuedBy)
                        <div class="info-row">
                            <span class="info-label">Issued By</span>
                            <span class="info-value">{{ $certificate->issuedBy->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card no-print">
                <div class="card-header">
                    <h6 class="mb-0 font-weight-bold text-white">
                        <i class="fas fa-history mr-2"></i>Certificate Timeline
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item completed">
                            <div class="timeline-content">
                                <h6 class="mb-2" style="color: #28a745; font-weight: 600;">Certificate Requested</h6>
                                <small class="text-muted">{{ $certificate->created_at->format('M d, Y \a\t g:i A') }}</small>
                                <p class="mb-0 mt-2" style="font-size: 13px; color: #6c757d;">
                                    Request submitted by {{ $certificate->franchise->name ?? 'franchise' }}
                                </p>
                            </div>
                        </div>

                        @if($certificate->certificateRequest && $certificate->certificateRequest->approved_at)
                        <div class="timeline-item completed">
                            <div class="timeline-content">
                                <h6 class="mb-2" style="color: #28a745; font-weight: 600;">Request Approved</h6>
                                <small class="text-muted">{{ $certificate->certificateRequest->approved_at->format('M d, Y \a\t g:i A') }}</small>
                                <p class="mb-0 mt-2" style="font-size: 13px; color: #6c757d;">
                                    Approved by {{ $certificate->certificateRequest->approvedBy->name ?? 'admin' }}
                                </p>
                            </div>
                        </div>
                        @endif

                        <div class="timeline-item {{ $certificate->status === 'issued' ? 'completed' : '' }}">
                            <div class="timeline-content">
                                <h6 class="mb-2" style="color: {{ $certificate->status === 'issued' ? '#28a745' : '#6c757d' }}; font-weight: 600;">
                                    Certificate Issued
                                </h6>
                                <small class="text-muted">
                                    @if($certificate->status === 'issued' && $certificate->issued_at)
                                        {{ $certificate->issued_at->format('M d, Y \a\t g:i A') }}
                                    @else
                                        Pending issuance
                                    @endif
                                </small>
                                <p class="mb-0 mt-2" style="font-size: 13px; color: #6c757d;">
                                    Certificate ready for download and distribution
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
// No custom JS needed - certificate is view-only
console.log('Certificate #{{ $certificate->number }} loaded successfully');
</script>
@endsection
