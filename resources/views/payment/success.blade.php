<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }
        .success-card { 
            border-radius: 20px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.1); 
            border: none;
            overflow: hidden;
        }
        .success-animation {
            animation: bounceIn 1s ease-out;
        }
        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
        .checkmark-circle {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border-radius: 50%;
            width: 120px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }
        .payment-summary {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            border-left: 5px solid #28a745;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card success-card">
                    <div class="card-body text-center p-5">
                        <div class="success-animation">
                            <div class="checkmark-circle">
                                <i class="fas fa-check text-white" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                        
                        <h2 class="text-success fw-bold mb-3">Payment Successful!</h2>
                        <p class="lead text-muted mb-4">Thank you for your payment</p>
                        
                        <div class="payment-summary p-4 mb-4">
                            <div class="row g-2">
                                <div class="col-5 text-end fw-bold text-muted">Amount:</div>
                                <div class="col-7 text-start fw-bold text-success">₹{{ number_format($payment->amount, 2) }}</div>
                                
                                <div class="col-5 text-end fw-bold text-muted">Student:</div>
                                <div class="col-7 text-start">{{ $payment->student->name }}</div>
                                
                                <div class="col-5 text-end fw-bold text-muted">Course:</div>
                                <div class="col-7 text-start">{{ $payment->course->name ?? 'N/A' }}</div>
                                
                                <div class="col-5 text-end fw-bold text-muted">Order ID:</div>
                                <div class="col-7 text-start"><code>{{ $payment->order_id }}</code></div>
                                
                                <div class="col-5 text-end fw-bold text-muted">Transaction ID:</div>
                                <div class="col-7 text-start"><code class="text-success">{{ $payment->gateway_payment_id }}</code></div>
                                
                                <div class="col-5 text-end fw-bold text-muted">Date & Time:</div>
                                <div class="col-7 text-start">{{ $payment->paid_at->format('d M Y, g:i A') }}</div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Payment Confirmed!</strong><br>
                            You will receive a receipt via email/SMS shortly.
                        </div>
                        
                        <div class="mt-4">
                            <small class="text-muted">
                                <i class="fas fa-lock me-1"></i>
                                This is a secure transaction processed via UPI
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
