@extends('layouts.custom-admin')

@section('title', 'Create Course')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/courses/create.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h3>Create New Course</h3>
                <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Courses
                </a>
            </div>
        </div>
    </div>

    <!-- Create Form -->
    <div class="row">
        <div class="col-md-8">
            <form action="{{ route('admin.courses.store') }}" method="POST">
                @csrf

                <!-- Course Information Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-plus mr-2"></i>Course Information</h5>
                    </div>
                    <div class="card-body">
                        <!-- Course Name -->
                        <div class="form-group">
                            <label for="name">Course Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Course Code -->
                        <div class="form-group">
                            <label for="code">Course Code</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                   id="code" name="code" value="{{ old('code') }}"
                                   placeholder="Leave empty for auto-generation">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="description">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Row for Duration, Level, Category -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="duration_months">Duration (Months) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('duration_months') is-invalid @enderror"
                                           id="duration_months" name="duration_months" value="{{ old('duration_months') }}"
                                           min="1" required>
                                    @error('duration_months')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="level">Level</label>
                                    <select class="form-control @error('level') is-invalid @enderror" id="level" name="level">
                                        <option value="">Select Level</option>
                                        <option value="beginner" {{ old('level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                        <option value="intermediate" {{ old('level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                        <option value="advanced" {{ old('level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                    </select>
                                    @error('level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="category">Category</label>
                                    <select class="form-control @error('category') is-invalid @enderror" id="category" name="category">
                                        <option value="">Select Category</option>
                                        <option value="technology" {{ old('category') == 'technology' ? 'selected' : '' }}>Technology</option>
                                        <option value="business" {{ old('category') == 'business' ? 'selected' : '' }}>Business</option>
                                        <option value="design" {{ old('category') == 'design' ? 'selected' : '' }}>Design</option>
                                        <option value="marketing" {{ old('category') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                        <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Enhanced Pricing Configuration Section --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-money-bill-wave mr-2"></i>Pricing Configuration</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Regular Fee <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">₹</span>
                                        </div>
                                        <input type="number" step="0.01" name="fee"
                                               class="form-control @error('fee') is-invalid @enderror"
                                               id="regularFee" value="{{ old('fee') }}" required>
                                    </div>
                                    @error('fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="form-text text-muted">Base course fee</small>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Discount Fee</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">₹</span>
                                        </div>
                                        <input type="number" step="0.01" name="discount_fee"
                                               class="form-control @error('discount_fee') is-invalid @enderror"
                                               id="discountFee" value="{{ old('discount_fee') }}">
                                    </div>
                                    @error('discount_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="form-text text-muted">Must be less than regular fee</small>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Franchise Fee</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">₹</span>
                                        </div>
                                        <input type="number" step="0.01" name="franchise_fee"
                                               class="form-control @error('franchise_fee') is-invalid @enderror"
                                               id="franchiseFee" value="{{ old('franchise_fee') }}">
                                    </div>
                                    @error('franchise_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="form-text text-muted">Special pricing for franchises</small>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Course Type</label>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="is_free" value="1"
                                               class="form-check-input @error('is_free') is-invalid @enderror"
                                               id="isFree" {{ old('is_free') ? 'checked' : '' }}>
                                        <label class="form-check-label font-weight-bold text-success" for="isFree">
                                            <i class="fas fa-gift mr-1"></i>Free Course
                                        </label>
                                    </div>
                                    @error('is_free')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="form-text text-muted">Override all pricing</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-weight-bold">Fee Notes</label>
                                    <textarea name="fee_notes" class="form-control @error('fee_notes') is-invalid @enderror" rows="2"
                                              placeholder="Additional notes about pricing, payment terms, installments, etc.">{{ old('fee_notes') }}</textarea>
                                    @error('fee_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- Pricing Preview --}}
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="alert alert-light border">
                                    <h6><i class="fas fa-calculator mr-2"></i>Pricing Preview</h6>
                                    <div id="pricingPreview">
                                        <span class="text-muted">Enter prices above to see preview</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add this right before the Submit Buttons card -->
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-cog mr-2"></i>Course Settings</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="status" class="font-weight-bold">Course Status</label>
                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold">Featured Course</label>
                    <div class="form-check mt-2">
                        <input type="checkbox" name="is_featured" value="1"
                               class="form-check-input @error('is_featured') is-invalid @enderror"
                               id="isFeatured" {{ old('is_featured') ? 'checked' : '' }}>
                        <label class="form-check-label font-weight-bold text-warning" for="isFeatured">
                            <i class="fas fa-star mr-1"></i>Mark as Featured
                        </label>
                    </div>
                    @error('is_featured')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>
</div>


                <!-- Submit Buttons -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save mr-2"></i>Create Course
                        </button>
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary btn-lg ml-2">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Quick Info</h5>
                </div>
                <div class="card-body">
                    <p><small>Fill out the form to create a new course. Required fields are marked with *</small></p>
                    <hr>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success mr-2"></i>Course code auto-generated</li>
                        <li><i class="fas fa-check text-success mr-2"></i>Status set to draft by default</li>
                        <li><i class="fas fa-check text-success mr-2"></i>Can be edited after creation</li>
                        <li><i class="fas fa-check text-success mr-2"></i><strong>New:</strong> Advanced pricing options</li>
                        <li><i class="fas fa-check text-success mr-2"></i><strong>New:</strong> Franchise-specific pricing</li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-lightbulb mr-2"></i>Pricing Tips</h6>
                </div>
                <div class="card-body">
                    <small>
                        <ul class="mb-0">
                            <li><strong>Regular Fee:</strong> Base course price</li>
                            <li><strong>Discount Fee:</strong> Promotional pricing</li>
                            <li><strong>Franchise Fee:</strong> Special rates for franchise partners</li>
                            <li><strong>Free Course:</strong> Overrides all pricing</li>
                        </ul>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript for Pricing Preview --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const feeInput = document.getElementById('regularFee');
    const discountFeeInput = document.getElementById('discountFee');
    const franchiseFeeInput = document.getElementById('franchiseFee');
    const isFreeInput = document.getElementById('isFree');
    const pricingPreview = document.getElementById('pricingPreview');

    function updatePricingPreview() {
        const fee = parseFloat(feeInput.value) || 0;
        const discountFee = parseFloat(discountFeeInput.value) || null;
        const franchiseFee = parseFloat(franchiseFeeInput.value) || null;
        const isFree = isFreeInput.checked;

        let html = '';

        if (isFree) {
            html = '<span class="badge badge-success px-3 py-2 font-weight-bold">FREE COURSE</span>';
        } else if (fee > 0) {
            html += `<strong>Regular Price:</strong> <span class="text-primary">₹${fee.toFixed(2)}</span>`;

            if (discountFee && discountFee < fee) {
                const discount = ((fee - discountFee) / fee * 100).toFixed(0);
                html += `<br><strong>Discounted Price:</strong> <span class="text-success font-weight-bold">₹${discountFee.toFixed(2)} (${discount}% off)</span>`;
            }

            if (franchiseFee) {
                html += `<br><strong>Franchise Price:</strong> <span class="text-info font-weight-bold">₹${franchiseFee.toFixed(2)}</span>`;
            }
        } else {
            html = '<span class="text-muted">Enter prices above to see preview</span>';
        }

        pricingPreview.innerHTML = html;
    }

    // Add event listeners
    [feeInput, discountFeeInput, franchiseFeeInput, isFreeInput].forEach(input => {
        if (input) {
            input.addEventListener('input', updatePricingPreview);
            input.addEventListener('change', updatePricingPreview);
        }
    });

    // Initial preview
    updatePricingPreview();

    // Free course toggle
    isFreeInput.addEventListener('change', function() {
        const priceInputs = [feeInput, discountFeeInput, franchiseFeeInput];
        if (this.checked) {
            priceInputs.forEach(input => {
                input.disabled = true;
                input.style.backgroundColor = '#f8f9fa';
            });
        } else {
            priceInputs.forEach(input => {
                input.disabled = false;
                input.style.backgroundColor = '';
            });
        }
    });
});
</script>
@endsection
