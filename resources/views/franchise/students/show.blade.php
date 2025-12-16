@extends('layouts.custom-admin')

@section('page-title', 'Student Details')

@section('css')
<style>
    .card {
        border-radius: 15px;
        box-shadow: 0 4px 18px rgba(102,126,234,.07);
        border: none;
    }
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        border-radius: 15px 15px 0 0 !important;
        padding: 20px;
    }
    .detail-row {
        padding: 14px 0;
        border-bottom: 1px solid #f1f3fa;
    }
    .detail-label {
        color: #786fa6;
        font-weight: 600;
    }
    .detail-value {
        font-weight: 500;
        color: #222a42;
    }
    @media (max-width: 575px) {
        .detail-row.d-flex {
            display: block !important;
        }
        .detail-label, .detail-value {
            display: block !important;
            width: 100%;
            text-align: left;
        }
        .card-header, .card-body {
            padding: 15px !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Student Details</h4>
                    <p class="text-muted mb-0">All information about this student</p>
                </div>
                <a href="{{ route('franchise.students.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Student Card -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-user mr-2"></i>{{ $student->name }}</h5>
        </div>
        <div class="card-body">

            <!-- Personal Info -->
            <div class="mb-4">
                <h6 class="text-primary mb-3" style="font-weight:600;">Personal Information</h6>
                <div class="detail-row d-flex justify-content-between">
                    <span class="detail-label">Full Name:</span>
                    <span class="detail-value">{{ $student->full_name  }}</span>
                </div>
                <div class="detail-row d-flex justify-content-between">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">{{ $student->email }}</span>
                </div>
                <div class="detail-row d-flex justify-content-between">
                    <span class="detail-label">Phone:</span>
                    <span class="detail-value">{{ $student->phone }}</span>
                </div>
                <div class="detail-row d-flex justify-content-between">
                    <span class="detail-label">Date of Birth:</span>
                    <span class="detail-value">
                        {{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d M Y') : '-' }}
                    </span>
                </div>
            </div>

            <!-- Address Info -->
            <div class="mb-4">
                <h6 class="text-primary mb-3" style="font-weight:600;">Address Information</h6>
                <div class="detail-row d-flex justify-content-between">
                    <span class="detail-label">Address:</span>
                    <span class="detail-value">{{ $student->address ?: '-' }}</span>
                </div>
                <div class="detail-row d-flex justify-content-between">
                    <span class="detail-label">City:</span>
                    <span class="detail-value">{{ $student->city ?: '-' }}</span>
                </div>
                <div class="detail-row d-flex justify-content-between">
                    <span class="detail-label">State:</span>
                    <span class="detail-value">{{ $student->state ?: '-' }}</span>
                </div>
                <div class="detail-row d-flex justify-content-between">
                    <span class="detail-label">Pincode:</span>
                    <span class="detail-value">{{ $student->pincode ?: '-' }}</span>
                </div>
            </div>

            <!-- Academic Info -->
            <div class="mb-3">
                <h6 class="text-primary mb-3" style="font-weight:600;">Academic Information</h6>
                <div class="detail-row d-flex justify-content-between">
                    <span class="detail-label">Course:</span>
                    <span class="detail-value">
                        @if($student->course)
                            {{ $student->course->name }}
                        @else
                            -
                        @endif
                    </span>
                </div>
                <div class="detail-row d-flex justify-content-between">
                    <span class="detail-label">Enrollment Date:</span>
                    <span class="detail-value">
                        {{ $student->enrollment_date ? \Carbon\Carbon::parse($student->enrollment_date)->format('d M Y') : '-' }}
                    </span>
                </div>
                <div class="detail-row d-flex justify-content-between">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        @switch($student->status)
                            @case('active') <span class="badge badge-success">Active</span> @break
                            @case('inactive') <span class="badge badge-secondary">Inactive</span> @break
                            @case('graduated') <span class="badge badge-info">Graduated</span> @break
                            @case('dropped') <span class="badge badge-danger">Dropped</span> @break
                            @default <span class="badge badge-light">-</span>
                        @endswitch
                    </span>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex mt-4">
                <a href="{{ route('franchise.students.edit', $student->id) }}" class="btn btn-primary mr-2">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
                <a href="{{ route('franchise.students.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i>Back
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
