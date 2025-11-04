@extends('layouts.custom-admin')

@section('title', 'UPI Payment')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <h4>Pay via UPI</h4>
                    <p class="lead">Amount: ₹{{ number_format($transaction->amount, 2) }}</p>
                    
                    <!-- QR Code would go here -->
                    <div class="my-4">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($upiString) }}" 
                             alt="UPI QR Code" class="img-fluid">
                    </div>
                    
                    <p>Scan with any UPI app</p>
                    <small class="text-muted">{{ $upiString }}</small>
                    
                    <hr>
                    
                    <a href="{{ route('franchise.wallet.index') }}" class="btn btn-secondary">
                        Done
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
