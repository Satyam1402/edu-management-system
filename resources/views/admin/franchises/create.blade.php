{{-- resources/views/admin/franchises/create.blade.php --}}
@extends('layouts.custom-admin')

@section('title', 'Create Franchise')
@section('page-title', 'Create New Franchise')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent">
        <h5 class="card-title mb-0">
            <i class="fas fa-building text-primary me-2"></i>Create New Franchise
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.franchises.store') }}" method="POST">
            @csrf

            <div class="row">
                <!-- Franchise Information -->
                <div class="col-md-6">
                    <h6 class="mb-3 text-muted">
                        <i class="fas fa-building me-2"></i>Franchise Information
                    </h6>

                    <div class="mb-3">
                        <label for="name" class="form-label">Franchise Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label">Franchise Code *</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror"
                               id="code" name="code" value="{{ old('code') }}" placeholder="e.g., FR001" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Official Email *</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number *</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                               id="phone" name="phone" value="{{ old('phone') }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Address Information -->
                <div class="col-md-6">
                    <h6 class="mb-3 text-muted">
                        <i class="fas fa-map-marker-alt me-2"></i>Address Information
                    </h6>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror"
                                  id="address" name="address" rows="3">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror"
                                   id="city" name="city" value="{{ old('city') }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="state" class="form-label">State</label>
                            <input type="text" class="form-control @error('state') is-invalid @enderror"
                                   id="state" name="state" value="{{ old('state') }}">
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="pincode" class="form-label">Pincode</label>
                        <input type="text" class="form-control @error('pincode') is-invalid @enderror"
                               id="pincode" name="pincode" value="{{ old('pincode') }}">
                        @error('pincode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <hr>

            <!-- Create User Section -->
            <div class="mb-4">
                <h6 class="mb-3 text-muted">
                    <i class="fas fa-user-plus me-2"></i>Create Franchise Owner Account
                </h6>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="create_user" name="create_user"
                           value="1" {{ old('create_user') ? 'checked' : 'checked' }}>
                    <label class="form-check-label" for="create_user">
                        <strong>Create login account for franchise owner</strong>
                        <small class="text-muted d-block">This will create a user account that can access the franchise panel</small>
                    </label>
                </div>

                <div id="user-fields" style="display: block;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="user_name" class="form-label">Owner Full Name *</label>
                            <input type="text" class="form-control @error('user_name') is-invalid @enderror"
                                   id="user_name" name="user_name" value="{{ old('user_name') }}">
                            @error('user_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="user_email" class="form-label">Login Email *</label>
                            <input type="email" class="form-control @error('user_email') is-invalid @enderror"
                                   id="user_email" name="user_email" value="{{ old('user_email') }}">
                            <small class="text-muted">This email will be used to login to franchise panel</small>
                            @error('user_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Password will be auto-generated</strong> and displayed after creating the franchise.
                        Make sure to share these credentials with the franchise owner.
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.franchises.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Create Franchise
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    document.getElementById('create_user').addEventListener('change', function() {
        const userFields = document.getElementById('user-fields');
        if (this.checked) {
            userFields.style.display = 'block';
            document.getElementById('user_name').required = true;
            document.getElementById('user_email').required = true;
        } else {
            userFields.style.display = 'none';
            document.getElementById('user_name').required = false;
            document.getElementById('user_email').required = false;
        }
    });
</script>
@endsection
