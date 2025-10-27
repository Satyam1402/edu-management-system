@extends('layouts.custom-admin')

@section('page-title', 'New Certificate Request')

@section('css')
<style>
    .card {
        border-radius: 15px;
        box-shadow: 0 4px 14px #667eea16;
        border: none;
    }
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .form-group label {
        font-weight: 600;
        color: #2d3748;
    }
    .alert {
        border-radius: 10px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-certificate"></i> New Certificate Request
                    </h5>
                </div>
                <div class="card-body">

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

                    <form action="{{ route('franchise.certificate-requests.store') }}" method="POST">
                        @csrf

                        {{-- STUDENT SELECTION --}}
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
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> {{ count($students) }} students found
                            </small>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- CERTIFICATE TYPE --}}
                        <div class="form-group">
                            <label for="certificate_type">
                                <i class="fas fa-award"></i> Certificate Type
                            </label>
                            <select class="form-control @error('certificate_type') is-invalid @enderror"
                                    name="certificate_type" id="certificate_type">
                                <option value="General Certificate" {{ old('certificate_type') == 'General Certificate' ? 'selected' : '' }}>
                                    General Certificate
                                </option>
                                <option value="Course Certificate" {{ old('certificate_type') == 'Course Certificate' ? 'selected' : '' }}>
                                    Course Certificate
                                </option>
                                <option value="Achievement Certificate" {{ old('certificate_type') == 'Achievement Certificate' ? 'selected' : '' }}>
                                    Achievement Certificate
                                </option>
                            </select>
                            @error('certificate_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- PAYMENT SELECTION --}}
                        <div class="form-group">
                            <label for="payment_id">
                                <i class="fas fa-credit-card"></i> Select Payment <span class="text-danger">*</span>
                            </label>
                            <select class="form-control @error('payment_id') is-invalid @enderror"
                                    name="payment_id" id="payment_id" required>
                                <option value="">-- Choose completed payment --</option>
                                @foreach($payments as $payment)
                                    <option value="{{ $payment->id }}" {{ old('payment_id') == $payment->id ? 'selected' : '' }}>
                                        {{ $payment->student->name }} -
                                        ₹{{ number_format($payment->amount, 2) }}
                                        ({{ $payment->course ? $payment->course->name : 'General Fee' }})
                                        - {{ $payment->created_at->format('M d, Y') }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> {{ count($payments) }} completed payments available
                            </small>
                            @error('payment_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- SPECIAL NOTE --}}
                        <div class="form-group">
                            <label for="special_note">
                                <i class="fas fa-sticky-note"></i> Special Note
                            </label>
                            <textarea class="form-control @error('special_note') is-invalid @enderror"
                                      name="special_note"
                                      id="special_note"
                                      rows="3"
                                      placeholder="Any special requirements or instructions for the certificate...">{{ old('special_note') }}</textarea>
                            <small class="text-muted">Optional: Add any special instructions for certificate generation</small>
                            @error('special_note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- FORM BUTTONS --}}
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane"></i> Submit Certificate Request
                            </button>
                            <a href="{{ route('franchise.certificate-requests.index') }}" class="btn btn-secondary btn-lg ml-2">
                                <i class="fas fa-arrow-left"></i> Back to Requests
                            </a>
                        </div>

                        {{-- INFO BOX --}}
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Information:</h6>
                            <ul class="mb-0">
                                <li>Certificate requests are processed within 1-2 business days</li>
                                <li>You will be notified via email when your certificate is ready</li>
                                <li>Only completed payments can be used for certificate requests</li>
                            </ul>
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
$(document).ready(function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert-dismissible').fadeOut('slow');
    }, 5000);

    // Student selection change handler
    $('#student_id').change(function() {
        const studentId = $(this).val();
        if (studentId) {
            // Filter payments by selected student
            $('#payment_id option').each(function() {
                const option = $(this);
                if (option.val() === '') return; // Skip the default option

                const studentName = $('#student_id option:selected').text().split(' (')[0];
                const paymentText = option.text();

                if (paymentText.includes(studentName)) {
                    option.show();
                } else {
                    option.hide();
                }
            });
            $('#payment_id').val(''); // Reset payment selection
        } else {
            // Show all payment options
            $('#payment_id option').show();
            $('#payment_id').val('');
        }
    });
});
</script>
@endsection
