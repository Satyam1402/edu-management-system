@extends('layouts.custom-admin')

@section('page-title', 'Certificate Details')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/certificates/show.css') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Inter', sans-serif; }
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
                    <h6 class="text-primary">#{{ $certificate->number }}</h6>
                </div>
                <div class="action-buttons">
                    @if($certificate->status === 'requested')
                        <button class="btn btn-success" onclick="approveCertificate({{ $certificate->id }})">
                            <i class="fas fa-check mr-2"></i>Approve
                        </button>
                    @endif
                    
                    @if($certificate->status === 'approved')
                        <button class="btn btn-primary" onclick="issueCertificate({{ $certificate->id }})">
                            <i class="fas fa-certificate mr-2"></i>Issue
                        </button>
                    @endif
                    
                    @if($certificate->status === 'issued')
                        <button class="btn btn-info" onclick="enhancedPrint()">
                            <i class="fas fa-print mr-2"></i>Print
                        </button>
                        <button class="btn btn-secondary" onclick="downloadPDF({{ $certificate->id }})">
                            <i class="fas fa-download mr-2"></i>Download PDF
                        </button>
                    @endif
                    
                    <a href="{{ route('admin.certificates.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>

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
                            <div class="certificate-badge">{{ ucfirst($certificate->status) }}</div>
                            <div class="certificate-number">{{ $certificate->number }}</div>
                            
                            <div class="mb-4">
                                <i class="fas fa-certificate text-success" style="font-size: 3.5rem;"></i>
                            </div>
                            
                            <h3 class="text-success mb-4" style="font-weight: 700; letter-spacing: 1px;">CERTIFICATE OF COMPLETION</h3>
                            
                            <div class="mb-4">
                                <h5 style="color: #2c3e50;">This is to certify that</h5>
                                <h2 class="text-primary my-3" style="font-weight: 700;">{{ $certificate->student->name ?? 'N/A' }}</h2>
                                <h5 style="color: #2c3e50;">has successfully completed the course</h5>
                                <h3 class="text-success mt-3" style="font-weight: 600;">{{ $certificate->course->name ?? 'N/A' }}</h3>
                            </div>
                            
                            <hr class="my-4" style="border-color: #28a745; border-width: 2px;">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="text-left">
                                        <strong style="color: #495057;">Issue Date:</strong><br>
                                        <span style="color: #2c3e50;">{{ $certificate->issued_at ? $certificate->issued_at->format('F d, Y') : 'Not issued yet' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-right">
                                        <strong style="color: #495057;">Certificate ID:</strong><br>
                                        <span style="color: #2c3e50;">CERT-{{ $certificate->number }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            @if($certificate->status === 'issued')
                                <div class="mt-4">
                                    <div style="border-top: 2px solid #28a745; width: 200px; margin: 20px auto 10px;"></div>
                                    <strong style="color: #495057;">Authorized Signature</strong>
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
                            <span class="info-value email">{{ $certificate->student->email ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phone</span>
                            <span class="info-value phone">{{ $certificate->student->phone ?? 'N/A' }}</span>
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
                        <div class="info-row">
                            <span class="info-label">Level</span>
                            <span class="info-value">{{ $certificate->course->level ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Certificate Status -->
                    <div class="info-section">
                        <h5><i class="fas fa-clipboard-list"></i>Certificate Status</h5>
                        <div class="info-row">
                            <span class="info-label">Status</span>
                            <span class="info-value">
                                @if($certificate->status === 'requested')
                                    <span class="badge badge-warning">Requested</span>
                                @elseif($certificate->status === 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @elseif($certificate->status === 'issued')
                                    <span class="badge badge-primary">Issued</span>
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
                                <p class="mb-0 mt-2" style="font-size: 13px; color: #6c757d;">Certificate request created in the system.</p>
                            </div>
                        </div>

                        <div class="timeline-item {{ $certificate->status === 'approved' || $certificate->status === 'issued' ? 'completed' : ($certificate->status === 'requested' ? 'active' : '') }}">
                            <div class="timeline-content">
                                <h6 class="mb-2" style="color: {{ $certificate->status === 'approved' || $certificate->status === 'issued' ? '#28a745' : '#ffc107' }}; font-weight: 600;">Request Approved</h6>
                                <small class="text-muted">
                                    @if($certificate->status === 'approved' || $certificate->status === 'issued')
                                        {{ $certificate->updated_at->format('M d, Y \a\t g:i A') }}
                                    @else
                                        Pending approval
                                    @endif
                                </small>
                                <p class="mb-0 mt-2" style="font-size: 13px; color: #6c757d;">Certificate request reviewed and approved.</p>
                            </div>
                        </div>

                        <div class="timeline-item {{ $certificate->status === 'issued' ? 'completed' : ($certificate->status === 'approved' ? 'active' : '') }}">
                            <div class="timeline-content">
                                <h6 class="mb-2" style="color: {{ $certificate->status === 'issued' ? '#28a745' : ($certificate->status === 'approved' ? '#ffc107' : '#6c757d') }}; font-weight: 600;">Certificate Issued</h6>
                                <small class="text-muted">
                                    @if($certificate->status === 'issued' && $certificate->issued_at)
                                        {{ $certificate->issued_at->format('M d, Y \a\t g:i A') }}
                                    @else
                                        Waiting to be issued
                                    @endif
                                </small>
                                <p class="mb-0 mt-2" style="font-size: 13px; color: #6c757d;">Certificate officially issued and available for download.</p>
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
<script src="{{ asset('js/admin/certificates/show.js') }}"></script>
@endsection
