<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Payment - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            font-family: 'Inter', sans-serif;
        }
        .payment-card { 
            border-radius: 20px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.1); 
            border: none;
        }
        .qr-container { 
            background: white; 
            padding: 30px; 
            border-radius: 15px; 
            border: 3px solid #28a745; 
            margin: 20px 0;
        }
        .qr-container svg { 
            width: 250px; 
            height: 250px; 
            max-width: 100%;
        }
        .payment-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border-radius: 20px 20px 0 0;
            padding: 25px;
        }
        .payment-amount {
            font-size: 3rem;
            font-weight: 800;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        .form-control-lg {
            border-radius: 15px;
            padding: 15px 20px;
            font-size: 16px;
            text-align: center;
            letter-spacing: 2px;
            border: 2px solid #e9ecef;
        }
        .form-control-lg:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .btn-success-custom {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 25px;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: 600;
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
            transition: all 0.3s ease;
        }
        .btn-success-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(40, 167, 69, 0.4);
        }
        .info-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .step-number {
            background: #28a745;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 12px;
            margin-right: 8px;
        }
        @media (max-width: 768px) {
            .payment-amount { font-size: 2rem; }
            .qr-container svg { width: 200px; height: 200px; }
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card payment-card shadow-lg">
                    <div class="payment-header text-center text-white">
                        <h3 class="mb-2"><i class="fas fa-qrcode me-2"></i>UPI Payment</h3>
                        <div class="payment-amount">₹{{ number_format($payment->amount, 2) }}</div>
                    </div>
                    
                    <div class="card-body p-4">
                        <!-- Payment Details -->
                        <div class="info-card p-3 mb-4">
                            <div class="row g-2 small">
                                <div class="col-5 fw-bold text-muted">Student:</div>
                                <div class="col-7">{{ $payment->student->name }}</div>
                                <div class="col-5 fw-bold text-muted">Course:</div>
                                <div class="col-7">{{ $payment->course->name ?? 'N/A' }}</div>
                                <div class="col-5 fw-bold text-muted">Order ID:</div>
                                <div class="col-7"><code class="small">{{ $payment->order_id }}</code></div>
                            </div>
                        </div>
                        
                        <!-- QR Code -->
                        <div class="text-center">
                            <div class="qr-container">
                                {!! base64_decode($qrData['qr_code']) !!}
                                <p class="mt-3 mb-0 text-muted small">
                                    <i class="fas fa-mobile-alt me-1"></i>
                                    Scan with any UPI app to pay
                                </p>
                            </div>
                        </div>
                        
                        <!-- Instructions -->
                        <div class="info-card p-3 mb-4">
                            <h6 class="text-success mb-3">
                                <i class="fas fa-info-circle me-1"></i>
                                How to Pay:
                            </h6>
                            <div class="small">
                                <div class="mb-2"><span class="step-number">1</span>Open your UPI app</div>
                                <div class="mb-2"><span class="step-number">2</span>Tap "Scan QR Code"</div>
                                <div class="mb-2"><span class="step-number">3</span>Scan the QR code above</div>
                                <div class="mb-2"><span class="step-number">4</span>Verify amount: ₹{{ number_format($payment->amount, 2) }}</div>
                                <div class="mb-2"><span class="step-number">5</span>Enter UPI PIN & pay</div>
                                <div class="mb-0"><span class="step-number">6</span>Copy transaction ID below</div>
                            </div>
                        </div>
                        
                        <!-- Payment Confirmation Form -->
                        <form method="POST" action="{{ route('payment.student.confirm', $payment->payment_token) }}" class="text-center">
                            @csrf
                            <div class="mb-4">
                                <label for="transaction_id" class="form-label fw-bold text-success">
                                    <i class="fas fa-receipt me-2"></i>Enter UPI Transaction ID:
                                </label>
                                <input type="text" name="transaction_id" id="transaction_id" 
                                       class="form-control form-control-lg" 
                                       placeholder="e.g., 123456789012" 
                                       autocomplete="off"
                                       required>
                                <div class="form-text text-muted mt-2">
                                    <i class="fas fa-search me-1"></i>
                                    Find this in your UPI app after payment success
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-success-custom btn-lg w-100 mb-3">
                                <i class="fas fa-check-circle me-2"></i>Confirm Payment
                            </button>
                        </form>
                        
                        <!-- Payment Info -->
                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                Secure Payment • Pay to: <strong>{{ $qrData['upi_id'] }}</strong>
                            </small>
                        </div>
                    </div>
                </div>
                
                <!-- Alternative Payment Methods -->
                <div class="text-center mt-3">
                    <small class="text-white-50">
                        Can't scan QR? 
                        <a href="{{ $qrData['upi_url'] }}" class="text-white fw-bold text-decoration-none">
                            Click here to open UPI app
                        </a>
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-format and validate transaction ID
        document.getElementById('transaction_id').addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9A-Za-z]/g, '');
            if (value.length > 20) value = value.substr(0, 20);
            e.target.value = value;
            
            // Visual feedback
            if (value.length >= 10) {
                e.target.classList.remove('is-invalid');
                e.target.classList.add('is-valid');
            } else {
                e.target.classList.remove('is-valid');
                e.target.classList.add('is-invalid');
            }
        });

        // Form submission validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const transactionId = document.getElementById('transaction_id').value;
            if (transactionId.length < 8) {
                e.preventDefault();
                alert('Please enter a valid transaction ID (minimum 8 characters)');
                return;
            }
        });
    </script>
</body>
</html>
