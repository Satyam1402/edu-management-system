@extends('layouts.custom-admin')

@section('page-title', 'UPI QR Code Payment')

@section('content')
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header text-center bg-success text-white">
                    <h5><i class="fas fa-qrcode mr-2"></i>UPI QR Code Payment</h5>
                </div>
                <div class="card-body text-center">
                    <!-- Payment Amount -->
                    <div class="payment-amount mb-4">
                        <h2 class="text-success mb-2">₹{{ number_format($payment->amount, 2) }}</h2>
                        <p class="text-muted mb-1">Payment for <strong>{{ $payment->student->name }}</strong></p>
                        @if($payment->course)
                            <p class="text-muted small">Course: <strong>{{ $payment->course->name }}</strong></p>
                        @endif
                    </div>
                    
                    <!-- QR Code Display -->
                    <div class="qr-code-container mb-4">
                        <div class="qr-code-wrapper">
                            {!! base64_decode($qrData['qr_code']) !!}
                        </div>
                        <small class="text-muted d-block mt-2">Scan with any UPI app</small>
                    </div>
                    
                    <!-- Payment Instructions -->
                    <div class="payment-instructions bg-light p-3 rounded mb-4">
                        <h6><i class="fas fa-mobile-alt text-success mr-2"></i>How to Pay:</h6>
                        <ol class="text-left small mb-0">
                            <li>Open any UPI app (PhonePe, Google Pay, Paytm, BHIM, etc.)</li>
                            <li>Tap on "Scan QR Code" or camera icon</li>
                            <li>Scan the QR code above</li>
                            <li>Verify amount: ₹{{ number_format($payment->amount, 2) }}</li>
                            <li>Enter your UPI PIN and complete payment</li>
                            <li>Take screenshot of success message</li>
                        </ol>
                    </div>
                    
                    <!-- UPI Details -->
                    <div class="upi-details mb-4 p-3 bg-white border rounded">
                        <div class="row">
                            <div class="col-md-6">
                                <small><strong>Pay to:</strong> {{ $qrData['upi_id'] }}</small>
                            </div>
                            <div class="col-md-6">
                                <small><strong>Amount:</strong> ₹{{ number_format($payment->amount, 2) }}</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Manual UPI Link (Fallback) -->
                    <div class="manual-upi mb-4">
                        <p class="small text-muted mb-2">Can't scan QR? Try this:</p>
                        <a href="{{ $qrData['upi_url'] }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-external-link-alt mr-1"></i>Open UPI App
                        </a>
                    </div>
                    
                    <!-- Payment Confirmation Form -->
                    <div class="payment-confirmation">
                        <form method="POST" action="{{ route('admin.payments.confirm-upi', $payment) }}" id="upiConfirmForm">
                            @csrf
                            <div class="form-group">
                                <label for="transaction_id" class="font-weight-bold">
                                    <i class="fas fa-receipt mr-1"></i>Enter UPI Transaction ID:
                                </label>
                                <input type="text" class="form-control form-control-lg" 
                                       id="transaction_id" name="transaction_id" 
                                       placeholder="e.g., 123456789012" 
                                       pattern="[0-9]{12}" 
                                       title="Please enter a 12-digit transaction ID"
                                       required>
                                <small class="form-text text-muted">
                                    Find this in your UPI app after successful payment
                                </small>
                            </div>
                            
                            <button type="submit" class="btn btn-success btn-lg px-4">
                                <i class="fas fa-check-circle mr-2"></i>Confirm Payment
                            </button>
                        </form>
                    </div>
                    
                    <!-- Help Text -->
                    <div class="mt-4">
                        <small class="text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            Payment will be verified once you submit the transaction ID
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
.qr-code-container {
    background: white;
    border: 3px solid #28a745;
    border-radius: 15px;
    padding: 20px;
    margin: 0 auto;
    max-width: 300px;
}

.qr-code-wrapper svg {
    width: 250px;
    height: 250px;
    max-width: 100%;
}

.payment-amount h2 {
    font-size: 3rem;
    font-weight: 700;
}

.card {
    border: none;
    border-radius: 15px;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
    padding: 20px;
}

.payment-instructions {
    border-left: 4px solid #28a745;
}

.upi-details {
    font-size: 14px;
}

.btn-lg {
    padding: 12px 30px;
    font-size: 16px;
    border-radius: 25px;
}

.form-control-lg {
    padding: 15px;
    font-size: 16px;
    border-radius: 10px;
    text-align: center;
    letter-spacing: 1px;
}

@media (max-width: 768px) {
    .payment-amount h2 {
        font-size: 2rem;
    }
    
    .qr-code-wrapper svg {
        width: 200px;
        height: 200px;
    }
}
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Format transaction ID input
    $('#transaction_id').on('input', function() {
        let value = $(this).val().replace(/\D/g, ''); // Remove non-digits
        $(this).val(value);
    });
    
    // Form validation
    $('#upiConfirmForm').on('submit', function(e) {
        const transactionId = $('#transaction_id').val();
        
        if (transactionId.length < 10) {
            e.preventDefault();
            alert('Please enter a valid transaction ID (minimum 10 digits)');
            return false;
        }
        
        if (confirm('Are you sure you have completed the payment and entered the correct transaction ID?')) {
            return true;
        } else {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endsection
