@extends('layouts.custom-admin')

@section('page-title', 'Razorpay Payment')

@section('content')
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center bg-primary text-white">
                    <h5><i class="fas fa-credit-card mr-2"></i>Razorpay Payment</h5>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-primary">₹{{ number_format($payment->amount, 2) }}</h2>
                    <p class="text-muted">Payment for {{ $payment->student->name }}</p>
                    @if($payment->course)
                        <p class="text-muted small">Course: {{ $payment->course->name }}</p>
                    @endif
                    
                    <button id="rzp-button1" class="btn btn-primary btn-lg">
                        <i class="fas fa-credit-card mr-2"></i>Pay Now
                    </button>
                    
                    <div class="mt-3">
                        <small class="text-muted">Secure payment powered by Razorpay</small>
                    </div>
                    
                    <div class="mt-3">
                        <small class="text-info">
                            <i class="fas fa-info-circle mr-1"></i>
                            Supports Cards, UPI, Net Banking, Wallets
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
var options = {
    "key": "{{ config('services.razorpay.key') }}",
    "amount": {{ $order['amount'] }},
    "currency": "{{ $order['currency'] }}",
    "name": "{{ config('app.name') }}",
    "description": "Payment for {{ $payment->course->name ?? 'Course' }}",
    "order_id": "{{ $order['order_id'] }}",
    "handler": function (response) {
        // Show loading
        document.getElementById('rzp-button1').innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Verifying...';
        document.getElementById('rzp-button1').disabled = true;
        
        // Verify payment
        fetch('{{ route("admin.payments.verify-razorpay") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                razorpay_payment_id: response.razorpay_payment_id,
                razorpay_order_id: response.razorpay_order_id,
                razorpay_signature: response.razorpay_signature
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Payment successful!');
                window.location.href = '{{ route("admin.payments.show", $payment) }}';
            } else {
                alert('Payment verification failed!');
                resetButton();
            }
        })
        .catch(error => {
            alert('Payment verification error!');
            resetButton();
        });
    },
    "prefill": {
        "name": "{{ $payment->student->name }}",
        "email": "{{ $payment->student->email ?? '' }}",
        // FIX: Don't prefill phone - let user enter manually
    },
    "theme": {
        "color": "#007bff"
    },
    "modal": {
        "ondismiss": function(){
            resetButton();
        }
    }
};

var rzp1 = new Razorpay(options);

document.getElementById('rzp-button1').onclick = function(e) {
    rzp1.open();
    e.preventDefault();
}

function resetButton() {
    document.getElementById('rzp-button1').innerHTML = '<i class="fas fa-credit-card mr-2"></i>Pay Now';
    document.getElementById('rzp-button1').disabled = false;
}
</script>
@endsection
@extends('layouts.custom-admin')

@section('page-title', 'Razorpay Payment')

@section('content')
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header text-center bg-primary text-white">
                    <h5><i class="fas fa-credit-card mr-2"></i>Razorpay Payment Gateway</h5>
                </div>
                <div class="card-body text-center">
                    <div class="payment-info mb-4">
                        <h2 class="text-primary mb-2">₹{{ number_format($payment->amount, 2) }}</h2>
                        <p class="text-muted mb-1">Payment for <strong>{{ $payment->student->name }}</strong></p>
                        @if($payment->course)
                            <p class="text-muted small">Course: <strong>{{ $payment->course->name }}</strong></p>
                        @endif
                        
                        <div class="student-info bg-light p-3 rounded mt-3">
                            <small class="text-muted">
                                <i class="fas fa-user mr-1"></i> {{ $payment->student->name }}<br>
                                <i class="fas fa-envelope mr-1"></i> {{ $payment->student->email ?? 'No email' }}<br>
                                @if($payment->student->phone)
                                    <i class="fas fa-phone mr-1"></i> {{ $payment->student->phone }}
                                @else
                                    <i class="fas fa-phone mr-1"></i> <span class="text-warning">Phone number will be requested</span>
                                @endif
                            </small>
                        </div>
                    </div>
                    
                    <button id="rzp-button1" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-credit-card mr-2"></i>Pay Now
                    </button>
                    
                    <div class="mt-3">
                        <small class="text-muted">Secure payment powered by Razorpay</small><br>
                        <small class="text-info">
                            <i class="fas fa-shield-alt mr-1"></i>
                            256-bit SSL • PCI DSS Compliant
                        </small>
                    </div>
                    
                    <div class="payment-methods mt-3">
                        <small class="text-muted d-block mb-2">Accepted Payment Methods:</small>
                        <div class="d-flex justify-content-center">
                            <span class="badge badge-outline-info mr-2">Cards</span>
                            <span class="badge badge-outline-success mr-2">UPI</span>
                            <span class="badge badge-outline-primary mr-2">Net Banking</span>
                            <span class="badge badge-outline-warning">Wallets</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
.card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    padding: 20px;
}

.payment-info h2 {
    font-weight: 700;
    font-size: 2.5rem;
}

.student-info {
    border-left: 4px solid #007bff;
}

.btn-lg {
    padding: 15px 40px;
    font-size: 18px;
    font-weight: 600;
    border-radius: 25px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,123,255,0.3);
}

.badge-outline-info { border: 1px solid #17a2b8; color: #17a2b8; }
.badge-outline-success { border: 1px solid #28a745; color: #28a745; }
.badge-outline-primary { border: 1px solid #007bff; color: #007bff; }
.badge-outline-warning { border: 1px solid #ffc107; color: #ffc107; }
</style>
@endsection

@section('js')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
var options = {
    "key": "{{ config('services.razorpay.key') }}",
    "amount": {{ $order['amount'] }},
    "currency": "{{ $order['currency'] }}",
    "name": "{{ config('app.name') }}",
    "description": "Payment for {{ $payment->course->name ?? 'Course Fee' }}",
    "order_id": "{{ $order['order_id'] }}",
    "handler": function (response) {
        // Show processing state
        updateButtonState('processing');
        
        // Verify payment with server
        fetch('{{ route("admin.payments.verify-razorpay") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                razorpay_payment_id: response.razorpay_payment_id,
                razorpay_order_id: response.razorpay_order_id,
                razorpay_signature: response.razorpay_signature
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateButtonState('success');
                showSuccessMessage();
                
                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = '{{ route("admin.payments.show", $payment) }}';
                }, 2000);
            } else {
                updateButtonState('error');
                showErrorMessage('Payment verification failed: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Verification error:', error);
            updateButtonState('error');
            showErrorMessage('Payment verification failed. Please contact support.');
        });
    },
    "prefill": {
        "name": "{{ $payment->student->name }}",
        "email": "{{ $payment->student->email ?? '' }}"
        @if($payment->student->phone)
        ,"contact": "{{ preg_replace('/[^0-9]/', '', $payment->student->phone) }}"
        @endif
    },
    "theme": {
        "color": "#007bff"
    },
    "modal": {
        "ondismiss": function(){
            updateButtonState('initial');
            console.log('Razorpay modal closed by user');
        }
    },
    "retry": {
        "enabled": true,
        "max_count": 1
    }
};

var rzp1 = new Razorpay(options);

// Initialize button click handler
document.getElementById('rzp-button1').onclick = function(e) {
    e.preventDefault();
    console.log('Opening Razorpay checkout...');
    rzp1.open();
}

// Button state management
function updateButtonState(state) {
    const button = document.getElementById('rzp-button1');
    
    switch(state) {
        case 'processing':
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
            button.disabled = true;
            button.className = 'btn btn-warning btn-lg px-5';
            break;
            
        case 'success':
            button.innerHTML = '<i class="fas fa-check mr-2"></i>Payment Successful!';
            button.disabled = true;
            button.className = 'btn btn-success btn-lg px-5';
            break;
            
        case 'error':
            button.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Payment Failed';
            button.disabled = false;
            button.className = 'btn btn-danger btn-lg px-5';
            setTimeout(() => updateButtonState('initial'), 3000);
            break;
            
        case 'initial':
        default:
            button.innerHTML = '<i class="fas fa-credit-card mr-2"></i>Pay Now';
            button.disabled = false;
            button.className = 'btn btn-primary btn-lg px-5';
            break;
    }
}

// Success message
function showSuccessMessage() {
    const alertHtml = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            <strong>Payment Successful!</strong> Redirecting to payment details...
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `;
    
    document.querySelector('.card-body').insertAdjacentHTML('afterbegin', alertHtml);
}

// Error message
function showErrorMessage(message) {
    const alertHtml = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>Payment Error!</strong> ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `;
    
    document.querySelector('.card-body').insertAdjacentHTML('afterbegin', alertHtml);
}

// Debug info
console.log('Razorpay Options:', {
    key: options.key,
    amount: options.amount,
    currency: options.currency,
    order_id: options.order_id,
    prefill: options.prefill
});
</script>
@endsection
