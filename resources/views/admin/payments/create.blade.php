@extends('layouts.custom-admin')

@section('page-title', 'Create Payment')

@section('content')
<div class="container-fluid p-4">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3>Create New Payment</h3>
            <p class="text-muted mb-0">Create payment records for students with multiple gateway options</p>
        </div>
        <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to Payments
        </a>
    </div>

    <div class="row">
        <!-- Main Form -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-credit-card mr-2"></i>Payment Details</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.payments.store') }}" id="paymentForm">
                        @csrf
                        
                        <!-- Student Selection -->
                        <div class="form-group">
                            <label for="student_id"><i class="fas fa-user mr-1"></i> Student *</label>
                            <select name="student_id" id="student_id" class="form-control" required>
                                <option value="">Select Student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->name }} - {{ $student->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Course Selection -->
                        <div class="form-group">
                            <label for="course_id"><i class="fas fa-book mr-1"></i> Course *</label>
                            <select name="course_id" id="course_id" class="form-control" required>
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" 
                                            data-fee="{{ $course->fee ?? 0 }}"
                                            {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }} - ₹{{ number_format($course->fee ?? 0, 2) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Amount Field (Fixed Version) -->
                        <div class="form-group">
                            <label for="amount"><i class="fas fa-rupee-sign mr-1"></i> Amount *</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">₹</span>
                                </div>
                                <input type="text" 
                                       name="amount" 
                                       id="amount" 
                                       class="form-control" 
                                       required 
                                       value="{{ old('amount') }}"
                                       placeholder="0.00"
                                       pattern="[0-9]+(\.[0-9]{1,2})?"
                                       title="Please enter a valid amount (e.g., 1000 or 1000.50)">
                            </div>
                            <small class="text-success" id="amount-help">Select a course above to auto-fill the amount.</small>
                            @error('amount')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Gateway Selection -->
                        <div class="form-group">
                            <label><i class="fas fa-credit-card mr-1"></i> Payment Method *</label>
                            <div class="row mt-3">
                                <div class="col-md-4 mb-3">
                                    <div class="card text-center gateway-card h-100" data-gateway="manual">
                                        <div class="card-body d-flex flex-column justify-content-center">
                                            <i class="fas fa-hand-paper fa-2x mb-2 text-secondary"></i>
                                            <h6 class="mb-1">Manual</h6>
                                            <small class="text-muted">Cash/Bank Transfer</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card text-center gateway-card h-100" data-gateway="razorpay">
                                        <div class="card-body d-flex flex-column justify-content-center">
                                            <i class="fas fa-credit-card fa-2x mb-2 text-primary"></i>
                                            <h6 class="mb-1">Razorpay</h6>
                                            <small class="text-muted">Card/NetBanking/UPI</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card text-center gateway-card h-100" data-gateway="upi">
                                        <div class="card-body d-flex flex-column justify-content-center">
                                            <i class="fas fa-qrcode fa-2x mb-2 text-success"></i>
                                            <h6 class="mb-1">UPI QR Code</h6>
                                            <small class="text-muted">Scan & Pay</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="gateway" id="selected_gateway" value="{{ old('gateway', 'manual') }}" required>
                            @error('gateway')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-plus mr-2"></i>Create Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Instructions Sidebar -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle mr-2"></i>Instructions</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6><i class="fas fa-list-ol text-info mr-2"></i>How it works:</h6>
                        <ol class="small pl-3">
                            <li>Select a student from the dropdown</li>
                            <li>Choose a course - <strong class="text-success">amount will auto-fill</strong></li>
                            <li>Pick your preferred payment method</li>
                            <li>Click "Create Payment" to proceed</li>
                        </ol>
                    </div>
                    
                    <div class="mb-4">
                        <h6><i class="fas fa-edit text-warning mr-2"></i>About Amount:</h6>
                        <ul class="small pl-3">
                            <li>Amount auto-fills from course fee</li>
                            <li>You can manually edit the amount if needed</li>
                            <li>Supports decimal values (e.g., 1000.50)</li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h6><i class="fas fa-payment text-success mr-2"></i>Payment Methods:</h6>
                        <div class="small">
                            <div class="mb-2">
                                <strong class="text-secondary">Manual:</strong> Record cash payments or bank transfers manually
                            </div>
                            <div class="mb-2">
                                <strong class="text-primary">Razorpay:</strong> Online payments via cards, UPI, net banking, wallets
                            </div>
                            <div class="mb-2">
                                <strong class="text-success">UPI QR:</strong> Generate QR code for direct UPI app payments
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <small><i class="fas fa-lightbulb mr-1"></i> <strong>Pro Tip:</strong> Select the course first to automatically get the correct amount!</small>
                    </div>

                    <div class="alert alert-warning">
                        <small><i class="fas fa-exclamation-triangle mr-1"></i> <strong>Note:</strong> For Razorpay and UPI payments, you'll be redirected to the payment interface after creation.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
.gateway-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid #e9ecef;
    min-height: 120px;
}

.gateway-card:hover {
    border-color: #007bff;
    box-shadow: 0 4px 15px rgba(0,123,255,0.2);
    transform: translateY(-3px);
}

.gateway-card.active {
    border-color: #007bff;
    background: linear-gradient(135deg, rgba(0,123,255,0.1), rgba(0,123,255,0.05));
    box-shadow: 0 8px 25px rgba(0,123,255,0.3);
    transform: translateY(-2px);
}

.gateway-card.active i {
    color: #007bff !important;
}

.gateway-card.active h6 {
    color: #007bff;
    font-weight: bold;
}

.form-group label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
}

.btn-lg {
    padding: 12px 30px;
    font-size: 16px;
    font-weight: 600;
}

.card {
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-radius: 10px;
}

.card-header {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    border-radius: 10px 10px 0 0 !important;
}

#amount {
    font-size: 16px;
    font-weight: 500;
}

#amount-help {
    font-size: 12px;
    margin-top: 5px;
}

.alert {
    border-radius: 8px;
}

.text-success { color: #28a745 !important; }
.text-info { color: #007bff !important; }
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Auto-fill amount when course is selected
    $('#course_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const courseFee = selectedOption.data('fee');
        
        if (courseFee && courseFee > 0) {
            $('#amount').val(courseFee);
            $('#amount-help')
                .text('Amount auto-filled from course fee: ₹' + courseFee + '. You can modify it if needed.')
                .removeClass('text-success')
                .addClass('text-info');
        } else {
            $('#amount').val('');
            $('#amount-help')
                .text('Please enter the amount manually as this course has no fee set.')
                .removeClass('text-info')
                .addClass('text-warning');
        }
    });
    
    // Gateway selection handling
    $('.gateway-card').on('click', function() {
        const gateway = $(this).data('gateway');
        selectGateway(gateway);
    });
    
    // Set default gateway
    selectGateway($('#selected_gateway').val() || 'manual');
    
    // If there's an old course selection, trigger amount fill
    const oldCourseId = "{{ old('course_id') }}";
    if (oldCourseId) {
        setTimeout(function() {
            $('#course_id').trigger('change');
        }, 100);
    }
    
    // Form validation before submit
    $('#paymentForm').on('submit', function(e) {
        const amount = $('#amount').val().trim();
        const amountFloat = parseFloat(amount);
        
        // Validate amount
        if (!amount || isNaN(amountFloat) || amountFloat <= 0) {
            e.preventDefault();
            alert('Please enter a valid amount greater than 0.');
            $('#amount').focus();
            return false;
        }
        
        // Validate gateway
        const gateway = $('#selected_gateway').val();
        if (!gateway) {
            e.preventDefault();
            alert('Please select a payment method.');
            return false;
        }
        
        // Show loading state
        $(this).find('button[type="submit"]')
            .html('<i class="fas fa-spinner fa-spin mr-2"></i>Creating Payment...')
            .prop('disabled', true);
        
        return true;
    });

    // Amount input formatting
    $('#amount').on('input', function() {
        let value = $(this).val();
        // Remove any non-numeric characters except decimal point
        value = value.replace(/[^0-9.]/g, '');
        // Ensure only one decimal point
        const parts = value.split('.');
        if (parts.length > 2) {
            value = parts[0] + '.' + parts.slice(1).join('');
        }
        // Limit to 2 decimal places
        if (parts[1] && parts[1].length > 2) {
            value = parts[0] + '.' + parts[1].substring(0, 2);
        }
        $(this).val(value);
    });
});

function selectGateway(gateway) {
    // Remove active class from all cards
    $('.gateway-card').removeClass('active');
    
    // Add active class to selected card
    $(`.gateway-card[data-gateway="${gateway}"]`).addClass('active');
    
    // Set hidden input value
    $('#selected_gateway').val(gateway);
    
    console.log('Gateway selected:', gateway);
}
</script>
@endsection
