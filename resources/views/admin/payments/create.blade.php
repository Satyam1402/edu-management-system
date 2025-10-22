{{-- resources/views/admin/payments/create.blade.php --}}
@extends('layouts.custom-admin')

@section('title', 'Create Payment')
@section('page-title', 'Create New Payment')

@section('css')
<style>
.create-payment-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    overflow: hidden;
    background: white;
    margin-bottom: 2rem;
}

.create-payment-header {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    color: white;
    padding: 2rem;
    position: relative;
    overflow: hidden;
}

.create-payment-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 100%;
    height: 200%;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    transform: rotate(-15deg);
}

.create-payment-header h3,
.create-payment-header p {
    position: relative;
    z-index: 2;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-section {
    border-left: 4px solid #17a2b8;
    padding: 0.75rem 0 0.75rem 1rem;
    margin: 2rem 0 1.5rem 0;
    background: linear-gradient(90deg, rgba(23, 162, 184, 0.05) 0%, transparent 100%);
    border-radius: 0 8px 8px 0;
}

.form-label-enhanced {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
    font-size: 14px;
    display: flex;
    align-items: center;
}

.required-marker::after {
    content: ' *';
    color: #dc3545;
    font-weight: bold;
}

.form-control-enhanced {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    padding-block: initial !important;
    font-size: 14px;
    transition: all 0.3s ease;
    background: white;
}

.form-control-enhanced:focus {
    border-color: #17a2b8;
    box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.15);
    background: white;
}

.input-group .input-group-text {
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-right: none;
    border-radius: 10px 0 0 10px !important;
    padding: 0.75rem;
    padding-block: initial !important;
    min-width: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.input-group .form-control {
    border: 2px solid #e9ecef;
    border-left: none;
    border-radius: 0 10px 10px 0 !important;
    padding: 0.75rem 1rem;
    padding-block: initial !important;
    font-size: 14px;
    transition: all 0.3s ease;
}

.input-group .form-control:focus {
    border-color: #17a2b8;
    box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.15);
}

.input-group .form-control:focus ~ .input-group-prepend .input-group-text {
    border-color: #17a2b8;
}

select.form-control {
    background-color: white;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3E%3C/svg%3E");
    background-position: right 0.75rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    padding-right: 2.5rem;
    padding-block: initial !important;
    color: #495057;
    font-size: 14px;
    font-weight: 400;
    cursor: pointer;
}

.action-footer {
    background: #f8f9fa;
    padding: 1.5rem 2rem;
    border-top: 1px solid #dee2e6;
    margin: 2rem -1.5rem -1.5rem -1.5rem;
}

.btn-enhanced {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 10px;
    transition: all 0.3s ease;
    border: none;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    text-decoration: none;
}

.btn-enhanced:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    text-decoration: none;
}

.btn-secondary.btn-enhanced {
    background: #6c757d;
    color: white;
}

.btn-info.btn-enhanced {
    background: linear-gradient(135deg, #17a2b8, #138496);
    color: white;
}

.amount-preview {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    padding: 1rem;
    border-radius: 10px;
    text-align: center;
    margin-top: 1rem;
}
</style>
@endsection

@section('content')
<div class="create-payment-card">
    <!-- Enhanced Header -->
    <div class="create-payment-header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h3 class="mb-2 font-weight-bold">
                    <i class="fas fa-plus-circle mr-3"></i>Create New Payment
                </h3>
                <p class="mb-0 h6" style="opacity: 0.9;">
                    Create a payment request for student course enrollment
                </p>
            </div>
            <div class="text-right d-none d-md-block">
                <div style="font-size: 4rem; opacity: 0.2;">
                    <i class="fas fa-credit-card"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body px-4">
        <!-- PAYMENT CREATION FORM -->
        <form action="{{ route('admin.payments.store') }}" method="POST" id="paymentForm">
            @csrf

            <div class="row">
                <!-- Student Selection -->
                <div class="col-md-6">
                    <div class="form-section">
                        <h6 class="mb-0 font-weight-bold text-dark">
                            <i class="fas fa-user mr-2 text-primary"></i>Student Information
                        </h6>
                    </div>

                    <div class="form-group">
                        <label for="student_id" class="form-label-enhanced required-marker">Select Student</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-user text-primary"></i>
                                </span>
                            </div>
                            <select class="form-control @error('student_id') is-invalid @enderror" 
                                    id="student_id" name="student_id" required>
                                <option value="" disabled selected style="color: #6c757d;">-- Select Student --</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" 
                                            data-course="{{ $student->course_id }}"
                                            {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->name }} ({{ $student->student_id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('student_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="course_id" class="form-label-enhanced required-marker">Select Course</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-book text-info"></i>
                                </span>
                            </div>
                            <select class="form-control @error('course_id') is-invalid @enderror" 
                                    id="course_id" name="course_id" required>
                                <option value="" disabled selected style="color: #6c757d;">-- Select Course --</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" 
                                            data-fee="{{ $course->fee ?? 0 }}"
                                            {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }} - ₹{{ number_format($course->fee ?? 0, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('course_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="col-md-6">
                    <div class="form-section">
                        <h6 class="mb-0 font-weight-bold text-dark">
                            <i class="fas fa-rupee-sign mr-2 text-success"></i>Payment Details
                        </h6>
                    </div>

                    <div class="form-group">
                        <label for="amount" class="form-label-enhanced required-marker">Amount</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-rupee-sign text-success"></i>
                                </span>
                            </div>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                   id="amount" name="amount" value="{{ old('amount') }}" 
                                   min="1" step="0.01" placeholder="Enter amount" required>
                        </div>
                        @error('amount')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Amount Preview -->
                    <div class="amount-preview" id="amountPreview" style="display: none;">
                        <h5 class="mb-1">Payment Amount</h5>
                        <h3 class="mb-0 font-weight-bold" id="previewAmount">₹0.00</h3>
                        <small>Including all applicable charges</small>
                    </div>

                    <!-- Student Details Preview -->
                    <div class="mt-3" id="studentPreview" style="display: none;">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-user mr-2"></i>Selected Student
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="studentDetails">
                                    <!-- Student details will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Action Footer -->
            <div class="action-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary btn-enhanced">
                            <i class="fas fa-arrow-left mr-2"></i>Back to List
                        </a>
                    </div>
                    <div class="d-flex">
                        <button type="button" class="btn btn-outline-warning btn-enhanced mr-3" onclick="resetForm()">
                            <i class="fas fa-redo mr-2"></i>Reset Form
                        </button>
                        <button type="submit" class="btn btn-info btn-enhanced">
                            <i class="fas fa-credit-card mr-2"></i>Create Payment
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Handle student selection change
    $('#student_id').on('change', function() {
        const studentId = $(this).val();
        const studentText = $(this).find('option:selected').text();
        const courseId = $(this).find('option:selected').data('course');
        
        if (studentId) {
            // Auto-select course if student has one
            if (courseId) {
                $('#course_id').val(courseId).trigger('change');
            }
            
            // Show student preview
            $('#studentDetails').html(`
                <div class="font-weight-bold text-primary">${studentText}</div>
                <small class="text-muted">Selected for payment</small>
            `);
            $('#studentPreview').show();
        } else {
            $('#studentPreview').hide();
            $('#course_id').val('');
        }
    });

    // Handle course selection change
    $('#course_id').on('change', function() {
        const courseFee = $(this).find('option:selected').data('fee');
        
        if (courseFee) {
            $('#amount').val(courseFee);
            updateAmountPreview(courseFee);
        }
    });

    // Handle amount input change
    $('#amount').on('input', function() {
        const amount = parseFloat($(this).val()) || 0;
        updateAmountPreview(amount);
    });

    // Form validation before submission
    $('#paymentForm').on('submit', function(e) {
        let isValid = true;
        
        // Check required fields
        const requiredFields = ['student_id', 'course_id', 'amount'];
        
        requiredFields.forEach(function(fieldName) {
            const field = $(`#${fieldName}`);
            if (!field.val()) {
                field.addClass('is-invalid');
                isValid = false;
            } else {
                field.removeClass('is-invalid').addClass('is-valid');
            }
        });

        // Validate amount
        const amount = parseFloat($('#amount').val());
        if (amount <= 0) {
            $('#amount').addClass('is-invalid');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            showAlert('error', 'Please fill in all required fields correctly.');
        }
    });
});

// Update amount preview
function updateAmountPreview(amount) {
    if (amount > 0) {
        $('#previewAmount').text('₹' + parseFloat(amount).toLocaleString('en-IN', {minimumFractionDigits: 2}));
        $('#amountPreview').show();
    } else {
        $('#amountPreview').hide();
    }
}

// Global reset function
function resetForm() {
    if (confirm('Are you sure you want to reset all form data?')) {
        $('#paymentForm')[0].reset();
        $('.form-control').removeClass('is-valid is-invalid');
        $('#studentPreview, #amountPreview').hide();
    }
}

// Show alert function
function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show mt-3" role="alert">
            <i class="${icon} mr-2"></i>${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `;
    
    $('body').prepend(alertHtml);
    
    setTimeout(() => {
        $('.alert').alert('close');
    }, 4000);
}
</script>
@endsection
