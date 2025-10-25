@extends('layouts.custom-admin')

@section('page-title', 'Payment Details')

@section('css')
<style>
.container-fluid {
    padding: 0 20px !important;
    background: #f5f7fa;
}

.card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    margin: 20px 0;
    overflow: hidden;
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.98);
}

.card-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 50%, #17a2b8 100%);
    color: white;
    border-radius: 20px 20px 0 0;
    padding: 20px 25px;
    border: none;
    position: relative;
    overflow: hidden;
}

.card-header h6 {
    font-size: 16px;
    margin: 0;
}

/* Special styling for Payment Link Card */
.payment-link-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    position: relative;
    overflow: hidden;
}

.payment-link-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 80%, rgba(120, 255, 214, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
    pointer-events: none;
}

.payment-link-card .card-body {
    position: relative;
    z-index: 1;
}

.payment-link-input {
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid rgba(255, 255, 255, 0.2);
    color: white;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    border-radius: 10px;
    backdrop-filter: blur(10px);
}

.payment-link-input::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

.payment-link-input:focus {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.4);
    color: white;
    box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
}

.payment-gateway-card {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    border: none;
}

.payment-gateway-card .card-body {
    position: relative;
    z-index: 1;
}

.gateway-option {
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 15px;
    text-decoration: none;
    color: white;
    display: block;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.gateway-option:hover {
    background: rgba(255, 255, 255, 0.25);
    border-color: rgba(255, 255, 255, 0.4);
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
}

.gateway-icon {
    font-size: 1.5rem;
    margin-right: 12px;
    opacity: 0.9;
}

.info-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
    border-left: 5px solid #28a745;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.info-section h5 {
    color: #28a745;
    font-weight: 700;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    font-size: 1rem;
}

.info-section h5 i {
    background: linear-gradient(135deg, #28a745, #20c997);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-right: 8px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 8px 0;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    gap: 10px;
    min-height: auto;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #495057;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
    flex-shrink: 0;
    max-width: 80px;
    line-height: 1.2;
}

.info-value {
    color: #2c3e50;
    font-weight: 500;
    font-size: 12px;
    line-height: 1.3;
    text-align: right;
    word-wrap: break-word;
    word-break: break-all;
    overflow-wrap: break-word;
    hyphens: auto;
    flex: 1;
    min-width: 0;
}

.info-value.long-text {
    font-size: 10px;
    font-family: 'Courier New', monospace;
    line-height: 1.2;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 9px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-block;
}

.status-pending { background: linear-gradient(135deg, #ffc107, #ffca2c); color: #856404; }
.status-completed { background: linear-gradient(135deg, #28a745, #20c997); color: white; }
.status-failed { background: linear-gradient(135deg, #dc3545, #c82333); color: white; }
.status-refunded { background: linear-gradient(135deg, #17a2b8, #138496); color: white; }

.payment-receipt {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
    text-align: center;
    position: relative;
    overflow: hidden;
    margin-bottom: 30px;
}

.payment-receipt::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 80%, rgba(120, 255, 214, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
    pointer-events: none;
}

.receipt-content {
    position: relative;
    z-index: 1;
}

.receipt-amount {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 20px 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.receipt-order-id {
    background: rgba(255, 255, 255, 0.2);
    padding: 8px 16px;
    border-radius: 20px;
    display: inline-block;
    margin-top: 15px;
    font-family: 'Courier New', monospace;
    font-weight: 600;
    letter-spacing: 1px;
    font-size: 13px;
}

.action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: center;
    margin-top: 15px;
}

.btn {
    border-radius: 8px;
    font-weight: 600;
    padding: 8px 16px;
    font-size: 13px;
    transition: all 0.3s ease;
    border: none;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.btn-success {
    background: linear-gradient(135deg, #28a745, #20c997);
}

.btn-danger {
    background: linear-gradient(135deg, #dc3545, #c82333);
}

.btn-warning {
    background: linear-gradient(135deg, #ffc107, #ffca2c);
    color: #856404;
}

.btn-info {
    background: linear-gradient(135deg, #17a2b8, #138496);
}

.btn-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
}

.btn-outline-secondary {
    background: transparent;
    border: 2px solid #6c757d;
    color: #6c757d;
}

.btn-outline-secondary:hover {
    background: linear-gradient(135deg, #6c757d, #495057);
    color: white;
    border-color: transparent;
}

.timeline {
    position: relative;
    padding: 15px 0;
}

.timeline::before {
    content: '';
    position: absolute;
    top: 0;
    left: 20px;
    width: 2px;
    height: 100%;
    background: linear-gradient(to bottom, #28a745, #20c997);
    border-radius: 1px;
}

.timeline-item {
    position: relative;
    padding: 10px 0 10px 40px;
    margin-bottom: 12px;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 15px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: white;
    border: 3px solid #28a745;
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.2);
}

.timeline-content {
    background: white;
    border-radius: 10px;
    padding: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border-left: 3px solid #28a745;
}

.timeline-content h6 {
    font-size: 13px;
    margin-bottom: 5px;
}

.timeline-content small {
    font-size: 10px;
}

.timeline-content p {
    font-size: 11px;
    margin: 5px 0 0 0;
}

.card-body {
    padding: 15px;
    overflow: hidden;
}

@media (max-width: 768px) {
    .action-buttons {
        flex-direction: column;
    }
    
    .receipt-amount {
        font-size: 2rem;
    }
    
    .info-row {
        flex-direction: column;
        gap: 2px;
    }
    
    .info-label {
        max-width: none;
    }
    
    .info-value {
        text-align: left;
        font-size: 13px;
    }
    
    .card-body {
        padding: 12px;
    }
}

* {
    box-sizing: border-box;
}

.col-lg-4 {
    min-width: 0;
}

.card {
    min-width: 0;
    max-width: 100%;
}

.info-section {
    min-width: 0;
    max-width: 100%;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    animation: fadeInUp 0.5s ease-out;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="mb-2 mb-md-0">
                    <h4 class="text-muted mb-1">Payment Details</h4>
                    <p class="text-muted mb-0 small">Order #{{ $payment->order_id }}</p>
                </div>
                <div class="action-buttons">
                    @if($payment->status === 'pending')
                        <button class="btn btn-success btn-sm" onclick="markAsCompleted({{ $payment->id }})">
                            <i class="fas fa-check mr-1"></i>Mark as Paid
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="markAsFailed({{ $payment->id }})">
                            <i class="fas fa-times mr-1"></i>Mark as Failed
                        </button>
                    @endif
                    
                    @if($payment->status === 'completed')
                        <button class="btn btn-warning btn-sm" onclick="processRefund({{ $payment->id }})">
                            <i class="fas fa-undo mr-1"></i>Process Refund
                        </button>
                    @endif
                    
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Payment Receipt -->
            <div class="payment-receipt">
                <div class="receipt-content">
                    <div class="mb-3">
                        <i class="fas fa-receipt" style="font-size: 2.5rem; opacity: 0.8;"></i>
                    </div>
                    <h3>Payment Receipt</h3>
                    <div class="receipt-amount">
                        {{ $payment->formatted_amount }}
                    </div>
                    <div class="status-badge status-{{ $payment->status }}">
                        {{ ucfirst($payment->status) }}
                    </div>
                    <div class="receipt-order-id">
                        Order: {{ $payment->order_id }}
                    </div>
                </div>
            </div>

            <!-- Student Payment Link (Only for Pending Payments) -->
            @if($payment->status === 'pending' && $payment->payment_token)
            <div class="card payment-link-card">
                <div class="card-header">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-link mr-2"></i>Student Payment Link
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">Share this secure link with the student to complete payment:</p>
                    
                    <div class="input-group mb-3">
                        <input type="text" class="form-control payment-link-input" 
                               value="{{ route('payment.student', $payment->payment_token) }}" 
                               id="paymentLink" readonly>
                        <div class="input-group-append">
                            <button class="btn btn-light" type="button" onclick="copyPaymentLink()">
                                <i class="fas fa-copy mr-1"></i> Copy Link
                            </button>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-white-75">
                                <i class="fas fa-whatsapp mr-1"></i>
                                Send via WhatsApp, SMS, or Email
                            </small>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <small class="text-white-75">
                                <i class="fas fa-shield-alt mr-1"></i>
                                Secure payment link expires when paid
                            </small>
                        </div>
                    </div>
                    
                    <!-- Quick Share Options -->
                    <div class="action-buttons">
                        <a href="https://wa.me/?text={{ urlencode('Complete your payment: ' . route('payment.student', $payment->payment_token)) }}" 
                           target="_blank" class="btn btn-success btn-sm">
                            <i class="fab fa-whatsapp mr-1"></i> WhatsApp
                        </a>
                        <a href="mailto:{{ $payment->student->email ?? '' }}?subject=Complete Payment&body={{ urlencode('Please complete your payment using this link: ' . route('payment.student', $payment->payment_token)) }}" 
                           class="btn btn-info btn-sm">
                            <i class="fas fa-envelope mr-1"></i> Email
                        </a>
                        <button class="btn btn-warning btn-sm" onclick="generateQR()">
                            <i class="fas fa-qrcode mr-1"></i> Generate QR
                        </button>
                    </div>
                </div>
            </div>
            @endif

            <!-- Payment Gateway Options (Only for Pending Payments) -->
            @if($payment->status === 'pending')
            <div class="card payment-gateway-card">
                <div class="card-header">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-credit-card mr-2"></i>Payment Gateway Options
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">Choose a payment method to process this payment:</p>
                    
                    <!-- Razorpay Option -->
                    <a href="{{ route('admin.payments.razorpay', $payment) }}" class="gateway-option">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-credit-card gateway-icon"></i>
                            <div>
                                <h6 class="mb-1">Razorpay Gateway</h6>
                                <small class="text-white-75">Automatic verification • 2-3% fees • Multiple payment methods</small>
                            </div>
                        </div>
                    </a>

                    <!-- UPI QR Code Option -->
                    <a href="{{ route('admin.payments.upi', $payment) }}" class="gateway-option">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-qrcode gateway-icon"></i>
                            <div>
                                <h6 class="mb-1">UPI QR Code</h6>
                                <small class="text-white-75">Direct UPI payment • 0% fees • Manual verification required</small>
                            </div>
                        </div>
                    </a>

                    <!-- Manual Payment Option -->
                    <div class="gateway-option" onclick="markAsCompleted({{ $payment->id }})">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-hand-holding-usd gateway-icon"></i>
                            <div>
                                <h6 class="mb-1">Manual Payment</h6>
                                <small class="text-white-75">Cash/Cheque/Bank Transfer • Mark as paid manually</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Student Information -->
            <div class="info-section">
                <h5><i class="fas fa-user-graduate"></i>Student Information</h5>
                @if($payment->student)
                    <div class="info-row">
                        <span class="info-label">Name</span>
                        <span class="info-value">{{ $payment->student->name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email</span>
                        <span class="info-value">{{ $payment->student->email ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Phone</span>
                        <span class="info-value">{{ $payment->student->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Student ID</span>
                        <span class="info-value">{{ $payment->student->student_id ?? 'N/A' }}</span>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Student information not available
                    </div>
                @endif
            </div>

            <!-- Course Information -->
            <div class="info-section">
                <h5><i class="fas fa-book"></i>Course Information</h5>
                @if($payment->course)
                    <div class="info-row">
                        <span class="info-label">Course</span>
                        <span class="info-value">{{ $payment->course->name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Duration</span>
                        <span class="info-value">{{ $payment->course->duration ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Fee</span>
                        <span class="info-value">₹{{ number_format($payment->course->fee ?? 0, 2) }}</span>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        No specific course associated with this payment
                    </div>
                @endif
            </div>

            <!-- Gateway Response -->
            @if($payment->gateway_response)
            <div class="info-section">
                <h5><i class="fas fa-code"></i>Gateway Response</h5>
                <div style="background: #2d3748; color: #e2e8f0; padding: 15px; border-radius: 8px; font-family: 'Courier New', monospace; font-size: 11px; overflow-x: auto; max-height: 200px; overflow-y: auto;">
                    {{ json_encode($payment->gateway_response, JSON_PRETTY_PRINT) }}
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Payment Details -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 font-weight-bold text-white">
                        <i class="fas fa-info-circle mr-2"></i>Payment Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">Order ID</span>
                        <span class="info-value long-text">{{ $payment->order_id }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Amount</span>
                        <span class="info-value">{{ $payment->formatted_amount }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Currency</span>
                        <span class="info-value">{{ strtoupper($payment->currency) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Gateway</span>
                        <span class="info-value">{{ ucfirst($payment->gateway ?: 'Manual') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="info-value">
                            <span class="status-badge status-{{ $payment->status }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </span>
                    </div>
                    @if($payment->gateway_payment_id)
                    <div class="info-row">
                        <span class="info-label">Gateway ID</span>
                        <span class="info-value long-text">{{ $payment->gateway_payment_id }}</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="info-label">Created</span>
                        <span class="info-value">{{ $payment->created_at->format('M d, Y') }}<br><small>{{ $payment->created_at->format('g:i A') }}</small></span>
                    </div>
                    @if($payment->paid_at)
                    <div class="info-row">
                        <span class="info-label">Paid At</span>
                        <span class="info-value">{{ $payment->paid_at->format('M d, Y') }}<br><small>{{ $payment->paid_at->format('g:i A') }}</small></span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment Timeline -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 font-weight-bold text-white">
                        <i class="fas fa-history mr-2"></i>Payment Timeline
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-content">
                                <h6 class="mb-1 text-success font-weight-bold">Payment Created</h6>
                                <small class="text-muted">{{ $payment->created_at->format('M d, Y \a\t g:i A') }}</small>
                                <p class="mb-0">Payment record created in the system</p>
                            </div>
                        </div>

                        @if($payment->status === 'completed' && $payment->paid_at)
                        <div class="timeline-item">
                            <div class="timeline-content">
                                <h6 class="mb-1 text-success font-weight-bold">Payment Completed</h6>
                                <small class="text-muted">{{ $payment->paid_at->format('M d, Y \a\t g:i A') }}</small>
                                <p class="mb-0">Payment successfully completed</p>
                            </div>
                        </div>
                        @endif

                        @if($payment->status === 'failed')
                        <div class="timeline-item">
                            <div class="timeline-content">
                                <h6 class="mb-1 text-danger font-weight-bold">Payment Failed</h6>
                                <small class="text-muted">{{ $payment->updated_at->format('M d, Y \a\t g:i A') }}</small>
                                <p class="mb-0">Payment failed or was marked as failed</p>
                            </div>
                        </div>
                        @endif

                        @if($payment->status === 'refunded')
                        <div class="timeline-item">
                            <div class="timeline-content">
                                <h6 class="mb-1 text-info font-weight-bold">Payment Refunded</h6>
                                <small class="text-muted">{{ $payment->updated_at->format('M d, Y \a\t g:i A') }}</small>
                                <p class="mb-0">Payment was refunded to the student</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
function copyPaymentLink() {
    const linkInput = document.getElementById('paymentLink');
    linkInput.select();
    document.execCommand('copy');
    
    // Show feedback
    const button = event.target.closest('button');
    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check mr-1"></i> Copied!';
    button.classList.remove('btn-light');
    button.classList.add('btn-success');
    
    setTimeout(() => {
        button.innerHTML = originalHtml;
        button.classList.remove('btn-success');
        button.classList.add('btn-light');
    }, 2000);
}

function generateQR() {
    const paymentLink = document.getElementById('paymentLink').value;
    const qrWindow = window.open('', '_blank', 'width=400,height=500');
    qrWindow.document.write(`
        <html>
            <head><title>Payment QR Code</title></head>
            <body style="text-align: center; padding: 20px; font-family: Arial;">
                <h3>Payment QR Code</h3>
                <div id="qrcode"></div>
                <p style="font-size: 12px; color: #666; margin-top: 20px;">
                    Scan this QR code to open payment link
                </p>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode/1.5.3/qrcode.min.js"></script>
                <script>
                    QRCode.toCanvas(document.getElementById('qrcode'), '${paymentLink}', {
                        width: 300,
                        height: 300
                    });
                </script>
            </body>
        </html>
    `);
}

function markAsCompleted(paymentId) {
    if (!confirm('Are you sure you want to mark this payment as completed?')) return;
    
    $.ajax({
        url: `/admin/payments/${paymentId}/mark-completed`,
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            showToast('success', response.message);
            setTimeout(() => location.reload(), 1500);
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Error updating payment';
            showToast('error', message);
        }
    });
}

function markAsFailed(paymentId) {
    if (!confirm('Are you sure you want to mark this payment as failed?')) return;
    
    $.ajax({
        url: `/admin/payments/${paymentId}/mark-failed`,
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            showToast('success', response.message);
            setTimeout(() => location.reload(), 1500);
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Error updating payment';
            showToast('error', message);
        }
    });
}

function processRefund(paymentId) {
    if (!confirm('Are you sure you want to process a refund for this payment?')) return;
    
    const reason = prompt('Please enter the reason for refund (optional):');
    
    $.ajax({
        url: `/admin/payments/${paymentId}/refund`,
        type: 'POST',
        data: { 
            _token: '{{ csrf_token() }}',
            reason: reason
        },
        success: function(response) {
            showToast('success', response.message);
            setTimeout(() => location.reload(), 1500);
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Error processing refund';
            showToast('error', message);
        }
    });
}

function showToast(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'error' ? 'alert-danger' : 'alert-info';
    
    const toast = $(`
        <div class="position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
            <div class="alert ${alertClass} alert-dismissible fade show shadow-lg">
                <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'}-circle mr-2"></i>
                <strong>${message}</strong>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        </div>
    `);
    
    $('body').append(toast);
    setTimeout(() => toast.find('.alert').alert('close'), 4000);
}
</script>
@endsection
