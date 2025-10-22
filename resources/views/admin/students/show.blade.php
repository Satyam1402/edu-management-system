{{-- resources/views/admin/students/show.blade.php --}}
@extends('layouts.custom-admin')

@section('title', 'Student Details')
@section('page-title', 'Student Profile - ' . $student->name)

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/student-show.css') }}">
@endsection

@section('content')
<div class="profile-card">
    <!-- Enhanced Profile Header -->
    <div class="profile-header">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <div class="profile-avatar mx-auto">
                    @if($student->profile_photo)
                        <img src="{{ $student->profile_photo_url }}" alt="Profile" class="w-100 h-100 rounded-circle object-cover">
                    @else
                        <i class="fas fa-user"></i>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <h3 class="mb-2 font-weight-bold">{{ $student->name }}</h3>
                <p class="mb-1"><i class="fas fa-id-card mr-2"></i>Student ID: {{ $student->student_id }}</p>
                <p class="mb-1"><i class="fas fa-envelope mr-2"></i>{{ $student->email }}</p>
                <p class="mb-0"><i class="fas fa-phone mr-2"></i>{{ $student->phone }}</p>
            </div>
            <div class="col-md-3 text-center">
                <span class="status-badge status-{{ $student->status }}">
                    {{ ucfirst($student->status) }}
                </span>
                <div class="mt-3">
                    <small style="opacity: 0.9;">Enrolled on</small><br>
                    <strong>{{ $student->enrollment_date ? $student->enrollment_date->format('M d, Y') : 'N/A' }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body p-4">
        <div class="row">
            <!-- Personal Information -->
            <div class="col-lg-6">
                <div class="info-section">
                    <h5><i class="fas fa-user text-primary mr-2"></i>Personal Information</h5>
                    
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-user mr-2 text-muted"></i>Full Name</span>
                        <span class="info-value">{{ $student->name }}</span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-envelope mr-2 text-muted"></i>Email</span>
                        <span class="info-value">{{ $student->email }}</span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-phone mr-2 text-muted"></i>Phone</span>
                        <span class="info-value">{{ $student->phone }}</span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-calendar mr-2 text-muted"></i>Date of Birth</span>
                        <span class="info-value">
                            @if($student->date_of_birth)
                                {{ $student->date_of_birth->format('M d, Y') }} 
                                <small class="text-muted">({{ $student->age }} years)</small>
                            @else
                                <span class="text-muted">Not provided</span>
                            @endif
                        </span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-venus-mars mr-2 text-muted"></i>Gender</span>
                        <span class="info-value">
                            @if($student->gender == 'male')
                                👨 Male
                            @elseif($student->gender == 'female')
                                👩 Female
                            @else
                                🧑 {{ ucfirst($student->gender) }}
                            @endif
                        </span>
                    </div>
                    
                    @if($student->guardian_name)
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-user-shield mr-2 text-muted"></i>Guardian</span>
                        <span class="info-value">{{ $student->guardian_name }}</span>
                    </div>
                    @endif
                    
                    @if($student->guardian_phone)
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-phone-alt mr-2 text-muted"></i>Guardian Phone</span>
                        <span class="info-value">{{ $student->guardian_phone }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Address Information -->
            <div class="col-lg-6">
                <div class="info-section">
                    <h5><i class="fas fa-map-marker-alt text-danger mr-2"></i>Address Information</h5>
                    
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-home mr-2 text-muted"></i>Address</span>
                        <span class="info-value">{{ $student->address ?: 'Not provided' }}</span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-city mr-2 text-muted"></i>City</span>
                        <span class="info-value">{{ $student->city ?: 'Not provided' }}</span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-map mr-2 text-muted"></i>State</span>
                        <span class="info-value">{{ $student->state ?: 'Not provided' }}</span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-mail-bulk mr-2 text-muted"></i>Pincode</span>
                        <span class="info-value">{{ $student->pincode ?: 'Not provided' }}</span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-globe mr-2 text-muted"></i>Full Address</span>
                        <span class="info-value">
                            {{ $student->address ? $student->address . ', ' : '' }}
                            {{ $student->city ? $student->city . ', ' : '' }}
                            {{ $student->state ? $student->state . ' - ' : '' }}
                            {{ $student->pincode ?: '' }}
                        </span>
                    </div>
                </div>
                
                <!-- Academic Information -->
                <div class="info-section">
                    <h5><i class="fas fa-graduation-cap text-success mr-2"></i>Academic Information</h5>
                    
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-building mr-2 text-muted"></i>Franchise</span>
                        <span class="info-value">
                            @if($student->franchise)
                                {{ $student->franchise->name }}
                                <small class="text-muted">({{ $student->franchise->code }})</small>
                            @else
                                <span class="text-muted">Not assigned</span>
                            @endif
                        </span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-book mr-2 text-muted"></i>Course</span>
                        <span class="info-value">
                            @if($student->course)
                                {{ $student->course->name }}
                                @if($student->course->duration)
                                    <small class="text-muted">({{ $student->course->duration }})</small>
                                @endif
                            @else
                                <span class="text-muted">Not enrolled</span>
                            @endif
                        </span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-layer-group mr-2 text-muted"></i>Batch</span>
                        <span class="info-value">{{ $student->batch ?: 'Not assigned' }}</span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-calendar-check mr-2 text-muted"></i>Enrollment Date</span>
                        <span class="info-value">
                            {{ $student->enrollment_date ? $student->enrollment_date->format('M d, Y') : 'Not set' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number">₹{{ number_format($student->getTotalPaidAmount(), 2) }}</div>
                    <div class="stats-label">Total Paid</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number">₹{{ number_format($student->getPendingPaymentAmount(), 2) }}</div>
                    <div class="stats-label">Pending Payments</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number">{{ $student->certificates()->count() }}</div>
                    <div class="stats-label">Certificates</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number">{{ number_format($student->getExamAverage(), 1) }}%</div>
                    <div class="stats-label">Exam Average</div>
                </div>
            </div>
        </div>

        @if($student->notes)
        <div class="info-section mt-4">
            <h5><i class="fas fa-sticky-note text-warning mr-2"></i>Additional Notes</h5>
            <div class="bg-light p-3 rounded">
                {{ $student->notes }}
            </div>
        </div>
        @endif
    </div>

    <!-- Enhanced Action Footer -->
    <div class="action-buttons">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="mb-2 mb-md-0">
                <a href="{{ route('admin.students.index') }}" class="btn btn-secondary btn-custom">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
                <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-primary btn-custom">
                    <i class="fas fa-edit mr-2"></i>Edit Student
                </a>
            </div>
            <div>
                <a href="{{ route('admin.payments.create') }}?student={{ $student->id }}" class="btn btn-success btn-custom">
                    <i class="fas fa-credit-card mr-2"></i>Create Payment
                </a>
                <div class="btn-group">
                    <button type="button" class="btn btn-info btn-custom dropdown-toggle" data-toggle="dropdown">
                        <i class="fas fa-ellipsis-v mr-2"></i>More Actions
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('admin.certificates.create') }}?student={{ $student->id }}">
                            <i class="fas fa-certificate mr-2"></i>Issue Certificate
                        </a>
                        <a class="dropdown-item" href="#" onclick="printStudent()">
                            <i class="fas fa-print mr-2"></i>Print Profile
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-file-export mr-2"></i>Export Data
                        </a>
                        <div class="dropdown-divider"></div>
                        @if($student->status === 'active')
                        <a class="dropdown-item text-warning" href="#" onclick="changeStatus('inactive')">
                            <i class="fas fa-pause mr-2"></i>Mark Inactive
                        </a>
                        @endif
                        @if($student->status !== 'graduated')
                        <a class="dropdown-item text-success" href="#" onclick="changeStatus('graduated')">
                            <i class="fas fa-graduation-cap mr-2"></i>Mark Graduated
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Student Timeline (Optional Enhancement) -->
@if($student->payments()->count() > 0 || $student->certificates()->count() > 0)
<div class="profile-card">
    <div class="card-body">
        <h5 class="mb-4"><i class="fas fa-history mr-2 text-primary"></i>Recent Activity</h5>
        <div class="timeline">
            @foreach($student->payments()->latest()->take(3)->get() as $payment)
            <div class="timeline-item">
                <div class="timeline-marker bg-success"></div>
                <div class="timeline-content">
                    <h6 class="mb-1">Payment {{ $payment->status === 'completed' ? 'Received' : 'Created' }}</h6>
                    <p class="mb-1">{{ $payment->formatted_amount }} - {{ $payment->payment_type }}</p>
                    <small class="text-muted">{{ $payment->created_at->diffForHumans() }}</small>
                </div>
            </div>
            @endforeach
            
            @foreach($student->certificates()->latest()->take(2)->get() as $certificate)
            <div class="timeline-item">
                <div class="timeline-marker bg-primary"></div>
                <div class="timeline-content">
                    <h6 class="mb-1">Certificate Issued</h6>
                    <p class="mb-1">{{ $certificate->course->name ?? 'Course Certificate' }}</p>
                    <small class="text-muted">{{ $certificate->created_at->diffForHumans() }}</small>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection

@section('js')
<script>
// Set JavaScript variables for the external JS file
window.studentUpdateRoute = '{{ route("admin.students.update", $student) }}';
window.studentExportRoute = '{{ route("admin.students.export", $student) }}'; 
window.studentData = {
    'name': '{{ $student->name }}',
    'email': '{{ $student->email }}',
    'phone': '{{ $student->phone }}',
    'address': '{{ $student->address }}',
    'city': '{{ $student->city }}',
    'state': '{{ $student->state }}',
    'pincode': '{{ $student->pincode }}',
    'franchise_id': '{{ $student->franchise_id }}',
    'date_of_birth': '{{ $student->date_of_birth?->format("Y-m-d") }}',
    'gender': '{{ $student->gender }}',
    'student_id': '{{ $student->student_id }}'
};
</script>
<!-- <script src="{{ asset('js/admin/student-show.js') }}"></script> -->
<script src="{{ asset('js/admin/students/show.js') }}"></script>
@endsection

