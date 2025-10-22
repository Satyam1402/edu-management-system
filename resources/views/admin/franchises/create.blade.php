{{-- resources/views/admin/franchises/create.blade.php - FIXED FORM SUBMISSION --}}
@extends('layouts.custom-admin')

@section('title', 'Create Franchise')
@section('page-title', 'Create New Franchise')

@section('css')
<!-- Custom Franchise Create Form Styles -->
<link rel="stylesheet" href="{{ asset('css/admin/franchise-create.css') }}">
@endsection

@section('content')
<div class="card create-form-card">
    <!-- Enhanced Header -->
    <div class="create-header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h3 class="mb-2 font-weight-bold">
                    <i class="fas fa-building mr-3"></i>Create New Franchise
                </h3>
                <p class="mb-0 h6" style="opacity: 0.9;">
                    Expand your business by adding a new franchise location
                </p>
            </div>
            <div class="text-right d-none d-md-block">
                <div style="font-size: 4rem; opacity: 0.2;">
                    <i class="fas fa-plus-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body px-4">
        <!-- SINGLE FORM - NO HIDDEN FORMS -->
        <form action="{{ route('admin.franchises.store') }}" method="POST" id="franchiseForm">
            @csrf

            <div class="row">
                <!-- Franchise Information Section -->
                <div class="col-md-6">
                    <div class="section-divider">
                        <h6 class="mb-0 font-weight-bold text-dark">
                            <i class="fas fa-building mr-2 text-success"></i>Franchise Information
                        </h6>
                    </div>

                    <div class="form-group">
                        <label for="name" class="form-label-enhanced required-marker">Franchise Name</label>
                        <div class="input-icon-group">
                            <i class="fas fa-building input-icon"></i>
                            <input type="text" class="form-control form-control-enhanced input-with-icon @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" 
                                   placeholder="Enter franchise name" required>
                        </div>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="code" class="form-label-enhanced required-marker">Franchise Code</label>
                        <div class="input-icon-group">
                            <i class="fas fa-hashtag input-icon"></i>
                            <input type="text" class="form-control form-control-enhanced input-with-icon @error('code') is-invalid @enderror"
                                   id="code" name="code" value="{{ old('code') }}" 
                                   placeholder="e.g., FR001, MUM01" required>
                        </div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle mr-1"></i>Unique identifier for this franchise
                        </small>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label-enhanced required-marker">Official Email</label>
                        <div class="input-icon-group">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" class="form-control form-control-enhanced input-with-icon @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}" 
                                   placeholder="franchise@example.com" required>
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
                                   placeholder="+91 9876543210" required>
                        </div>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label-enhanced required-marker">Initial Status</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="background: #f8f9fa; border-color: #e9ecef;">
                                    <i class="fas fa-toggle-on text-success"></i>
                                </span>
                            </div>
                            <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" name="status" required 
                                    style="border-radius: 0 10px 10px 0; padding: -0.25rem 1rem; border: 2px solid #e9ecef; font-size: 14px;">
                                <option value="">-- Select Initial Status --</option>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                    ✅ Active
                                </option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                    ⏸️ Inactive
                                </option>
                            </select>
                        </div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle mr-1"></i>Choose the initial operational status
                        </small>
                        @error('status')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <!-- Address Information Section -->
                <div class="col-md-6">
                    <div class="section-divider">
                        <h6 class="mb-0 font-weight-bold text-dark">
                            <i class="fas fa-map-marker-alt mr-2 text-danger"></i>Address Information
                        </h6>
                    </div>

                    <div class="form-group">
                        <label for="address" class="form-label-enhanced">Street Address</label>
                        <textarea class="form-control form-control-enhanced @error('address') is-invalid @enderror"
                                  id="address" name="address" rows="3" 
                                  placeholder="Enter complete street address">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="city" class="form-label-enhanced">City</label>
                                <div class="input-icon-group">
                                    <i class="fas fa-city input-icon"></i>
                                    <input type="text" class="form-control form-control-enhanced input-with-icon @error('city') is-invalid @enderror"
                                           id="city" name="city" value="{{ old('city') }}" 
                                           placeholder="Mumbai, Delhi">
                                </div>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="state" class="form-label-enhanced">State</label>
                                <div class="input-icon-group">
                                    <i class="fas fa-map input-icon"></i>
                                    <input type="text" class="form-control form-control-enhanced input-with-icon @error('state') is-invalid @enderror"
                                           id="state" name="state" value="{{ old('state') }}" 
                                           placeholder="Maharashtra, Delhi">
                                </div>
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pincode" class="form-label-enhanced">Pincode</label>
                        <div class="input-icon-group">
                            <i class="fas fa-mail-bulk input-icon"></i>
                            <input type="text" class="form-control form-control-enhanced input-with-icon @error('pincode') is-invalid @enderror"
                                   id="pincode" name="pincode" value="{{ old('pincode') }}" 
                                   placeholder="400001">
                        </div>
                        @error('pincode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="established_date" class="form-label-enhanced">Established Date</label>
                        <div class="input-icon-group">
                            <i class="fas fa-calendar-alt input-icon"></i>
                            <input type="date" class="form-control form-control-enhanced input-with-icon @error('established_date') is-invalid @enderror"
                                   id="established_date" name="established_date" value="{{ old('established_date') }}">
                        </div>
                        @error('established_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Add this field to your Address Information section -->
                    <div class="form-group">
                        <label for="contact_person" class="form-label-enhanced">Contact Person</label>
                        <div class="input-icon-group">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" class="form-control form-control-enhanced input-with-icon @error('contact_person') is-invalid @enderror"
                                id="contact_person" name="contact_person" value="{{ old('contact_person') }}" 
                                placeholder="Primary contact person name">
                        </div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle mr-1"></i>Name of the main contact person for this franchise
                        </small>
                        @error('contact_person')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="form-group">
                        <label for="notes" class="form-label-enhanced">Additional Notes</label>
                        <textarea class="form-control form-control-enhanced @error('notes') is-invalid @enderror"
                                  id="notes" name="notes" rows="3" 
                                  placeholder="Any special notes about this franchise">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Enhanced User Creation Section -->
            <div class="user-creation-section">
                <div class="d-flex align-items-center mb-3">
                    <div class="mr-3">
                        <div style="width: 50px; height: 50px; background: linear-gradient(45deg, #28a745, #20c997); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">
                            <i class="fas fa-user-plus"></i>
                        </div>
                    </div>
                    <div>
                        <h6 class="mb-1 font-weight-bold text-dark">
                            Create Franchise Owner Account
                        </h6>
                        <p class="mb-0 text-muted">Set up login access for franchise management</p>
                    </div>
                </div>

                <div class="custom-control custom-checkbox mb-4">
                    <input type="checkbox" class="custom-control-input" id="create_user" name="create_user"
                           value="1" {{ old('create_user', '1') ? 'checked' : '' }}>
                    <label class="custom-control-label font-weight-medium" for="create_user">
                        <span class="text-dark">Create login account for franchise owner</span>
                        <small class="text-muted d-block mt-1">
                            This will generate login credentials for accessing the franchise panel
                        </small>
                    </label>
                </div>

                <div id="user-fields" style="display: block;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user_name" class="form-label-enhanced required-marker">Owner Full Name</label>
                                <div class="input-icon-group">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" class="form-control form-control-enhanced input-with-icon @error('user_name') is-invalid @enderror"
                                           id="user_name" name="user_name" value="{{ old('user_name') }}" 
                                           placeholder="John Doe">
                                </div>
                                @error('user_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user_email" class="form-label-enhanced required-marker">Login Email</label>
                                <div class="input-icon-group">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input type="email" class="form-control form-control-enhanced input-with-icon @error('user_email') is-invalid @enderror"
                                           id="user_email" name="user_email" value="{{ old('user_email') }}" 
                                           placeholder="owner@franchise.com">
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-key mr-1"></i>This email will be used for franchise panel login
                                </small>
                                @error('user_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="info-alert">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <i class="fas fa-shield-alt" style="font-size: 24px;"></i>
                            </div>
                            <div>
                                <strong>🔐 Security Information</strong>
                                <div class="mt-1" style="opacity: 0.95;">
                                    A secure password will be auto-generated and displayed after creating the franchise. 
                                    Make sure to share these credentials securely with the franchise owner.
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
                        <a href="{{ route('admin.franchises.index') }}" class="btn btn-secondary btn-enhanced">
                            <i class="fas fa-arrow-left mr-2"></i>Back to List
                        </a>
                    </div>
                    <div class="d-flex">
                        <button type="button" class="btn btn-outline-warning btn-enhanced mr-3" onclick="resetForm()">
                            <i class="fas fa-redo mr-2"></i>Reset Form
                        </button>
                        <button type="submit" class="btn btn-success btn-enhanced">
                            <i class="fas fa-plus mr-2"></i>Create Franchise
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('js/admin/franchise-create.js') }}"></script>
@endsection

