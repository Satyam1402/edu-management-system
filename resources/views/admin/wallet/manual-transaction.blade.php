@extends('layouts.custom-admin')

@section('page-title', 'Manual Wallet Transaction')

@section('css')
    <style>
        .transaction-form {
            max-width: 800px;
            margin: 0 auto;
        }
        .form-card {
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
    </style>
    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-1">Manual Wallet Transaction</h4>
            <p class="text-muted mb-0">Add or deduct credits from franchise wallet manually</p>
        </div>
    </div>

    <div class="transaction-form">
        <div class="card form-card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-exchange-alt mr-2"></i>New Transaction</h5>
            </div>
            <div class="card-body p-4">
                <form id="manualTransactionForm">
                    @csrf
                    <div class="form-group">
                        <label for="franchise_id">Select Franchise <span class="text-danger">*</span></label>
                        <select class="form-control form-control-lg" id="franchise_id" name="franchise_id" required>
                            <option value="">-- Select Franchise --</option>
                            @foreach($franchises as $franchise)
                                <option value="{{ $franchise->id }}" data-balance="{{ $franchise->wallet->balance ?? 0 }}">
                                    {{ $franchise->name }} (Balance: ₹{{ number_format($franchise->wallet->balance ?? 0, 2) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type">Transaction Type <span class="text-danger">*</span></label>
                                <select class="form-control form-control-lg" id="type" name="type" required>
                                    <option value="">-- Select Type --</option>
                                    <option value="credit">Credit (Add Money)</option>
                                    <option value="debit">Debit (Deduct Money)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="amount">Amount <span class="text-danger">*</span></label>
                                <input type="number" class="form-control form-control-lg" id="amount" name="amount" 
                                       placeholder="Enter amount" step="0.01" min="1" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="4"
                                  placeholder="Enter transaction description..." required></textarea>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Note:</strong> This transaction will be immediately processed and reflected in the franchise wallet.
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.wallet.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-left mr-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                            <i class="fas fa-check mr-2"></i>Process Transaction
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Current Balance Display -->
        <div class="card mt-3" id="balanceCard" style="display:none;">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Current Wallet Balance</h6>
                <h3 class="font-weight-bold text-success mb-0" id="currentBalance">₹0.00</h3>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#franchise_id').change(function() {
            const balance = $(this).find(':selected').data('balance');
            if (balance !== undefined) {
                $('#currentBalance').text('₹' + parseFloat(balance).toFixed(2));
                $('#balanceCard').slideDown();
            } else {
                $('#balanceCard').slideUp();
            }
        });

        $('#manualTransactionForm').submit(function(e) {
            e.preventDefault();

            const type = $('#type').val();
            const amount = $('#amount').val();

            if (!confirm(`Are you sure you want to ${type} ₹${parseFloat(amount).toFixed(2)}?`)) {
                return;
            }

            $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');

            $.ajax({
                url: '{{ route("admin.wallet.process-manual") }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    // Toastr fallback
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message);
                    } else {
                        alert(response.message);
                    }
                    $('#submitBtn').prop('disabled', false).html('<i class="fas fa-check mr-2"></i>Process Transaction');
                    if (response.redirect_url) {
                        setTimeout(function() {
                            window.location.href = response.redirect_url;
                        }, 1200);
                    } else {
                        setTimeout(function() {
                            window.location.href = '{{ route("admin.wallet.index") }}';
                        }, 1200);
                    }
                },
                error: function(xhr) {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(xhr.responseJSON?.message || 'Error occurred');
                    } else {
                        alert(xhr.responseJSON?.message || 'Error occurred');
                    }
                    $('#submitBtn').prop('disabled', false).html('<i class="fas fa-check mr-2"></i>Process Transaction');
                }
            });
        });
    });
    </script>
@endsection
