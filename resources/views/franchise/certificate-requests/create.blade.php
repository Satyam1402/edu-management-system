@extends('layouts.custom-admin')

@section('title', 'Create Certificate Request')
@section('page-title', 'Certificate Requests')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .eligible-student-card {
        border: 2px solid #28a745;
        background: #f8fff9;
    }
    .student-info-box {
        background: #e7f3ff;
        border-left: 4px solid #007bff;
        padding: 15px;
        margin: 15px 0;
    }
    .fee-display {
        font-size: 2rem;
        font-weight: bold;
        color: #28a745;
    }
    .select2-container .select2-selection--single {
        height: 45px !important;
        padding: 8px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Create Certificate Request</h2>
            <p class="text-muted mb-0">Select an eligible student to request a certificate</p>
        </div>
        <a href="{{ route('franchise.certificate-requests.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    <!-- Wallet Balance Card -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Your Wallet Balance
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                ₹{{ number_format($walletBalance, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('franchise.wallet.index') }}" class="btn btn-sm btn-success">
                            <i class="fas fa-plus mr-1"></i>Add Funds
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Eligible Students
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                {{ $eligibleStudents->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="alert alert-info mb-0 h-100 d-flex align-items-center">
                <i class="fas fa-info-circle fa-2x mr-3"></i>
                <div>
                    <strong>Note:</strong> Payment will be required AFTER admin approval.
                    No money will be deducted now.
                </div>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-file-certificate mr-2"></i>Certificate Request Form</h5>
                </div>
                <div class="card-body">
                    @if($eligibleStudents->isEmpty())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>No Eligible Students</strong>
                            <p class="mb-0">There are no students currently eligible for certificates.
                            Students must complete all course exams to be eligible.</p>
                        </div>
                        <a href="{{ route('franchise.students.index') }}" class="btn btn-primary">
                            <i class="fas fa-users mr-2"></i>View All Students
                        </a>
                    @else
                        <form action="{{ route('franchise.certificate-requests.store') }}" method="POST" id="certificateRequestForm">
                            @csrf

                            <!-- Student Selection -->
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    Select Eligible Student <span class="text-danger">*</span>
                                </label>
                                <select class="form-control select2" name="student_id" id="studentSelect" required>
                                    <option value="">-- Choose a student --</option>
                                    @foreach($eligibleStudents as $student)
                                        <option value="{{ $student['id'] }}"
                                                data-course="{{ $student['course_name'] }}"
                                                data-fee="{{ $student['certificate_fee'] }}"
                                                data-email="{{ $student['email'] }}">
                                            {{ $student['name'] }} - {{ $student['course_name'] }} (₹{{ number_format($student['certificate_fee'], 2) }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">
                                    <i class="fas fa-check-circle text-success"></i>
                                    Only students who have completed all course requirements are shown
                                </small>
                            </div>

                            <!-- Student Info Display (Hidden initially) -->
                            <div id="studentInfoBox" class="student-info-box" style="display: none;">
                                <h6 class="font-weight-bold mb-3">
                                    <i class="fas fa-user-check text-success mr-2"></i>Selected Student Information
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>Student:</strong> <span id="displayName"></span></p>
                                        <p class="mb-2"><strong>Email:</strong> <span id="displayEmail"></span></p>
                                        <p class="mb-2"><strong>Course:</strong> <span id="displayCourse"></span></p>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <p class="mb-2"><strong>Certificate Fee:</strong></p>
                                        <p class="fee-display mb-0">₹<span id="displayFee">0</span></p>
                                    </div>
                                </div>
                                <div class="alert alert-success mt-3 mb-0">
                                    <i class="fas fa-shield-alt mr-2"></i>
                                    <strong>Payment Protected:</strong> Amount will be deducted only after admin approval and your confirmation.
                                </div>
                            </div>

                            <!-- Certificate Type -->
                            <div class="form-group">
                                <label class="font-weight-bold">Certificate Type (Optional)</label>
                                <select class="form-control" name="certificate_type">
                                    <option value="Course Completion Certificate">Course Completion Certificate</option>
                                    <option value="Merit Certificate">Merit Certificate</option>
                                    <option value="Participation Certificate">Participation Certificate</option>
                                </select>
                            </div>

                            <!-- Notes -->
                            <div class="form-group">
                                <label class="font-weight-bold">Additional Notes (Optional)</label>
                                <textarea class="form-control" name="notes" rows="3"
                                          placeholder="Any special instructions or notes..."></textarea>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary btn-lg btn-block">
                                    <i class="fas fa-paper-plane mr-2"></i>Submit Request for Approval
                                </button>
                                <small class="text-muted d-block mt-2 text-center">
                                    <i class="fas fa-info-circle"></i>
                                    Your request will be reviewed by the admin
                                </small>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Information Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-question-circle mr-2"></i>How It Works</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item mb-3">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">1. Select Student</h6>
                                <p class="text-muted small mb-0">Choose an eligible student from the list</p>
                            </div>
                        </div>
                        <div class="timeline-item mb-3">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">2. Submit Request</h6>
                                <p class="text-muted small mb-0">Request sent for admin review (No payment yet)</p>
                            </div>
                        </div>
                        <div class="timeline-item mb-3">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">3. Admin Approval</h6>
                                <p class="text-muted small mb-0">Admin verifies and approves your request</p>
                            </div>
                        </div>
                        <div class="timeline-item mb-3">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">4. Payment</h6>
                                <p class="text-muted small mb-0">You confirm and pay from your wallet</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">5. Certificate Ready</h6>
                                <p class="text-muted small mb-0">Download your certificate</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Eligibility Criteria -->
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-check-circle mr-2"></i>Eligibility Criteria</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success mr-2"></i>
                            Student must be actively enrolled
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success mr-2"></i>
                            All course exams must be completed
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success mr-2"></i>
                            All exams must be passed
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success mr-2"></i>
                            No existing certificate request
                        </li>
                    </ul>
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
    // Initialize Select2
    $('.select2').select2({
        placeholder: 'Search and select a student...',
        allowClear: true,
        width: '100%'
    });

    // Show student info when selected
    $('#studentSelect').on('change', function() {
        const selectedOption = $(this).find('option:selected');

        if (selectedOption.val()) {
            const studentName = selectedOption.text().split(' - ')[0];
            const courseName = selectedOption.data('course');
            const fee = selectedOption.data('fee');
            const email = selectedOption.data('email');

            $('#displayName').text(studentName);
            $('#displayEmail').text(email);
            $('#displayCourse').text(courseName);
            $('#displayFee').text(parseFloat(fee).toFixed(2));

            $('#studentInfoBox').slideDown();
        } else {
            $('#studentInfoBox').slideUp();
        }
    });

    // Form validation
    $('#certificateRequestForm').on('submit', function(e) {
        const studentId = $('#studentSelect').val();

        if (!studentId) {
            e.preventDefault();
            alert('Please select a student');
            return false;
        }

        // Show loading state
        $(this).find('button[type="submit"]')
            .prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin mr-2"></i>Submitting Request...');
    });
});
</script>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline:before {
    content: '';
    position: absolute;
    left: 8px;
    top: 5px;
    bottom: 5px;
    width: 2px;
    background: #e0e0e0;
}
.timeline-item {
    position: relative;
}
.timeline-marker {
    position: absolute;
    left: -26px;
    top: 2px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #e0e0e0;
}
</style>
@endsection
