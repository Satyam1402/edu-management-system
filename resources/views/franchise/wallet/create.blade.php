@extends('layouts.custom-admin')

@section('title', 'Add Funds to Wallet')
@section('page-title', 'Add Funds to Wallet')

@section('css')
<style>
    .payment-method-card {
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }
    .payment-method-card:hover {
        border-color: #007bff;
        box-shadow: 0 4px 15px rgba(0,123,255,0.2);
    }
    .payment-method-card.active {
        border-color: #007bff;
        background: #f0f7ff;
    }
    .payment-method-card i {
        font-size: 3rem;
        margin-bottom: 10px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-wallet"></i> Top-up Wallet</h5>
                </div>
                <div class="card-body">
                    <!-- Current Balance -->
                    <div class="alert alert-info">
                        <strong><i class="fas fa-info-circle"></i> Current Balance:</strong> 
                        ₹{{ number_format($wallet->balance, 2) }}
                    </div>

                    <form action="{{ route('franchise.wallet.store') }}" method="POST" id="walletForm">
                        @csrf

                        <!-- Amount Input -->
                        <div class="form-group">
                            <label for="amount">Amount to Add <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-rupee-sign"></i></span>
                                </div>
                                <input type="number" name="amount" id="amount" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       value="{{ old('amount') }}" 
                                       min="100" 
                                       max="100000" 
                                       placeholder="Enter amount"
                                       required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Minimum: ₹100 | Maximum: ₹1,00,000
                            </small>
                        </div>

                        <!-- Quick Amount Buttons -->
                        <div class="form-group">
                            <label>Quick Select:</label>
                            <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                                <button type="button" class="btn btn-outline-primary" onclick="setAmount(500)">₹500</button>
                                <button type="button" class="btn btn-outline-primary" onclick="setAmount(1000)">₹1,000</button>
                                <button type="button" class="btn btn-outline-primary" onclick="setAmount(2000)">₹2,000</button>
                                <button type="button" class="btn btn-outline-primary" onclick="setAmount(5000)">₹5,000</button>
                            </div>
                        </div>

                        <!-- Payment Method Selection -->
                        <div class="form-group">
                            <label>Select Payment Method <span class="text-danger">*</span></label>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="payment-method-card active" onclick="selectPayment('razorpay')">
                                        <input type="radio" name="payment_method" value="razorpay" id="razorpay" checked hidden>
                                        <i class="fas fa-bolt text-primary"></i>
                                        <h6>Razorpay</h6>
                                        <small class="text-muted">Card/UPI/Net Banking</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="payment-method-card" onclick="selectPayment('upi')">
                                        <input type="radio" name="payment_method" value="upi" id="upi" hidden>
                                        <i class="fab fa-google-pay text-success"></i>
                                        <h6>UPI</h6>
                                        <small class="text-muted">Google Pay/PhonePe</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="payment-method-card" onclick="selectPayment('bank_transfer')">
                                        <input type="radio" name="payment_method" value="bank_transfer" id="bank" hidden>
                                        <i class="fas fa-university text-info"></i>
                                        <h6>Bank Transfer</h6>
                                        <small class="text-muted">NEFT/RTGS/IMPS</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary btn-lg btn-block">
                                <i class="fas fa-credit-card"></i> Proceed to Payment
                            </button>
                            <a href="{{ route('franchise.wallet.index') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
function setAmount(amount) {
    document.getElementById('amount').value = amount;
}

function selectPayment(method) {
    // Remove active class from all cards
    document.querySelectorAll('.payment-method-card').forEach(card => {
        card.classList.remove('active');
    });
    
    // Add active class to selected card
    event.currentTarget.classList.add('active');
    
    // Check the radio button
    document.getElementById(method).checked = true;
}
</script>
@endsection
