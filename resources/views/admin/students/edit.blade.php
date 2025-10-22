{{-- resources/views/admin/students/edit.blade.php --}}
@extends('layouts.custom-admin')

@section('title', 'Edit Student')
@section('page-title', 'Edit Student - ' . $student->name)

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/student-edit.css') }}">
@endsection

@section('content')
<div class="card form-card">
    <!-- Enhanced Header -->
    <div class="form-header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-1 font-weight-bold">
                    <i class="fas fa-user-edit mr-2"></i>Edit Student Details
                </h4>
                <p class="mb-0" style="opacity: 0.9;">Update information for {{ $student->name }}</p>
            </div>
            <div class="text-right">
                <span class="badge badge-light px-3 py-2" style="font-size: 14px;">
                    <i class="fas fa-id-card mr-1"></i>{{ $student->student_id }}
                </span>
            </div>
        </div>
    </div>

    <div class="card-body p-4">
        <!-- STUDENT EDIT FORM -->
        <form action="{{ route('admin.students.update', $student) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Personal Information Section -->
                <div class="col-md-6">
                    <div class="section-header">
                        <h6 class="mb-0 font-weight-bold text-dark">
                            <i class="fas fa-user mr-2 text-primary"></i>Personal Information
                        </h6>
                    </div>

                    <div class="form-group">
                        <label for="name" class="form-label-enhanced required-marker">Full Name</label>
                        <input type="text" class="form-control form-control-enhanced @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $student->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label-enhanced required-marker">Email Address</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-envelope text-primary"></i>
                                </span>
                            </div>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $student->email) }}" required>
                        </div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label-enhanced required-marker">Phone Number</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-phone text-success"></i>
                                </span>
                            </div>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                   id="phone" name="phone" value="{{ old('phone', $student->phone) }}" required>
                        </div>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_of_birth" class="form-label-enhanced required-marker">Date of Birth</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-calendar-alt text-info"></i>
                                        </span>
                                    </div>
                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                           id="date_of_birth" name="date_of_birth" 
                                           value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}" required>
                                </div>
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gender" class="form-label-enhanced required-marker">Gender</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-venus-mars text-info"></i>
                                        </span>
                                    </div>
                                    <select class="form-control @error('gender') is-invalid @enderror" 
                                            id="gender" name="gender" required>
                                        <option value="">-- Select Gender --</option>
                                        <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>👨 Male</option>
                                        <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>👩 Female</option>
                                        <option value="other" {{ old('gender', $student->gender) == 'other' ? 'selected' : '' }}>🧑 Other</option>
                                    </select>
                                </div>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="guardian_name" class="form-label-enhanced">Guardian Name</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-user-shield text-warning"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control @error('guardian_name') is-invalid @enderror"
                                   id="guardian_name" name="guardian_name" 
                                   value="{{ old('guardian_name', $student->guardian_name) }}">
                        </div>
                        @error('guardian_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="guardian_phone" class="form-label-enhanced">Guardian Phone</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-phone-alt text-warning"></i>
                                </span>
                            </div>
                            <input type="tel" class="form-control @error('guardian_phone') is-invalid @enderror"
                                   id="guardian_phone" name="guardian_phone" 
                                   value="{{ old('guardian_phone', $student->guardian_phone) }}">
                        </div>
                        @error('guardian_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status Field -->
                    <div class="form-group">
                        <label>Status *</label>
                        <div class="mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status_active" value="active" 
                                    {{ old('status', $student->status) == 'active' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="status_active">Active</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status_inactive" value="inactive" 
                                    {{ old('status', $student->status) == 'inactive' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_inactive">Inactive</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status_graduated" value="graduated" 
                                    {{ old('status', $student->status) == 'graduated' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_graduated">Graduated</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status_dropped" value="dropped" 
                                    {{ old('status', $student->status) == 'dropped' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_dropped">Dropped</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status_suspended" value="suspended" 
                                    {{ old('status', $student->status) == 'suspended' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_suspended">Suspended</label>
                            </div>
                        </div>
                        @error('status')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Address & Academic Information Section -->
                <div class="col-md-6">
                    <div class="section-header">
                        <h6 class="mb-0 font-weight-bold text-dark">
                            <i class="fas fa-map-marker-alt mr-2 text-danger"></i>Address Information
                        </h6>
                    </div>

                    <div class="form-group">
                        <label for="address" class="form-label-enhanced required-marker">Street Address</label>
                        <textarea class="form-control form-control-enhanced @error('address') is-invalid @enderror"
                                  id="address" name="address" rows="3" 
                                  placeholder="Enter complete street address" required>{{ old('address', $student->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="city" class="form-label-enhanced required-marker">City</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-city text-info"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror"
                                           id="city" name="city" value="{{ old('city', $student->city) }}" required>
                                </div>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="state" class="form-label-enhanced required-marker">State</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-map text-warning"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control @error('state') is-invalid @enderror"
                                           id="state" name="state" value="{{ old('state', $student->state) }}" required>
                                </div>
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pincode" class="form-label-enhanced required-marker">Pincode</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-mail-bulk text-secondary"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control @error('pincode') is-invalid @enderror"
                                   id="pincode" name="pincode" value="{{ old('pincode', $student->pincode) }}" required>
                        </div>
                        @error('pincode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Academic Information -->
                    <div class="section-header mt-4">
                        <h6 class="mb-0 font-weight-bold text-dark">
                            <i class="fas fa-graduation-cap mr-2 text-success"></i>Academic Information
                        </h6>
                    </div>

                    <div class="form-group">
                        <label for="franchise_id" class="form-label-enhanced required-marker">Franchise</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-building text-primary"></i>
                                </span>
                            </div>
                            <select class="form-control @error('franchise_id') is-invalid @enderror" 
                                    id="franchise_id" name="franchise_id" required>
                                <option value="">-- Select Franchise --</option>
                                @foreach($franchises as $franchise)
                                    <option value="{{ $franchise->id }}" {{ old('franchise_id', $student->franchise_id) == $franchise->id ? 'selected' : '' }}>
                                        {{ $franchise->name }} ({{ $franchise->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('franchise_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="course_id" class="form-label-enhanced">Course</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-book text-info"></i>
                                </span>
                            </div>
                            <select class="form-control @error('course_id') is-invalid @enderror" 
                                    id="course_id" name="course_id">
                                <option value="">-- Select Course (Optional) --</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id', $student->course_id) == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('course_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="batch" class="form-label-enhanced">Batch</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-layer-group text-info"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control @error('batch') is-invalid @enderror"
                                   id="batch" name="batch" value="{{ old('batch', $student->batch) }}">
                        </div>
                        @error('batch')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('admin.students.index') }}" class="btn btn-secondary btn-custom">
                            <i class="fas fa-arrow-left mr-2"></i>Back to List
                        </a>
                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-info btn-custom ml-2">
                            <i class="fas fa-eye mr-2"></i>View Details
                        </a>
                    </div>
                    <div>
                        <button type="button" class="btn btn-warning btn-custom mr-2" onclick="resetForm()">
                            <i class="fas fa-undo mr-2"></i>Reset Changes
                        </button>
                        <button type="submit" class="btn btn-primary btn-custom">
                            <i class="fas fa-save mr-2"></i>Update Student
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<!-- <script src="{{ asset('js/admin/student-edit.js') }}"></script> -->
<script src="{{ asset('js/admin/students/edit.js') }}"></script>
@endsection
