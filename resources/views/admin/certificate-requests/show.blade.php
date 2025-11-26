@extends('layouts.custom-admin')

@section('page-title', 'Certificate Request Details')

@section('css')
<style>
    .detail-card {
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border: none;
        margin-bottom: 20px;
        transition: transform 0.2s ease;
    }
    .detail-card:hover {
        transform: translateY(-2px);
    }
    .status-badge {
        font-size: 1.1rem;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
    }
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, #007bff, #6c757d);
    }
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -23px;
        top: 20px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #007bff;
        border: 3px solid white;
        box-shadow: 0 0 0 2px #007bff;
    }
    .timeline-item.success::before {
        background: #28a745;
        box-shadow: 0 0 0 2px #28a745;
    }
    .timeline-item.danger::before {
        background: #dc3545;
        box-shadow: 0 0 0 2px #dc3545;
    }
    .timeline-item.warning::before {
        background: #ffc107;
        box-shadow: 0 0 0 2px #ffc107;
    }
    .action-buttons {
        position: sticky;
        top: 20px;
    }
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: white;
        border-bottom: none;
        border-radius: 10px 10px 0 0 !important;
    }
    .info-icon {
        width: 40px;
        height: 40px;
        background: #e3f2fd;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
    }
    .info-row {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        padding: 0.5rem;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid #007bff;
    }
    .quick-stat {
        text-align: center;
        padding: 1rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    .quick-stat:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .alert-custom {
        border-radius: 10px;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="text-muted mb-0">Review and manage certificate request details</h4>
                    <nav aria-label="breadcrumb" class="mt-2">
                        <ol class="breadcrumb bg-transparent p-0 mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.certificate-requests.index') }}">Certificate Requests</a>
                            </li>
                            <li class="breadcrumb-item active">Request #{{ $certificateRequest->id }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.certificate-requests.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- MAIN DETAILS --}}
        <div class="col-lg-8">

            {{-- REQUEST OVERVIEW --}}
            <div class="card detail-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-certificate mr-2"></i>Request Overview
                    </h6>
                    @php
                        $statusColors = [
                            'pending' => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            'completed' => 'info'
                        ];
                        $statusIcons = [
                            'pending' => 'clock',
                            'approved' => 'check-circle',
                            'rejected' => 'times-circle',
                            'completed' => 'certificate'
                        ];
                        $color = $statusColors[$certificateRequest->status] ?? 'secondary';
                        $icon = $statusIcons[$certificateRequest->status] ?? 'question';
                    @endphp
                    <span class="badge badge-{{ $color }} status-badge">
                        <i class="fas fa-{{ $icon }} mr-1"></i>{{ ucfirst($certificateRequest->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-icon">
                                    <i class="fas fa-hashtag text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Request ID</h6>
                                    <strong>#{{ $certificateRequest->id }}</strong>
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-icon">
                                    <i class="fas fa-calendar text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Request Date</h6>
                                    <strong>{{ $certificateRequest->created_at->format('d M Y, h:i A') }}</strong>
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-icon">
                                    <i class="fas fa-book text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Course</h6>
                                    <strong>{{ $certificateRequest->course->name ?? 'General Certificate' }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @if($certificateRequest->approved_at)
                                <div class="info-row">
                                    <div class="info-icon bg-success text-white">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 text-success">Approved Date</h6>
                                        <strong>{{ $certificateRequest->approved_at->format('d M Y, h:i A') }}</strong>
                                    </div>
                                </div>
                            @endif

                            @if($certificateRequest->rejected_at)
                                <div class="info-row">
                                    <div class="info-icon bg-danger text-white">
                                        <i class="fas fa-times-circle"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 text-danger">Rejected Date</h6>
                                        <strong>{{ $certificateRequest->rejected_at->format('d M Y, h:i A') }}</strong>
                                    </div>
                                </div>
                            @endif

                            @if($certificateRequest->admin_notes)
                                <div class="info-row">
                                    <div class="info-icon bg-info text-white">
                                        <i class="fas fa-sticky-note"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 text-info">Admin Notes</h6>
                                        <p class="mb-0">{{ $certificateRequest->admin_notes }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($certificateRequest->rejection_reason)
                                <div class="alert alert-danger alert-custom">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <strong>Rejection Reason:</strong><br>
                                    {{ $certificateRequest->rejection_reason }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- STUDENT DETAILS --}}
            <div class="card detail-card">
                <div class="card-header">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-user mr-2"></i>Student Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-icon">
                                    <i class="fas fa-user text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Student Name</h6>
                                    <strong>{{ $certificateRequest->student->name }}</strong>
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-icon">
                                    <i class="fas fa-envelope text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Email</h6>
                                    <strong>{{ $certificateRequest->student->email }}</strong>
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-icon">
                                    <i class="fas fa-phone text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Phone</h6>
                                    <strong>{{ $certificateRequest->student->phone ?? 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-icon">
                                    <i class="fas fa-building text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Franchise</h6>
                                    <strong>{{ $certificateRequest->franchise->name ?? 'N/A' }}</strong>
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-icon">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Enrolled Date</h6>
                                    <strong>{{ $certificateRequest->student->created_at->format('d M Y') }}</strong>
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-icon">
                                    <i class="fas fa-id-badge text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Student ID</h6>
                                    <strong>#{{ $certificateRequest->student->id }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ✅ FIXED: WALLET PAYMENT DETAILS --}}
            <div class="card detail-card">
                <div class="card-header">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-wallet mr-2"></i>Payment Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-icon bg-success text-white">
                                    <i class="fas fa-rupee-sign"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Certificate Fee</h6>
                                    <strong class="text-success" style="font-size: 1.3rem;">₹{{ number_format($certificateRequest->amount, 2) }}</strong>
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-icon bg-info text-white">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Payment Method</h6>
                                    <strong>Wallet Deduction</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-icon bg-success text-white">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Payment Status</h6>
                                    <span class="badge badge-success status-badge">
                                        <i class="fas fa-check mr-1"></i>Paid
                                    </span>
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-icon">
                                    <i class="fas fa-calendar text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Payment Date</h6>
                                    <strong>{{ $certificateRequest->created_at->format('d M Y, h:i A') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info alert-custom mt-3">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Payment Method:</strong> Amount deducted from franchise wallet balance
                    </div>
                </div>
            </div>

        </div>

        {{-- ACTION SIDEBAR --}}
        <div class="col-lg-4">
            <div class="action-buttons">

                {{-- QUICK ACTIONS --}}
                @if($certificateRequest->status === 'pending')
                <div class="card detail-card">
                    <div class="card-header">
                        <h6 class="mb-0 font-weight-bold">
                            <i class="fas fa-cogs mr-2"></i>Quick Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-success btn-block mb-3 btn-lg" onclick="approveRequest({{ $certificateRequest->id }})">
                            <i class="fas fa-check mr-2"></i>Approve Request
                        </button>

                        <button class="btn btn-danger btn-block btn-lg" onclick="showRejectModal({{ $certificateRequest->id }})">
                            <i class="fas fa-times mr-2"></i>Reject Request
                        </button>
                    </div>
                </div>
                @endif

                {{-- QUICK STATS --}}
                <div class="card detail-card">
                    <div class="card-header">
                        <h6 class="mb-0 font-weight-bold">
                            <i class="fas fa-chart-line mr-2"></i>Quick Stats
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="quick-stat">
                                    <i class="fas fa-calendar-day text-primary fa-2x mb-2"></i>
                                    <h6 class="mb-1">{{ $certificateRequest->created_at->diffForHumans() }}</h6>
                                    <small class="text-muted">Request Age</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="quick-stat">
                                    <i class="fas fa-building text-info fa-2x mb-2"></i>
                                    <h6 class="mb-1 small">{{ Str::limit($certificateRequest->franchise->name ?? 'N/A', 15) }}</h6>
                                    <small class="text-muted">Franchise</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ACTIVITY TIMELINE --}}
                <div class="card detail-card">
                    <div class="card-header">
                        <h6 class="mb-0 font-weight-bold">
                            <i class="fas fa-history mr-2"></i>Activity Timeline
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <h6 class="mb-1"><i class="fas fa-plus-circle text-primary mr-2"></i>Request Created</h6>
                                <small class="text-muted">{{ $certificateRequest->created_at->format('d M Y, h:i A') }}</small>
                                <p class="mb-0 small mt-1">Certificate request submitted by franchise.</p>
                            </div>

                            <div class="timeline-item success">
                                <h6 class="mb-1">
                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                    Payment Completed
                                </h6>
                                <small class="text-muted">{{ $certificateRequest->created_at->format('d M Y, h:i A') }}</small>
                                <p class="mb-0 small mt-1">₹{{ number_format($certificateRequest->amount, 2) }} deducted from wallet.</p>
                            </div>

                            @if($certificateRequest->approved_at)
                            <div class="timeline-item success">
                                <h6 class="mb-1"><i class="fas fa-check-circle text-success mr-2"></i>Request Approved</h6>
                                <small class="text-muted">{{ $certificateRequest->approved_at->format('d M Y, h:i A') }}</small>
                                @if($certificateRequest->approvedBy)
                                    <p class="mb-0 small mt-1">Approved by {{ $certificateRequest->approvedBy->name }}</p>
                                @endif
                            </div>
                            @endif

                            @if($certificateRequest->rejected_at)
                            <div class="timeline-item danger">
                                <h6 class="mb-1"><i class="fas fa-times-circle text-danger mr-2"></i>Request Rejected</h6>
                                <small class="text-muted">{{ $certificateRequest->rejected_at->format('d M Y, h:i A') }}</small>
                                <p class="mb-0 small mt-1">{{ $certificateRequest->rejection_reason }}</p>
                                @if($certificateRequest->rejectedBy)
                                    <p class="mb-0 small mt-1 text-muted">Rejected by {{ $certificateRequest->rejectedBy->name }}</p>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- FRANCHISE NOTES --}}
                @if($certificateRequest->notes)
                <div class="card detail-card">
                    <div class="card-header">
                        <h6 class="mb-0 font-weight-bold">
                            <i class="fas fa-sticky-note mr-2"></i>Franchise Notes
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $certificateRequest->notes }}</p>
                    </div>
                </div>
                @endif

                {{-- CERTIFICATE PREVIEW --}}
                @if(in_array($certificateRequest->status, ['approved', 'completed']) && isset($certificate))
                <div class="card detail-card">
                    <div class="card-header">
                        <h6 class="mb-0 font-weight-bold">
                            <i class="fas fa-certificate mr-2"></i>Generated Certificate
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <i class="fas fa-certificate text-success fa-3x mb-3"></i>
                        <h6 class="mb-2">Certificate Number</h6>
                        <p class="mb-3 font-weight-bold">{{ $certificate->number }}</p>

                        <a href="{{ route('admin.certificates.show', $certificate->id) }}"
                           class="btn btn-primary btn-block">
                            <i class="fas fa-eye mr-2"></i>View Certificate
                        </a>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

{{-- REJECT MODAL --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle mr-2"></i>Reject Certificate Request
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Warning:</strong> This action will reject the certificate request and refund the amount to franchise wallet.
                </div>
                <form id="reject-form">
                    <input type="hidden" id="reject-request-id" value="{{ $certificateRequest->id }}">
                    <div class="form-group">
                        <label for="rejection-reason">
                            <i class="fas fa-exclamation-triangle text-danger mr-1"></i>
                            Rejection Reason <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="rejection-reason" rows="4"
                                  placeholder="Please provide a detailed reason for rejection..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="admin-notes">
                            <i class="fas fa-sticky-note text-info mr-1"></i>
                            Additional Notes (Optional)
                        </label>
                        <textarea class="form-control" id="admin-notes" rows="3"
                                  placeholder="Any additional notes for internal use..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-arrow-left mr-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirm-reject">
                    <i class="fas fa-times mr-1"></i>Reject & Refund
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
function approveRequest(requestId) {
    if (confirm('Are you sure you want to approve this certificate request?\n\nThis will create a certificate for the student.')) {
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Approving...';
        btn.disabled = true;

        $.post('/admin/certificate-requests/' + requestId + '/approve', {
            _token: '{{ csrf_token() }}'
        }).done(function(response) {
            if (response.success) {
                showAlert('success', response.message);
                setTimeout(function() {
                    location.reload();
                }, 1500);
            } else {
                showAlert('error', response.message);
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }).fail(function(xhr) {
            showAlert('error', xhr.responseJSON?.message || 'Error approving request');
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
}

function showRejectModal(requestId) {
    $('#reject-request-id').val(requestId);
    $('#rejection-reason').val('');
    $('#admin-notes').val('');
    $('#rejectModal').modal('show');
}

$('#confirm-reject').on('click', function() {
    const requestId = $('#reject-request-id').val();
    const reason = $('#rejection-reason').val();
    const notes = $('#admin-notes').val();

    if (!reason.trim()) {
        alert('Please provide a rejection reason.');
        $('#rejection-reason').focus();
        return;
    }

    const btn = $(this);
    const originalText = btn.html();
    btn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Rejecting...').prop('disabled', true);

    $.post('/admin/certificate-requests/' + requestId + '/reject', {
        _token: '{{ csrf_token() }}',
        reason: reason,
        notes: notes
    }).done(function(response) {
        if (response.success) {
            showAlert('success', response.message);
            $('#rejectModal').modal('hide');
            setTimeout(function() {
                location.reload();
            }, 1500);
        } else {
            showAlert('error', response.message);
            btn.html(originalText).prop('disabled', false);
        }
    }).fail(function(xhr) {
        showAlert('error', xhr.responseJSON?.message || 'Error rejecting request');
        btn.html(originalText).prop('disabled', false);
    });
});

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';

    const alertHtml = `
        <div class="position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
            <div class="alert ${alertClass} alert-dismissible fade show alert-custom shadow-lg">
                <i class="fas ${icon} mr-2"></i> ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        </div>
    `;

    $('body').append(alertHtml);

    setTimeout(function() {
        $('.alert').fadeOut(function() {
            $(this).parent().remove();
        });
    }, 5000);
}

$(document).on('click', '.alert', function() {
    $(this).fadeOut(function() {
        $(this).parent().remove();
    });
});
</script>
@endsection
