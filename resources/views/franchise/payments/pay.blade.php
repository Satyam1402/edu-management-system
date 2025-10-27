@extends('layouts.custom-admin')

@section('page-title', 'Complete Payment')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card"></i> Complete Payment
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="payment-details mb-4">
                        <h6>Student: {{ $payment->student->name }}</h6>
                        <h6>Course: {{ $payment->course ? $payment->course->name : 'Certificate Fee' }}</h6>
                        <h4 class="text-success">Amount: {{ $payment->formatted_amount }}</h4>
                        <small class="text-muted">Order ID: {{ $payment->order_id }}</small>
                    </div>

                    <hr>

                    <button id="pay-button" class="btn btn-success btn-lg">
                        <i class="fas fa-credit-card"></i> Pay Now
                    </button>

                    <div class="mt-3">
                        <a href="{{ route('franchise.payments.show', $payment->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>

                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt"></i> Secured by Razorpay
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const payButton = document.getElementById('pay-button');

    const options = {
        "key": "{{ config('services.razorpay.key') }}",
        "amount": {{ $payment->amount * 100 }},
        "currency": "{{ $payment->currency }}",
        "name": "EduManagement System",
        "description": "Certificate Payment for {{ $payment->student->name }}",
        "order_id": "{{ $payment->gateway_order_id }}",
        "handler": function (response) {
            // 🎯 DIRECT REDIRECT - NO VERIFICATION
            alert('Payment Successful! Redirecting to certificate request...');
            window.location.href = "{{ route('franchise.certificate-requests.create') }}";
        },
        "prefill": {
            "name": "{{ $payment->student->name }}",
            "email": "{{ $payment->student->email }}"
        },
        "theme": {
            "color": "#667eea"
        }
    };

    const rzp = new Razorpay(options);

    payButton.onclick = function(e) {
        e.preventDefault();
        rzp.open();
    };
});
</script>
@endsection

