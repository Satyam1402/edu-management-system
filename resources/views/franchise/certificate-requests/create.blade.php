@extends('layouts.custom-admin')

@section('page-title', 'Request Certificates')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .card {
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        border: none;
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.12);
    }

    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px 15px 0 0!important;
        padding: 1.5rem;
    }

    .wallet-card {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        border-radius: 12px;
        margin-bottom: 2rem;
    }

    .wallet-balance {
        font-size: 2rem;
        font-weight: 700;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .form-group label {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }

    .form-control {
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .student-checkbox {
        transform: scale(1.2);
        margin-right: 0.5rem;
    }

    .student-card {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .student-card:hover, .student-card.selected {
        border-color: #667eea;
        background-color: #f7faff;
    }

    .cost-calculator {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin: 1.5rem 0;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 8px;
        padding: 12px 30px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }

    .alert {
        border-radius: 10px;
        border: none;
    }

    .select2-container .select2-selection--single {
        height: 45px;
        border-radius: 8px;
        border: 2px solid #e2e8f0;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 41px;
        padding-left: 15px;
    }

    .insufficient-balance {
        background: linear-gradient(135deg, #ff4757 0%, #ff3742 100%);
        color: white;
    }

    .loading-spinner {
        display: none;
        text-align: center;
        padding: 2rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    {{-- WALLET BALANCE CARD --}}
    <div class="row">
        <div class="col-12">
            <div class="card wallet-card">
                <div class="card-body text-center">
                    <h5 class="mb-2"><i class="fas fa-wallet"></i> Current Wallet Balance</h5>
                    <div class="wallet-balance" id="wallet-balance">₹{{ number_format($walletBalance, 2) }}</div>
                    <small><i class="fas fa-info-circle"></i> Sufficient balance required to submit requests</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-certificate"></i> Request New Certificates
                    </h4>
                    <small class="opacity-75">Select students and course to generate certificates</small>
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

                    <form action="{{ route('franchise.certificate-requests.store') }}" method="POST" id="certificate-form">
                        @csrf

                        {{-- COURSE SELECTION --}}
                        <div class="form-group">
                            <label for="course_id">
                                <i class="fas fa-book"></i> Select Course <span class="text-danger">*</span>
                            </label>
                            <select class="form-control @error('course_id') is-invalid @enderror"
                                    name="course_id" id="course_id" required>
                                <option value="">-- Choose a course --</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}"
                                            data-fee="{{ $course->certificate_fee ?? 500 }}"
                                            {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }} - ₹{{ number_format($course->certificate_fee ?? 500, 2) }} per certificate
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Fee will be deducted from your wallet balance
                            </small>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- STUDENT SELECTION --}}
                        <div class="form-group">
                            <label>
                                <i class="fas fa-users"></i> Select Students <span class="text-danger">*</span>
                            </label>
                            <div class="mb-3">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="select-all-students">
                                    <i class="fas fa-check-square"></i> Select All
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm ml-2" id="deselect-all-students">
                                    <i class="fas fa-square"></i> Deselect All
                                </button>
                                <span class="ml-3 text-muted">
                                    <i class="fas fa-user-friends"></i> <span id="selected-count">0</span> students selected
                                </span>
                            </div>

                            @if(count($students) > 0)
                                <div class="row" id="students-container">
                                    @foreach($students as $student)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="student-card" data-student-id="{{ $student->id }}">
                                                <div class="form-check">
                                                    <input class="form-check-input student-checkbox"
                                                           type="checkbox"
                                                           name="student_ids[]"
                                                           value="{{ $student->id }}"
                                                           id="student_{{ $student->id }}"
                                                           {{ is_array(old('student_ids')) && in_array($student->id, old('student_ids')) ? 'checked' : '' }}>
                                                    <label class="form-check-label w-100" for="student_{{ $student->id }}">
                                                        <div class="font-weight-bold">{{ $student->name }}</div>
                                                        <small class="text-muted">
                                                            <i class="fas fa-envelope"></i> {{ $student->email }}
                                                            @if($student->phone)
                                                                <br><i class="fas fa-phone"></i> {{ $student->phone }}
                                                            @endif
                                                        </small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-user-slash"></i> No active students found for your franchise.
                                    <a href="{{ route('franchise.students.create') }}" class="alert-link">Add students first</a>.
                                </div>
                            @endif

                            @error('student_ids')
                                <div class="text-danger mt-2">
                                    <small><i class="fas fa-exclamation-circle"></i> {{ $message }}</small>
                                </div>
                            @enderror
                        </div>

                        {{-- COST CALCULATOR --}}
                        <div class="cost-calculator" id="cost-calculator" style="display: none;">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="mb-1"><i class="fas fa-calculator"></i> Cost Calculation</h6>
                                    <div id="cost-breakdown">
                                        <span id="selected-students-text">0 students</span> ×
                                        <span id="fee-per-certificate">₹0</span> =
                                        <strong id="total-cost">₹0</strong>
                                    </div>
                                </div>
                                <div class="col-md-4 text-right">
                                    <div class="balance-check">
                                        <small>Wallet Balance: <span id="current-balance">₹{{ number_format($walletBalance, 2) }}</span></small>
                                        <div id="balance-status" class="font-weight-bold"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- CERTIFICATE TYPE --}}
                        <div class="form-group">
                            <label for="certificate_type">
                                <i class="fas fa-award"></i> Certificate Type
                            </label>
                            <select class="form-control @error('certificate_type') is-invalid @enderror"
                                    name="certificate_type" id="certificate_type">
                                <option value="Standard Certificate" {{ old('certificate_type') == 'Standard Certificate' ? 'selected' : '' }}>
                                    Standard Certificate
                                </option>
                                <option value="Course Completion Certificate" {{ old('certificate_type') == 'Course Completion Certificate' ? 'selected' : '' }}>
                                    Course Completion Certificate
                                </option>
                                <option value="Achievement Certificate" {{ old('certificate_type') == 'Achievement Certificate' ? 'selected' : '' }}>
                                    Achievement Certificate
                                </option>
                                <option value="Merit Certificate" {{ old('certificate_type') == 'Merit Certificate' ? 'selected' : '' }}>
                                    Merit Certificate
                                </option>
                            </select>
                            @error('certificate_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- NOTES --}}
                        <div class="form-group">
                            <label for="notes">
                                <i class="fas fa-sticky-note"></i> Additional Notes
                            </label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      name="notes" id="notes" rows="3"
                                      placeholder="Any special requirements or instructions for the certificates...">{{ old('notes') }}</textarea>
                            <small class="form-text text-muted">Optional: Add any special instructions for certificate generation</small>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- LOADING SPINNER --}}
                        <div class="loading-spinner" id="loading-spinner">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                            <p class="mt-2">Processing your certificate requests...</p>
                        </div>

                        {{-- FORM BUTTONS --}}
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary btn-lg" id="submit-btn" disabled>
                                <i class="fas fa-paper-plane"></i> Submit Certificate Requests
                            </button>
                            <a href="{{ route('franchise.certificate-requests.index') }}" class="btn btn-secondary btn-lg ml-2">
                                <i class="fas fa-arrow-left"></i> Back to Requests
                            </a>
                        </div>

                        {{-- INFO BOX --}}
                        <div class="alert alert-info mt-4">
                            <h6><i class="fas fa-info-circle"></i> Important Information:</h6>
                            <ul class="mb-0">
                                <li><strong>Processing Time:</strong> Certificate requests are processed within 1-2 business days</li>
                                <li><strong>Notifications:</strong> You will be notified via email when certificates are ready</li>
                                <li><strong>Payment:</strong> Fees will be deducted from your wallet balance immediately</li>
                                <li><strong>Bulk Requests:</strong> You can select multiple students for the same course</li>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    let walletBalance = {{ $walletBalance }};
    let selectedStudents = [];
    let currentFee = 0;

    // Initialize Select2
    $('#course_id').select2({
        placeholder: "Choose a course",
        allowClear: true
    });

    // Auto-hide alerts
    setTimeout(() => $('.alert-dismissible').fadeOut('slow'), 6000);

    // Course selection handler
    $('#course_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        currentFee = parseFloat(selectedOption.data('fee')) || 0;

        $('#fee-per-certificate').text('₹' + currentFee.toLocaleString());
        updateCostCalculation();
        validateForm();
    });

    // Student selection handlers
    $('.student-checkbox').on('change', function() {
        const studentId = $(this).val();
        const isChecked = $(this).is(':checked');
        const studentCard = $(this).closest('.student-card');

        if (isChecked) {
            selectedStudents.push(studentId);
            studentCard.addClass('selected');
        } else {
            selectedStudents = selectedStudents.filter(id => id !== studentId);
            studentCard.removeClass('selected');
        }

        updateSelectedCount();
        updateCostCalculation();
        validateForm();
    });

    // Student card click handler
    $('.student-card').on('click', function(e) {
        if (e.target.type !== 'checkbox') {
            const checkbox = $(this).find('.student-checkbox');
            checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
        }
    });

    // Select/Deselect all buttons
    $('#select-all-students').on('click', function() {
        $('.student-checkbox').prop('checked', true).trigger('change');
    });

    $('#deselect-all-students').on('click', function() {
        $('.student-checkbox').prop('checked', false).trigger('change');
    });

    // Form submission handler
    $('#certificate-form').on('submit', function(e) {
        e.preventDefault();

        if (!validateForm()) {
            return false;
        }

        // Show loading spinner
        $('#loading-spinner').show();
        $('#submit-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

        // Submit the form
        this.submit();
    });

    function updateSelectedCount() {
        const count = selectedStudents.length;
        $('#selected-count').text(count);
        $('#selected-students-text').text(count + ' student' + (count !== 1 ? 's' : ''));
    }

    function updateCostCalculation() {
        const studentCount = selectedStudents.length;
        const totalCost = studentCount * currentFee;

        $('#total-cost').text('₹' + totalCost.toLocaleString());

        if (studentCount > 0 && currentFee > 0) {
            $('#cost-calculator').slideDown();

            // Check wallet balance
            const balanceStatus = $('#balance-status');
            if (totalCost > walletBalance) {
                balanceStatus.html('<i class="fas fa-exclamation-triangle"></i> Insufficient Balance')
                           .removeClass('text-success').addClass('text-warning');
                $('#cost-calculator').removeClass('cost-calculator').addClass('insufficient-balance');
            } else {
                balanceStatus.html('<i class="fas fa-check-circle"></i> Sufficient Balance')
                           .removeClass('text-warning').addClass('text-success');
                $('#cost-calculator').removeClass('insufficient-balance').addClass('cost-calculator');
            }
        } else {
            $('#cost-calculator').slideUp();
        }
    }

    function validateForm() {
        const courseSelected = $('#course_id').val() !== '';
        const studentsSelected = selectedStudents.length > 0;
        const totalCost = selectedStudents.length * currentFee;
        const sufficientBalance = totalCost <= walletBalance;

        const isValid = courseSelected && studentsSelected && sufficientBalance;
        $('#submit-btn').prop('disabled', !isValid);

        if (!sufficientBalance && studentsSelected && courseSelected) {
            showInsufficientBalanceError();
        }

        return isValid;
    }

    function showInsufficientBalanceError() {
        const totalCost = selectedStudents.length * currentFee;
        const shortfall = totalCost - walletBalance;

        if ($('#balance-error').length === 0) {
            const errorHtml = `
                <div class="alert alert-danger" id="balance-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Insufficient Wallet Balance!</strong><br>
                    Required: ₹${totalCost.toLocaleString()}, Available: ₹${walletBalance.toLocaleString()}<br>
                    Shortfall: ₹${shortfall.toLocaleString()}<br>
                    <a href="{{ route('franchise.wallet.create') }}" class="alert-link">
                        <i class="fas fa-plus-circle"></i> Add funds to wallet
                    </a>
                </div>
            `;
            $('#cost-calculator').after(errorHtml);
        }
    }

    // Remove balance error when conditions change
    $('#course_id, .student-checkbox').on('change', function() {
        $('#balance-error').remove();
    });
});
</script>
@endsection
