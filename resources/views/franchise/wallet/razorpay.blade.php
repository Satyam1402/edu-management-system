@extends('layouts.custom-admin')

@section('title', 'Complete Payment')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <h4>Complete Your Payment</h4>
                    <p class="lead">Amount: ₹{{ number_format($transaction->amount, 2) }}</p>
                    <button id="rzp-button" class="btn btn-primary btn-lg">
                        <i class="fas fa-lock"></i> Pay with Razorpay
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
var options = {
    "key": "{{ config('services.razorpay.key') }}",
    "amount": "{{ $order->amount }}",
    "currency": "INR",
    "name": "Wallet Top-up",
    "description": "Add funds to wallet",
    "order_id": "{{ $order->id }}",
    "handler": function (response){
        // Send payment details to server for verification
        window.location.href = "{{ route('franchise.wallet.verify-razorpay') }}?razorpay_payment_id=" + 
            response.razorpay_payment_id + 
            "&razorpay_order_id=" + response.razorpay_order_id + 
            "&razorpay_signature=" + response.razorpay_signature +
            "&transaction_id={{ $transaction->id }}";
    },
    "theme": {
        "color": "#3399cc"
    }
};

var rzp = new Razorpay(options);

document.getElementById('rzp-button').onclick = function(e){
    rzp.open();
    e.preventDefault();
}
</script>
@endsection
