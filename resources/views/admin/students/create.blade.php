{{-- resources/views/admin/students/create.blade.php --}}
@extends('layouts.custom-admin')

@section('title', 'Create Student')
@section('page-title', 'Add New Student')

@section('css')
<!-- Custom Student Create Form Styles -->
<link rel="stylesheet" href="{{ asset('css/admin/student-create.css') }}">
@endsection

@section('content')
<div class="card create-form-card">
    <!-- Enhanced Header -->
    <div class="create-header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h3 class="mb-2 font-weight-bold">
                    <i class="fas fa-user-plus mr-3"></i>Add New Student
                </h3>
                <p class="mb-0 h6" style="opacity: 0.9;">
                    Register a new student to your learning management system
                </p>
            </div>
            <div class="text-right d-none d-md-block">
                <div style="font-size: 4rem; opacity: 0.2;">
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body px-4">
        <!-- STUDENT CREATION FORM -->
        <form action="{{ route('admin.students.store') }}" method="POST" id="studentForm">
            @csrf

            <div class="row">
                <!-- Personal Information Section -->
                <div class="col-md-6">
                    <div class="section-divider">
                        <h6 class="mb-0 font-weight-bold text-dark">
                            <i class="fas fa-user mr-2 text-primary"></i>Personal Information
                        </h6>
                    </div>

                    <div class="form-group">
                        <label for="name" class="form-label-enhanced required-marker">Full Name</label>
                        <div class="input-icon-group">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" class="form-control form-control-enhanced input-with-icon @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}"
                                   placeholder="Enter student's full name" required>
                        </div>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label-enhanced required-marker">Email Address</label>
                        <div class="input-icon-group">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" class="form-control form-control-enhanced input-with-icon @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}"
                                   placeholder="Enter email here" required>
                        </div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label-enhanced required-marker">Phone Number</label>
                        <div class="input-icon-group">
                            <i class="fas fa-phone input-icon"></i>
                            <input type="tel" class="form-control form-control-enhanced input-with-icon @error('phone') is-invalid @enderror"
                                   id="phone" name="phone" value="{{ old('phone') }}"
                                required maxlength="10">
                        </div>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_of_birth" class="form-label-enhanced required-marker">Date of Birth</label>
                                <div class="input-icon-group">
                                    <i class="fas fa-calendar-alt input-icon"></i>
                                    <input type="date" class="form-control form-control-enhanced input-with-icon @error('date_of_birth') is-invalid @enderror"
                                           id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                                </div>
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="gender" class="form-label-enhanced required-marker">Gender</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-venus-mars text-info"></i>
                                    </span>
                                </div>
                                <select class="form-control @error('gender') is-invalid @enderror"
                                        id="gender" name="gender" required>
                                    <option value="" disabled selected style="color: #6c757d;">-- Select Gender --</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>👨 Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>👩 Female</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>🧑 Other</option>
                                </select>
                            </div>
                            @error('gender')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="guardian_name" class="form-label-enhanced">Guardian Name</label>
                        <div class="input-icon-group">
                            <i class="fas fa-user-shield input-icon"></i>
                            <input type="text" class="form-control form-control-enhanced input-with-icon @error('guardian_name') is-invalid @enderror"
                                   id="guardian_name" name="guardian_name" value="{{ old('guardian_name') }}"
                                   placeholder="Parent or guardian name">
                        </div>
                        @error('guardian_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="guardian_phone" class="form-label-enhanced">Guardian Phone</label>
                        <div class="input-icon-group">
                            <i class="fas fa-phone-alt input-icon"></i>
                            <input type="tel" class="form-control form-control-enhanced input-with-icon @error('guardian_phone') is-invalid @enderror"
                                   id="guardian_phone" name="guardian_phone" value="{{ old('guardian_phone') }}"
                                   placeholder="Guardian contact number" maxlength="10">
                        </div>
                        @error('guardian_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Address & Academic Information Section -->
                <div class="col-md-6">
                    <div class="section-divider">
                        <h6 class="mb-0 font-weight-bold text-dark">
                            <i class="fas fa-map-marker-alt mr-2 text-danger"></i>Address Information
                        </h6>
                    </div>

                    <div class="form-group">
                        <label for="address" class="form-label-enhanced required-marker">Street Address</label>
                        <textarea class="form-control form-control-enhanced @error('address') is-invalid @enderror"
                                  id="address" name="address" rows="3"
                                  placeholder="Enter complete street address" required>{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="city" class="form-label-enhanced required-marker">City</label>
                                <div class="input-icon-group">
                                    <i class="fas fa-city input-icon"></i>
                                    <input type="text" class="form-control form-control-enhanced input-with-icon @error('city') is-invalid @enderror"
                                           id="city" name="city" value="{{ old('city') }}"
                                           placeholder="Enter city name" required>
                                </div>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="state" class="form-label-enhanced required-marker">State</label>
                                <div class="input-icon-group">
                                    <i class="fas fa-map input-icon"></i>
                                    <input type="text" class="form-control form-control-enhanced input-with-icon @error('state') is-invalid @enderror"
                                           id="state" name="state" value="{{ old('state') }}"
                                           placeholder="Enter state name" required>
                                </div>
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pincode" class="form-label-enhanced required-marker">Pincode</label>
                        <div class="input-icon-group">
                            <i class="fas fa-mail-bulk input-icon"></i>
                            <input type="text" class="form-control form-control-enhanced input-with-icon @error('pincode') is-invalid @enderror"
                                   id="pincode" name="pincode" value="{{ old('pincode') }}"
                                   placeholder="Enter pincode here" required>
                        </div>
                        @error('pincode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Academic Information -->
                    <div class="section-divider mt-4">
                        <h6 class="mb-0 font-weight-bold text-dark">
                            <i class="fas fa-graduation-cap mr-2 text-success"></i>Academic Information
                        </h6>
                    </div>

                   <div class="form-group">
                        <label for="franchise_id" class="form-label-enhanced required-marker">Franchise</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-building text-primary"></i>
                                </span>
                            </div>
                            <select class="form-control @error('franchise_id') is-invalid @enderror"
                                    id="franchise_id" name="franchise_id" required>
                                <option value="" disabled selected style="color: #6c757d;">-- Select Franchise --</option>
                                @foreach($franchises as $franchise)
                                    <option value="{{ $franchise->id }}" {{ old('franchise_id') == $franchise->id ? 'selected' : '' }}>
                                        {{ $franchise->name }} ({{ $franchise->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('franchise_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="course_id" class="form-label-enhanced">Course</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-book text-info"></i>
                                </span>
                            </div>
                            <select class="form-control @error('course_id') is-invalid @enderror"
                                    id="course_id" name="course_id">
                                <option value="" disabled selected style="color: #6c757d;">-- Select Course (Optional) --</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('course_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="batch" class="form-label-enhanced">Batch</label>
                        <div class="input-icon-group">
                            <i class="fas fa-layer-group input-icon"></i>
                            <input type="text" class="form-control form-control-enhanced input-with-icon @error('batch') is-invalid @enderror"
                                   id="batch" name="batch" value="{{ old('batch') }}"
                                   placeholder="Enter batch here">
                        </div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle mr-1"></i>Batch or class group identifier
                        </small>
                        @error('batch')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label-enhanced required-marker">Initial Status</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-toggle-on text-success"></i>
                                </span>
                            </div>
                            <select class="form-control @error('status') is-invalid @enderror"
                                    id="status" name="status" required>
                                <option value="" disabled selected style="color: #6c757d;">-- Select Initial Status --</option>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                    ✅ Active
                                </option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                    ⏸️ Inactive
                                </option>
                            </select>
                        </div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle mr-1"></i>Choose the initial enrollment status
                        </small>
                        @error('status')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Enhanced Action Footer -->
            <div class="action-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('admin.students.index') }}" class="btn btn-secondary btn-enhanced">
                            <i class="fas fa-arrow-left mr-2"></i>Back to List
                        </a>
                    </div>
                    <div class="d-flex">
                        <button type="button" class="btn btn-outline-warning btn-enhanced mr-3" onclick="resetForm()">
                            <i class="fas fa-redo mr-2"></i>Reset Form
                        </button>
                        <button type="submit" class="btn btn-success btn-enhanced">
                            <i class="fas fa-user-plus mr-2"></i>Create Student
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<!-- <script src="{{ asset('js/admin/student-create.js') }}"></script> -->
<script src="{{ asset('js/admin/students/create.js') }}"></script>
@endsection
