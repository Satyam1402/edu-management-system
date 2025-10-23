@extends('layouts.custom-admin')

@section('page-title', 'Create Certificate')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/admin/certificates/create.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="text-muted mb-0">Create new certificate</h4>
                </div>
                <a href="{{ route('admin.certificates.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Certificates
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Certificate Form -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 font-weight-bold text-white">
                        <i class="fas fa-plus-circle mr-2"></i>Certificate Details
                    </h6>
                </div>
                <div class="card-body">
                    <form id="certificateForm" action="{{ route('admin.certificates.store') }}" method="POST">
                        @csrf
                        
                        <!-- Student Selection -->
                        <div class="form-section">
                            <h5><i class="fas fa-user-graduate mr-2"></i>Student Information</h5>
                            
                            <div class="form-group">
                                <label for="student_id">Select Student <span class="text-danger">*</span></label>
                                <select name="student_id" id="student_id" class="form-control" required>
                                    <option value="">Choose a student...</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" data-email="{{ $student->email }}">
                                            {{ $student->name }} - {{ $student->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div id="studentInfo" class="alert alert-info" style="display: none;">
                                <strong>Selected Student:</strong>
                                <div id="studentDetails"></div>
                            </div>
                        </div>

                        <!-- Course Selection -->
                        <div class="form-section">
                            <h5><i class="fas fa-book mr-2"></i>Course Information</h5>
                            
                            <div class="form-group">
                                <label for="course_id">Select Course <span class="text-danger">*</span></label>
                                <select name="course_id" id="course_id" class="form-control" required>
                                    <option value="">Choose a course...</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">
                                            {{ $course->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Status Selection -->
                        <div class="form-section">
                            <h5><i class="fas fa-tasks mr-2"></i>Certificate Status</h5>
                            
                            <div class="status-options">
                                <div class="status-option">
                                    <div class="status-card requested active" data-status="requested">
                                        <div class="icon"><i class="fas fa-clock"></i></div>
                                        <h6>Requested</h6>
                                        <small>Awaiting approval</small>
                                    </div>
                                </div>
                                
                                <div class="status-option">
                                    <div class="status-card approved" data-status="approved">
                                        <div class="icon"><i class="fas fa-check"></i></div>
                                        <h6>Approved</h6>
                                        <small>Ready to issue</small>
                                    </div>
                                </div>
                                
                                <div class="status-option">
                                    <div class="status-card issued" data-status="issued">
                                        <div class="icon"><i class="fas fa-certificate"></i></div>
                                        <h6>Issued</h6>
                                        <small>Certificate delivered</small>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="status" id="status" value="requested">
                            @error('status')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="text-right">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save mr-2"></i>Create Certificate
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Preview Section -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 font-weight-bold text-white">
                        <i class="fas fa-eye mr-2"></i>Preview
                    </h6>
                </div>
                <div class="card-body">
                    <div class="preview-section">
                        <h6 class="mb-3">Certificate Preview</h6>
                        
                        <div class="certificate-preview">
                            <div class="mb-3">
                                <i class="fas fa-certificate text-success" style="font-size: 3rem;"></i>
                            </div>
                            
                            <h5 class="text-success">CERTIFICATE OF COMPLETION</h5>
                            
                            <hr class="my-3">
                            
                            <p class="mb-2">This is to certify that</p>
                            <h6 id="previewStudentName" class="text-primary">Student Name</h6>
                            <p class="mb-2">has successfully completed</p>
                            <h6 id="previewCourseName" class="text-success">Course Name</h6>
                            
                            <hr class="my-3">
                            
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">Certificate #</small><br>
                                    <strong id="previewCertNumber">CERT-XXXXXXXX</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Status</small><br>
                                    <span id="previewStatus" class="badge badge-warning">Requested</span>
                                </div>
                            </div>
                        </div>
                        
                        <small class="text-muted">* This is a preview. Actual certificate design may vary.</small>
                    </div>
                </div>
            </div>

            <!-- Help Section -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 font-weight-bold text-white">
                        <i class="fas fa-info-circle mr-2"></i>Certificate Workflow
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge badge-warning mr-2">1</span>
                            <strong>Requested</strong>
                        </div>
                        <small class="text-muted">Certificate request is created and awaiting approval.</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge badge-success mr-2">2</span>
                            <strong>Approved</strong>
                        </div>
                        <small class="text-muted">Certificate is approved and ready to be issued.</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge badge-primary mr-2">3</span>
                            <strong>Issued</strong>
                        </div>
                        <small class="text-muted">Certificate is issued and available for download.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('js/admin/certificates/create.js') }}"></script>
@endsection
