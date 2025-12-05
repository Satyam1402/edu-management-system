{{-- resources/views/admin/franchises/edit.blade.php - USING YOUR WORKING PATTERNS --}}
@extends('layouts.custom-admin')

@section('title', 'Edit Franchise')
@section('page-title', 'Edit Franchise - ' . $franchise->name)

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/franchise-edit.css') }}">
@endsection

@section('content')
<div class="card form-card">
    <!-- Enhanced Header - Same pattern as your show blade -->
    <div class="form-header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-1 font-weight-bold">
                    <i class="fas fa-edit mr-2"></i>Edit Franchise Details
                </h4>
                <p class="mb-0" style="opacity: 0.9;">Update information for {{ $franchise->name }}</p>
            </div>
            <div class="text-right">
                <span class="badge badge-light px-3 py-2" style="font-size: 14px;">
                    <i class="fas fa-hashtag mr-1"></i>{{ $franchise->code }}
                </span>
            </div>
        </div>
    </div>

    <div class="card-body p-4">
        <!-- SIMPLE WORKING FORM - Same pattern as your create form -->
        <form action="{{ route('admin.franchises.update', $franchise) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Franchise Information Section -->
                <div class="col-md-6">
                    <div class="section-header">
                        <h6 class="mb-0 font-weight-bold text-dark">
                            <i class="fas fa-building mr-2 text-primary"></i>Franchise Information
                        </h6>
                    </div>

                    <div class="form-group">
                        <label for="name" class="form-label-enhanced required-marker">Franchise Name</label>
                        <input type="text" class="form-control form-control-enhanced @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $franchise->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="code" class="form-label-enhanced">Franchise Code</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-hashtag text-primary"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" id="code" name="code"
                                   value="{{ old('code', $franchise->code) }}" readonly
                                   style="background-color: #f8f9fa;">
                        </div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle mr-1"></i>Franchise code cannot be changed after creation
                        </small>
                        @error('code')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label-enhanced required-marker">Official Email</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-envelope text-primary"></i>
                                </span>
                            </div>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $franchise->email) }}" required>
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
                                   id="phone" name="phone" value="{{ old('phone', $franchise->phone) }}" required maxlength="10">
                        </div>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="contact_person" class="form-label-enhanced">Contact Person</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-user text-info"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control @error('contact_person') is-invalid @enderror"
                                   id="contact_person" name="contact_person"
                                   value="{{ old('contact_person', $franchise->contact_person) }}">
                        </div>
                        @error('contact_person')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- FIXED STATUS FIELD - Same pattern as your working create form -->
                 <div class="form-group">
                    <label>Status *</label>
                    <div class="mt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="status_active" value="active"
                                {{ $franchise->status == 'active' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="status_active">Active</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="status_inactive" value="inactive"
                                {{ $franchise->status == 'inactive' ? 'checked' : '' }}>
                            <label class="form-check-label" for="status_inactive">Inactive</label>
                        </div>
                        {{-- <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="status_suspended" value="suspended"
                                {{ $franchise->status == 'suspended' ? 'checked' : '' }}>
                            <label class="form-check-label" for="status_suspended">Suspended</label>
                        </div> --}}
                    </div>
                    @error('status')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                </div>

                <!-- Address Information Section -->
                <div class="col-md-6">
                    <div class="section-header">
                        <h6 class="mb-0 font-weight-bold text-dark">
                            <i class="fas fa-map-marker-alt mr-2 text-danger"></i>Address Information
                        </h6>
                    </div>

                    <div class="form-group">
                        <label for="address" class="form-label-enhanced">Street Address</label>
                        <textarea class="form-control form-control-enhanced @error('address') is-invalid @enderror"
                                  id="address" name="address" rows="3"
                                  placeholder="Enter full street address">{{ old('address', $franchise->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="city" class="form-label-enhanced">City</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-city text-info"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror"
                                           id="city" name="city" value="{{ old('city', $franchise->city) }}">
                                </div>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="state" class="form-label-enhanced">State</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-map text-warning"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control @error('state') is-invalid @enderror"
                                           id="state" name="state" value="{{ old('state', $franchise->state) }}">
                                </div>
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pincode" class="form-label-enhanced">Pincode</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-mail-bulk text-secondary"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control @error('pincode') is-invalid @enderror"
                                   id="pincode" name="pincode" value="{{ old('pincode', $franchise->pincode) }}">
                        </div>
                        @error('pincode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="established_date" class="form-label-enhanced">Established Date</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-calendar-alt text-info"></i>
                                </span>
                            </div>
                            <input type="date" class="form-control @error('established_date') is-invalid @enderror"
                                   id="established_date" name="established_date"
                                   value="{{ old('established_date', $franchise->established_date?->format('Y-m-d')) }}">
                        </div>
                        @error('established_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="notes" class="form-label-enhanced">Additional Notes</label>
                        <textarea class="form-control form-control-enhanced @error('notes') is-invalid @enderror"
                                  id="notes" name="notes" rows="3"
                                  placeholder="Any additional notes about this franchise">{{ old('notes', $franchise->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Enhanced Franchise Users Section -->
            @if($franchise->users->count() > 0)
            <hr class="my-4">
            <div class="mb-4">
                <div class="section-header">
                    <h6 class="mb-0 font-weight-bold text-dark">
                        <i class="fas fa-users mr-2 text-success"></i>Franchise Users
                        <span class="badge badge-success ml-2">{{ $franchise->users->count() }}</span>
                    </h6>
                </div>
                <div class="row">
                    @foreach($franchise->users as $user)
                        <div class="col-md-6 mb-3">
                            <div class="user-card">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="font-weight-bold text-dark">{{ $user->name }}</div>
                                        <div class="text-muted small mb-1">
                                            <i class="fas fa-envelope mr-1"></i>{{ $user->email }}
                                        </div>
                                        <div class="text-muted small">
                                            <i class="fas fa-calendar mr-1"></i>Created: {{ $user->created_at->format('M d, Y') }}
                                        </div>
                                    </div>
                                    <div>
                                        <span class="badge badge-success px-3 py-2" style="border-radius: 12px;">
                                            <i class="fas fa-check-circle mr-1"></i>Active
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Action Buttons - Same pattern as your working forms -->
            <div class="action-buttons">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('admin.franchises.index') }}" class="btn btn-secondary btn-custom">
                            <i class="fas fa-arrow-left mr-2"></i>Back to List
                        </a>
                        <a href="{{ route('admin.franchises.show', $franchise) }}" class="btn btn-info btn-custom ml-2">
                            <i class="fas fa-eye mr-2"></i>View Details
                        </a>
                    </div>
                    <div>
                        <button type="button" class="btn btn-warning btn-custom mr-2" onclick="resetForm()">
                            <i class="fas fa-undo mr-2"></i>Reset Changes
                        </button>
                        <button type="submit" class="btn btn-primary btn-custom">
                            <i class="fas fa-save mr-2"></i>Update Franchise
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('js/admin/franchise-edit.js') }}"></script>
@endsection
