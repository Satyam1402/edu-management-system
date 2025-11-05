@extends('layouts.custom-admin')

@section('page-title', 'Edit Certificate Request')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-edit"></i> Edit Certificate Request #{{ $request->id }}</h4>
                    <a href="{{ route('franchise.certificate-requests.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
                
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <h5><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</h5>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    <form action="{{ route('franchise.certificate-requests.update', $request->id) }}" method="POST" id="editCertificateForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Student Selection -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="student_id">
                                        <i class="fas fa-user-graduate"></i> Student 
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="student_id" id="student_id" class="form-control @error('student_id') is-invalid @enderror" required>
                                        <option value="">-- Select Student --</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" 
                                                data-name="{{ $student->name }}"
                                                data-email="{{ $student->email }}"
                                                {{ old('student_id', $request->student_id) == $student->id ? 'selected' : '' }}>
                                                {{ $student->name }} ({{ $student->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('student_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Course Selection -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="course_id">
                                        <i class="fas fa-book"></i> Course 
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="course_id" id="course_id" class="form-control @error('course_id') is-invalid @enderror" required>
                                        <option value="">-- Select Course --</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" 
                                                data-fee="{{ $course->certificate_fee ?? 100 }}"
                                                {{ old('course_id', $request->course_id) == $course->id ? 'selected' : '' }}>
                                                {{ $course->name }} - ₹{{ number_format($course->certificate_fee ?? 100, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Certificate Type -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="certificate_type">
                                        <i class="fas fa-certificate"></i> Certificate Type
                                    </label>
                                    <select name="certificate_type" id="certificate_type" class="form-control">
                                        <option value="Course Completion Certificate" 
                                            {{ old('certificate_type', $request->certificate_type) == 'Course Completion Certificate' ? 'selected' : '' }}>
                                            Course Completion Certificate
                                        </option>
                                        <option value="Standard Certificate" 
                                            {{ old('certificate_type', $request->certificate_type) == 'Standard Certificate' ? 'selected' : '' }}>
                                            Standard Certificate
                                        </option>
                                        <option value="Premium Certificate" 
                                            {{ old('certificate_type', $request->certificate_type) == 'Premium Certificate' ? 'selected' : '' }}>
                                            Premium Certificate
                                        </option>
                                        <option value="Merit Certificate" 
                                            {{ old('certificate_type', $request->certificate_type) == 'Merit Certificate' ? 'selected' : '' }}>
                                            Merit Certificate
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="notes">
                                        <i class="fas fa-sticky-note"></i> Notes / Description
                                    </label>
                                    <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Add any special instructions or notes...">{{ old('notes', $request->notes) }}</textarea>
                                    <small class="form-text text-muted">Optional: Add any special requirements or notes for this certificate request.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Cost Summary Card -->
                        <div class="card bg-light border mt-4">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-calculator"></i> Cost Summary</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="info-box bg-white">
                                            <div class="info-box-content">
                                                <span class="info-box-text">Current Amount</span>
                                                <span class="info-box-number" id="oldAmount">
                                                    ₹{{ number_format($request->amount, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box bg-white">
                                            <div class="info-box-content">
                                                <span class="info-box-text">New Amount</span>
                                                <span class="info-box-number text-primary" id="newAmount">
                                                    ₹{{ number_format($request->amount, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box bg-white">
                                            <div class="info-box-content">
                                                <span class="info-box-text">Price Difference</span>
                                                <h3 id="difference" class="badge badge-secondary">₹0.00</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info mb-0 mt-3">
                                    <i class="fas fa-wallet"></i> <strong>Current Wallet Balance:</strong> 
                                    <span class="badge badge-success badge-lg">₹{{ number_format($walletBalance, 2) }}</span>
                                    <p class="mb-0 mt-2 small">
                                        <i class="fas fa-info-circle"></i> 
                                        If you select a more expensive course, the difference will be deducted from your wallet. 
                                        If you select a cheaper course, the difference will be refunded to your wallet.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4 d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i> Update Request
                                </button>
                                <a href="{{ route('franchise.certificate-requests.index') }}" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                            <div>
                                <span class="badge badge-warning badge-lg">
                                    <i class="fas fa-clock"></i> Status: {{ ucfirst($request->status) }}
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const oldAmount = {{ $request->amount }};
    const walletBalance = {{ $walletBalance }};
    
    $(document).ready(function() {
        // Calculate cost when course changes
        $('#course_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const newFee = parseFloat(selectedOption.data('fee')) || 100;
            
            const difference = newFee - oldAmount;
            
            // Update new amount display
            $('#newAmount').text('₹' + newFee.toFixed(2));
            
            // Update difference badge
            if (difference > 0) {
                // More expensive - additional charge
                $('#difference').removeClass('badge-secondary badge-success').addClass('badge-danger');
                $('#difference').html('<i class="fas fa-arrow-up"></i> +₹' + difference.toFixed(2) + ' (Additional Charge)');
                
                // Check if wallet has sufficient balance
                if (difference > walletBalance) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Insufficient Balance!',
                        text: 'Your wallet balance is insufficient for this change. Additional required: ₹' + difference.toFixed(2),
                        confirmButtonColor: '#d33'
                    });
                }
            } else if (difference < 0) {
                // Cheaper - refund
                $('#difference').removeClass('badge-secondary badge-danger').addClass('badge-success');
                $('#difference').html('<i class="fas fa-arrow-down"></i> ₹' + Math.abs(difference).toFixed(2) + ' (Refund)');
            } else {
                // Same price
                $('#difference').removeClass('badge-danger badge-success').addClass('badge-secondary');
                $('#difference').html('<i class="fas fa-equals"></i> ₹0.00 (No Change)');
            }
        });
        
        // Trigger calculation on page load
        $('#course_id').trigger('change');
        
        // Form submission with confirmation
        $('#editCertificateForm').on('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const difference = parseFloat($('#difference').text().replace(/[^\d.-]/g, ''));
            
            let message = 'Are you sure you want to update this certificate request?';
            
            if (difference > 0) {
                message += '\n\nAdditional ₹' + Math.abs(difference).toFixed(2) + ' will be deducted from your wallet.';
            } else if (difference < 0) {
                message += '\n\n₹' + Math.abs(difference).toFixed(2) + ' will be refunded to your wallet.';
            }
            
            Swal.fire({
                title: 'Confirm Update',
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
