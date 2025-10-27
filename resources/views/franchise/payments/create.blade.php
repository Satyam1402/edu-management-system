@extends('layouts.custom-admin')

@section('page-title', 'New Payment')

@section('css')
<style>
    .card {
        border-radius: 15px;
        box-shadow: 0 4px 14px #667eea16;
        border: none;
        transition: all 0.3s ease;
    }
    .payment-method-card {
        cursor: pointer;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
        height: 200px;
        display: flex;
        align-items: center;
    }
    .payment-method-card:hover {
        border-color: #667eea;
        transform: translateY(-3px);
    }
    .payment-method-card.active {
        border-color: #667eea;
        background: linear-gradient(145deg, #f8f9ff 0%, #ffffff 100%);
        transform: scale(1.02);
        box-shadow: 0 8px 20px #667eea20;
    }
    .method-icon {
        font-size: 3rem;
        margin-bottom: 15px;
    }
    .qr-generator-container {
        display: none;
        background: linear-gradient(145deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 12px;
        padding: 25px;
        margin: 20px 0;
        border: 2px solid #e9ecef;
    }
    .qr-generator-container.active {
        display: block;
        border-color: #28a745;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
    }

    .qr-code-display {
        text-align: center;
        padding: 20px;
        background: white;
        border-radius: 12px;
        border: 3px solid #28a745;
        margin: 20px 0;
    }

    .qr-code-display svg {
        max-width: 250px;
        height: auto;
        border: 5px solid white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .form-container {
        background: linear-gradient(145deg, #f8f9fa 0%, #ffffff 100%);
        padding: 25px;
        border-radius: 12px;
        margin-top: 20px;
        border: 2px solid #e9ecef;
    }
    .form-container.active {
        border-color: #667eea;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
    }
    .btn-custom {
        border-radius: 25px;
        padding: 12px 30px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .btn-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .payment-info {
        background: #e8f5e8;
        padding: 15px;
        border-radius: 8px;
        margin: 15px 0;
        border-left: 4px solid #28a745;
    }
    .step-indicator {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }
    .step {
        display: flex;
        align-items: center;
        margin: 0 10px;
        padding: 8px 15px;
        background: #f8f9fa;
        border-radius: 20px;
        font-size: 14px;
    }
    .step.active {
        background: #28a745;
        color: white;
    }
    .step i {
        margin-right: 8px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            {{-- SUCCESS/ERROR MESSAGES --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <div class="card">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card"></i> New Payment - Choose Method
                    </h5>
                </div>
                <div class="card-body">

                    {{-- PAYMENT METHOD SELECTION --}}
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="mb-3 text-center">
                                <i class="fas fa-hand-point-right text-primary"></i> Select Your Payment Method
                            </h6>
                        </div>

                        {{-- MANUAL PAYMENT METHOD --}}
                        <div class="col-md-6">
                            <div class="card payment-method-card" id="manual-method" onclick="selectPaymentMethod('manual')">
                                <div class="card-body text-center">
                                    <div class="method-icon">
                                        <i class="fas fa-edit text-primary"></i>
                                    </div>
                                    <h5 class="mb-2">Manual Payment</h5>
                                    <p class="text-muted mb-1">Direct payment recording</p>
                                    <span class="badge badge-primary">Traditional</span>
                                </div>
                            </div>
                        </div>

                        {{-- QR CODE PAYMENT METHOD --}}
                        <div class="col-md-6">
                            <div class="card payment-method-card" id="qr-method" onclick="selectPaymentMethod('qr')">
                                <div class="card-body text-center">
                                    <div class="method-icon">
                                        <i class="fas fa-qrcode text-success"></i>
                                    </div>
                                    <h5 class="mb-2">QR Code Payment</h5>
                                    <p class="text-muted mb-1">Generate QR for student to pay</p>
                                    <span class="badge badge-success">Modern & Easy</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- QR CODE GENERATOR SECTION --}}
                    <div class="qr-generator-container" id="qr-generator-section">
                        <div class="text-center mb-3">
                            <h5>
                                <i class="fas fa-qrcode text-success"></i> QR Code Payment Generator
                            </h5>
                            <p class="text-muted">Fill payment details to generate QR code for student</p>
                        </div>

                        {{-- STEP INDICATOR --}}
                        <div class="step-indicator">
                            <div class="step active" id="step1">
                                <i class="fas fa-edit"></i> Fill Details
                            </div>
                            <div class="step" id="step2">
                                <i class="fas fa-qrcode"></i> Generate QR
                            </div>
                            <div class="step" id="step3">
                                <i class="fas fa-mobile-alt"></i> Student Pays
                            </div>
                            <div class="step" id="step4">
                                <i class="fas fa-check"></i> Complete
                            </div>
                        </div>

                        {{-- QR CODE FORM --}}
                        <div class="row">
                            <div class="col-md-6">
                                <form id="qr-form">
                                    <div class="form-group">
                                        <label for="qr_student_id">
                                            <i class="fas fa-user"></i> Select Student <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control" id="qr_student_id" required>
                                            <option value="">-- Choose Student --</option>
                                            @foreach($students as $student)
                                                <option value="{{ $student->id }}" data-name="{{ $student->name }}" data-email="{{ $student->email }}">
                                                    {{ $student->name }} ({{ $student->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="qr_course_id">
                                            <i class="fas fa-book"></i> Course (Optional)
                                        </label>
                                        <select class="form-control" id="qr_course_id">
                                            <option value="">-- Select Course --</option>
                                            @foreach($courses as $course)
                                                <option value="{{ $course->id }}" data-name="{{ $course->name }}">
                                                    {{ $course->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="qr_amount">
                                            <i class="fas fa-rupee-sign"></i> Payment Amount <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">₹</span>
                                            </div>
                                            <input type="number" class="form-control" id="qr_amount" min="1" max="99999" step="0.01" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="upi_id">
                                            <i class="fas fa-mobile-alt"></i> Your UPI ID <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="upi_id" placeholder="yourname@paytm / yourname@phonepe" required>
                                        <small class="text-muted">Enter your UPI ID where students will send payment</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="payee_name">
                                            <i class="fas fa-user-tie"></i> Payee Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="payee_name" placeholder="Franchise Name" required>
                                        <small class="text-muted">Name that will appear in payment</small>
                                    </div>

                                    <div class="text-center">
                                        <button type="button" class="btn btn-success btn-custom btn-lg" onclick="generateQRCode()">
                                            <i class="fas fa-qrcode"></i> Generate QR Code
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="col-md-6">
                                {{-- QR CODE DISPLAY --}}
                                <div id="qr-display" style="display: none;">
                                    <div class="qr-code-display">
                                        <h6 class="text-success mb-3">
                                            <i class="fas fa-mobile-alt"></i> Student Scan & Pay
                                        </h6>
                                        <div id="qr-code-container"></div>

                                        <div class="payment-info mt-3">
                                            <strong>Payment Details:</strong><br>
                                            <span id="payment-details"></span>
                                        </div>

                                        <div class="mt-3">
                                            <button class="btn btn-primary btn-custom" onclick="confirmPayment()">
                                                <i class="fas fa-check"></i> Confirm Payment Received
                                            </button>
                                            <button class="btn btn-secondary btn-custom ml-2" onclick="resetQR()">
                                                <i class="fas fa-redo"></i> Generate New QR
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- INSTRUCTIONS --}}
                                <div id="qr-instructions">
                                    <div class="alert alert-info">
                                        <h6><i class="fas fa-lightbulb"></i> How QR Payment Works:</h6>
                                        <ol class="mb-0 small">
                                            <li>Fill student and payment details</li>
                                            <li>Enter your UPI ID for receiving payment</li>
                                            <li>Generate QR code</li>
                                            <li>Student scans QR with any UPI app</li>
                                            <li>Student completes payment on their phone</li>
                                            <li>Confirm payment received</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- MANUAL PAYMENT FORM --}}
                    <div class="form-container" id="payment-form-section" style="display: none;">
                        <form action="{{ route('franchise.payments.store') }}" method="POST" id="payment-form">
                            @csrf
                            <input type="hidden" name="payment_method" id="selected-method" value="">
                            <input type="hidden" name="qr_data" id="qr-data" value="">

                            <div class="form-group">
                                <label for="student_id">
                                    <i class="fas fa-user"></i> Select Student <span class="text-danger">*</span>
                                </label>
                                <select class="form-control @error('student_id') is-invalid @enderror"
                                        name="student_id" id="student_id" required>
                                    <option value="">-- Choose a student --</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->name }} ({{ $student->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="course_id">
                                    <i class="fas fa-book"></i> Course (Optional)
                                </label>
                                <select class="form-control @error('course_id') is-invalid @enderror"
                                        name="course_id" id="course_id">
                                    <option value="">-- Select Course (Optional) --</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="amount">
                                    <i class="fas fa-rupee-sign"></i> Amount <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">₹</span>
                                    </div>
                                    <input type="number"
                                           class="form-control @error('amount') is-invalid @enderror"
                                           name="amount"
                                           id="amount"
                                           value="{{ old('amount') }}"
                                           min="1"
                                           max="99999"
                                           step="0.01"
                                           required>
                                </div>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary btn-custom btn-lg" id="submit-btn">
                                    <i class="fas fa-credit-card"></i> Record Manual Payment
                                </button>
                                <a href="{{ route('franchise.certificate-requests.create') }}" class="btn btn-secondary btn-custom btn-lg ml-2">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
let selectedPaymentMethod = null;
let generatedQRData = null;

$(document).ready(function() {
    setTimeout(function() {
        $('.alert-dismissible').fadeOut('slow');
    }, 5000);
});

function selectPaymentMethod(method) {
    selectedPaymentMethod = method;

    $('.payment-method-card').removeClass('active');
    $(`#${method}-method`).addClass('active');

    $('#qr-generator-section').removeClass('active').hide();
    $('#payment-form-section').hide().removeClass('active');

    resetSteps();

    $('#selected-method').val(method);

    if (method === 'qr') {
        $('#qr-generator-section').addClass('active').show();
        $('#step1').addClass('active');
    } else {
        $('#payment-form-section').show().addClass('active');
    }
}

function resetSteps() {
    $('.step').removeClass('active');
    $('#qr-display').hide();
    $('#qr-instructions').show();
    $('#qr-form')[0].reset();
    generatedQRData = null;
}

function generateQRCode() {
    const studentId = $('#qr_student_id').val();
    const courseId = $('#qr_course_id').val();
    const amount = $('#qr_amount').val();
    const upiId = $('#upi_id').val();
    const payeeName = $('#payee_name').val();

    if (!studentId || !amount || !upiId || !payeeName) {
        alert('Please fill all required fields!');
        return;
    }

    const studentName = $('#qr_student_id option:selected').data('name');
    const courseName = $('#qr_course_id option:selected').data('name') || 'General Payment';

    // Create UPI payment string
    const transactionNote = `Payment for ${courseName} - ${studentName}`;
    const upiString = `upi://pay?pa=${upiId}&pn=${encodeURIComponent(payeeName)}&am=${amount}&cu=INR&tn=${encodeURIComponent(transactionNote)}`;

    // Store QR data
    generatedQRData = {
        student_id: studentId,
        course_id: courseId,
        amount: amount,
        upi_string: upiString,
        payee_name: payeeName,
        upi_id: upiId
    };

    // Generate QR Code using Laravel QR package via AJAX
    $.ajax({
        url: '{{ route("franchise.payments.generate-qr") }}',
        method: 'POST',
        data: {
            upi_string: upiString,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            // Update steps
            $('.step').removeClass('active');
            $('#step2, #step3').addClass('active');

            // Show QR code
            $('#qr-code-container').html(response.qr_code);
            $('#payment-details').html(`
                <strong>Student:</strong> ${studentName}<br>
                <strong>Course:</strong> ${courseName}<br>
                <strong>Amount:</strong> ₹${amount}<br>
                <strong>Pay To:</strong> ${payeeName} (${upiId})
            `);

            $('#qr-display').show();
            $('#qr-instructions').hide();
        },
        error: function() {
            alert('Error generating QR code. Please try again.');
        }
    });
}

function confirmPayment() {
    if (!generatedQRData) {
        alert('No QR data found!');
        return;
    }

    // Submit payment confirmation
    $.ajax({
        url: '{{ route("franchise.payments.store") }}',
        method: 'POST',
        data: {
            payment_method: 'qr',
            student_id: generatedQRData.student_id,
            course_id: generatedQRData.course_id,
            amount: generatedQRData.amount,
            qr_data: JSON.stringify(generatedQRData),
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            $('.step').removeClass('active');
            $('#step4').addClass('active');

            alert('✅ Payment confirmed successfully!');
            window.location.href = '{{ route("franchise.certificate-requests.create") }}';
        },
        error: function() {
            alert('❌ Error confirming payment. Please try again.');
        }
    });
}

function resetQR() {
    resetSteps();
    $('#step1').addClass('active');
}

$('#payment-form').submit(function(e) {
    $('#submit-btn').prop('disabled', true)
        .html('<i class="fas fa-spinner fa-spin"></i> Recording Payment...');
});
</script>
@endsection
