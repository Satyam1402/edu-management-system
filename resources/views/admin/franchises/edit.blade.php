{{-- resources/views/admin/franchises/edit.blade.php --}}
@extends('layouts.custom-admin')

@section('title', 'Edit Franchise')
@section('page-title', 'Edit Franchise - ' . $franchise->name)

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent">
        <h5 class="card-title mb-0">
            <i class="fas fa-edit text-primary me-2"></i>Edit Franchise Details
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.franchises.update', $franchise) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Franchise Information -->
                <div class="col-md-6">
                    <h6 class="mb-3 text-muted">
                        <i class="fas fa-building me-2"></i>Franchise Information
                    </h6>

                    <div class="mb-3">
                        <label for="name" class="form-label">Franchise Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $franchise->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label">Franchise Code *</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror"
                               id="code" name="code" value="{{ old('code', $franchise->code) }}" required readonly>
                        <small class="text-muted">Franchise code cannot be changed after creation</small>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Official Email *</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email', $franchise->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number *</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                               id="phone" name="phone" value="{{ old('phone', $franchise->phone) }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="contact_person" class="form-label">Contact Person</label>
                        <input type="text" class="form-control @error('contact_person') is-invalid @enderror"
                               id="contact_person" name="contact_person" value="{{ old('contact_person', $franchise->contact_person) }}">
                        @error('contact_person')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="active" {{ old('status', $franchise->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $franchise->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ old('status', $franchise->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
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
                                  id="address" name="address" rows="3">{{ old('address', $franchise->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror"
                                   id="city" name="city" value="{{ old('city', $franchise->city) }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="state" class="form-label">State</label>
                            <input type="text" class="form-control @error('state') is-invalid @enderror"
                                   id="state" name="state" value="{{ old('state', $franchise->state) }}">
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="pincode" class="form-label">Pincode</label>
                        <input type="text" class="form-control @error('pincode') is-invalid @enderror"
                               id="pincode" name="pincode" value="{{ old('pincode', $franchise->pincode) }}">
                        @error('pincode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="established_date" class="form-label">Established Date</label>
                        <input type="date" class="form-control @error('established_date') is-invalid @enderror"
                               id="established_date" name="established_date" value="{{ old('established_date', $franchise->established_date?->format('Y-m-d')) }}">
                        @error('established_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes" name="notes" rows="3" placeholder="Any additional notes about this franchise">{{ old('notes', $franchise->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Franchise Users -->
            @if($franchise->users->count() > 0)
            <hr>
            <div class="mb-4">
                <h6 class="mb-3 text-muted">
                    <i class="fas fa-users me-2"></i>Franchise Users ({{ $franchise->users->count() }})
                </h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Created</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($franchise->users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td><span class="badge bg-success">Active</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.franchises.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Franchise
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
