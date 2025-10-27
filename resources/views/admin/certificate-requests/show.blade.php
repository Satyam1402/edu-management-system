@extends('layouts.admin')

@section('page-title', 'Certificate Request Details')

@section('css')
<style>
    .card {
        border-radius: 15px;
        box-shadow: 0 4px 14px #667eea16;
        border: none;
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
    }
    .status-pending { background-color: #fff3cd; color: #856404; }
    .status-approved { background-color: #d4edda; color: #155724; }
    .status-rejected { background-color: #f8d7da; color: #721c24; }
    .info-section {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .info-label {
        font-weight: 600;
        color: #666;
        min-width: 150px;
    }
    .info-value {
        flex: 1;
        text-align: right;
    }
    .action-buttons {
        position: sticky;
        top: 20px;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .approval-section {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .rejection-section {
        background: linear-gradient(135deg, #dc3545, #fd7e14);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <!-- Request Details Card -->
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

                    <!-- Franchise Information -->
                    <div class="info-section">
                        <h6 class="mb-3"><i class="fas fa-building"></i> Franchise Information</h6>
                        <div class="info-row">
                            <span class="info-label">Franchise Name:</span>
                            <span class="info-value">{{ $certificateRequest->franchise->name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Franchise Email:</span>
                            <span class="info-value">{{ $certificateRequest->franchise->email }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Contact Number:</span>
                            <span class="info-value">{{ $certificateRequest->franchise->phone ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Student Information -->
                    <div class="info-section">
                        <h6 class="mb-3"><i class="fas fa-user-graduate"></i> Student Information</h6>
                        <div class="info-row">
                            <span class="info-label">Student Name:</span>
                            <span class="info-value">{{ $certificateRequest->student->name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value">{{ $certificateRequest->student->email }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phone:</span>
                            <span class="info-value">{{ $certificateRequest->student->phone ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Enrollment Number:</span>
                            <span class="info-value">{{ $certificateRequest->student->enrollment_number ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Student Status:</span>
                            <span class="info-value">
                                <span class="badge badge-{{ $certificateRequest->student->status === 'active' ? 'success' : 'warning' }}">
                                    {{ ucfirst($certificateRequest->student->status) }}
                                </span>
                            </span>
                        </div>
                    </div>

                    <!-- Certificate Information -->
                    <div class="info-section">
                        <h6 class="mb-3"><i class="fas fa-certificate"></i> Certificate Information</h6>
                        <div class="info-row">
                            <span class="info-label">Certificate Type:</span>
                            <span class="info-value">{{ $certificateRequest->course->name ?? 'General Certificate' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Request Date:</span>
                            <span class="info-value">{{ $certificateRequest->requested_at->format('M d, Y H:i A') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Days Since Request:</span>
                            <span class="info-value">{{ $certificateRequest->requested_at->diffInDays(now()) }} days ago</span>
                        </div>
                        @if($certificateRequest->note)
                        <div class="info-row">
                            <span class="info-label">Special Note:</span>
                            <span class="info-value">{{ $certificateRequest->note }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Payment Verification -->
                    @if($certificateRequest->payment)
                    <div class="info-section border-success">
                        <h6 class="mb-3 text-success"><i class="fas fa-check-circle"></i> Payment Verified</h6>
                        <div class="info-row">
                            <span class="info-label">Payment ID:</span>
                            <span class="info-value">
                                <a href="{{ route('admin.payments.show', $certificateRequest->payment) }}" class="text-primary">
                                    #{{ $certificateRequest->payment->id }}
                                </a>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Amount Paid:</span>
                            <span class="info-value"><strong>₹{{ number_format($certificateRequest->payment->amount, 2) }}</strong></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Payment Method:</span>
                            <span class="info-value">{{ ucfirst($certificateRequest->payment->payment_method ?? 'N/A') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Payment Status:</span>
                            <span class="info-value">
                                <span class="badge badge-success">{{ ucfirst($certificateRequest->payment->status) }}</span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Transaction Date:</span>
                            <span class="info-value">{{ $certificateRequest->payment->created_at->format('M d, Y H:i A') }}</span>
                        </div>
                        @if($certificateRequest->payment->transaction_id)
                        <div class="info-row">
                            <span class="info-label">Transaction ID:</span>
                            <span class="info-value"><code>{{ $certificateRequest->payment->transaction_id }}</code></span>
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="info-section border-warning">
                        <h6 class="mb-3 text-warning"><i class="fas fa-exclamation-triangle"></i> Payment Not Found</h6>
                        <p class="text-muted mb-0">No payment information is linked to this request. This may indicate an issue with the request.</p>
                    </div>
                    @endif

                    <!-- Admin Actions History -->
                    @if($certificateRequest->status !== 'pending')
                    <div class="info-section">
                        <h6 class="mb-3"><i class="fas fa-history"></i> Action History</h6>
                        <div class="info-row">
                            <span class="info-label">Status Changed:</span>
                            <span class="info-value">{{ $certificateRequest->updated_at->format('M d, Y H:i A') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Processed By:</span>
                            <span class="info-value">{{ auth()->user()->name }} (Admin)</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Action Panel -->
            @if($certificateRequest->status === 'pending')
            <div class="action-buttons">
                <h6><i class="fas fa-tasks"></i> Quick Actions</h6>

                <!-- Approve Section -->
                <div class="approval-section mb-3">
                    <h6 class="mb-2"><i class="fas fa-check"></i> Approve Request</h6>
                    <p class="small mb-3">This will create and issue the certificate automatically.</p>
                    <button class="btn btn-light btn-block" onclick="showApprovalModal()">
                        <i class="fas fa-check"></i> Approve & Issue Certificate
                    </button>
                </div>

                <!-- Reject Section -->
                <div class="rejection-section">
                    <h6 class="mb-2"><i class="fas fa-times"></i> Reject Request</h6>
                    <p class="small mb-3">Provide a reason for rejection to the franchise.</p>
                    <button class="btn btn-light btn-block" onclick="showRejectionModal()">
                        <i class="fas fa-times"></i> Reject Request
                    </button>
                </div>
            </div>
            @else
            <!-- Status Display -->
            <div class="action-buttons">
                <h6><i class="fas fa-info-circle"></i> Request Status</h6>
                @if($certificateRequest->status === 'approved')
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <strong>Approved</strong><br>
                    Certificate has been issued successfully.
                </div>
                @else
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle"></i> <strong>Rejected</strong><br>
                    This request was rejected by admin.
                </div>
                @endif
            </div>
            @endif

            <!-- Related Links -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-link"></i> Related Information</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.students.show', $certificateRequest->student) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-user"></i> View Student Profile
                        </a>
                        <a href="{{ route('admin.franchises.show', $certificateRequest->franchise) }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-building"></i> View Franchise Details
                        </a>
                        @if($certificateRequest->payment)
                        <a href="{{ route('admin.payments.show', $certificateRequest->payment) }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-receipt"></i> View Payment Details
                        </a>
                        @endif
                        @if($certificateRequest->course)
                        <a href="{{ route('admin.courses.show', $certificateRequest->course) }}" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-book"></i> View Course Details
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Quick Stats</h6>
                </div>
                <div class="card-body">
                    <small class="text-muted">Franchise Statistics:</small>
                    <ul class="list-unstyled mt-2">
                        <li><i class="fas fa-users text-info"></i> Students: <span class="float-right">{{ $certificateRequest->franchise->students_count ?? 0 }}</span></li>
                        <li><i class="fas fa-certificate text-success"></i> Total Requests: <span class="float-right">{{ $certificateRequest->franchise->certificate_requests_count ?? 0 }}</span></li>
                        <li><i class="fas fa-credit-card text-warning"></i> Payments: <span class="float-right">₹{{ number_format($certificateRequest->franchise->total_payments ?? 0, 2) }}</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-check"></i> Approve Certificate Request</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> This will automatically create and issue the certificate for <strong>{{ $certificateRequest->student->name }}</strong>.
                </div>
                <form id="approvalForm">
                    @csrf
                    <div class="form-group">
                        <label>Certificate Title</label>
                        <input type="text" class="form-control" name="title" value="Certificate of Completion" required>
                    </div>
                    <div class="form-group">
                        <label>Certificate Description</label>
                        <textarea class="form-control" name="description" rows="3" required>This certifies that {{ $certificateRequest->student->name }} has successfully completed the requirements for {{ $certificateRequest->course->name ?? 'the program' }}.</textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="confirmApproval()">
                    <i class="fas fa-check"></i> Approve & Issue Certificate
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-times"></i> Reject Certificate Request</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> This request will be marked as rejected and the franchise will be notified.
                </div>
                <form id="rejectionForm">
                    @csrf
                    <div class="form-group">
                        <label>Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="rejection_reason" rows="4" placeholder="Please provide a clear reason for rejection..." required></textarea>
                        <small class="form-text text-muted">This reason will be visible to the franchise.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmRejection()">
                    <i class="fas fa-times"></i> Reject Request
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
function showApprovalModal() {
    $('#approvalModal').modal('show');
}

function showRejectionModal() {
    $('#rejectionModal').modal('show');
}

function confirmApproval() {
    const formData = $('#approvalForm').serialize();

    $.ajax({
        url: '{{ route("admin.certificate-requests.approve", $certificateRequest) }}',
        method: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                $('#approvalModal').modal('hide');
                showAlert('success', response.message);
                // Reload page to show updated status
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                showAlert('error', response.message || 'Failed to approve request');
            }
        },
        error: function() {
            showAlert('error', 'An error occurred while processing the approval');
        }
    });
}

function confirmRejection() {
    const formData = $('#rejectionForm').serialize();

    $.ajax({
        url: '{{ route("admin.certificate-requests.reject", $certificateRequest) }}',
        method: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                $('#rejectionModal').modal('hide');
                showAlert('success', response.message);
                // Reload page to show updated status
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                showAlert('error', response.message || 'Failed to reject request');
            }
        },
        error: function() {
            showAlert('error', 'An error occurred while processing the rejection');
        }
    });
}

function showAlert(type, message) {
    // You can integrate with your existing alert system
    // For now, using simple alert
    alert(message);

    // Or if you have a toast system:
    // toastr[type](message);
}
</script>
@endsection
