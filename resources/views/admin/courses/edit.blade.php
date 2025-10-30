@extends('layouts.custom-admin')

@section('title', 'Edit Course')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/courses/edit.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h3>Edit Course: {{ $course->name }}</h3>
                <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Courses
                </a>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-md-8">
            <form action="{{ route('admin.courses.update', $course) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Course Information Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-edit mr-2"></i>Course Information</h5>
                    </div>
                    <div class="card-body">
                        <!-- Course Name -->
                        <div class="form-group">
                            <label for="name">Course Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $course->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Course Code -->
                        <div class="form-group">
                            <label for="code">Course Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                   id="code" name="code" value="{{ old('code', $course->code) }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Course code should be unique and descriptive</small>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="description">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3" required>{{ old('description', $course->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Row for Duration, Level, Category, Status -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="duration_months">Duration (Months) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('duration_months') is-invalid @enderror"
                                           id="duration_months" name="duration_months" value="{{ old('duration_months', $course->duration_months) }}"
                                           min="1" required>
                                    @error('duration_months')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="level">Level</label>
                                    <select class="form-control @error('level') is-invalid @enderror" id="level" name="level">
                                        <option value="">Select Level</option>
                                        <option value="beginner" {{ old('level', $course->level) == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                        <option value="intermediate" {{ old('level', $course->level) == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                        <option value="advanced" {{ old('level', $course->level) == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                    </select>
                                    @error('level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="category">Category</label>
                                    <select class="form-control @error('category') is-invalid @enderror" id="category" name="category">
                                        <option value="">Select Category</option>
                                        <option value="technology" {{ old('category', $course->category) == 'technology' ? 'selected' : '' }}>Technology</option>
                                        <option value="business" {{ old('category', $course->category) == 'business' ? 'selected' : '' }}>Business</option>
                                        <option value="design" {{ old('category', $course->category) == 'design' ? 'selected' : '' }}>Design</option>
                                        <option value="marketing" {{ old('category', $course->category) == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                        <option value="other" {{ old('category', $course->category) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="active" {{ old('status', $course->status) == 'active' ? 'selected' : '' }}>
                                            🟢 Active
                                        </option>
                                        <option value="inactive" {{ old('status', $course->status) == 'inactive' ? 'selected' : '' }}>
                                            🔴 Inactive
                                        </option>
                                        {{-- 🔧 REMOVED DRAFT OPTION --}}
                                    </select>
                                    @error('status')
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
                                               id="regularFee" value="{{ old('fee', $course->fee) }}" required>
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
                                               id="discountFee" value="{{ old('discount_fee', $course->discount_fee) }}">
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
                                               id="franchiseFee" value="{{ old('franchise_fee', $course->franchise_fee) }}">
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
                                               id="isFree" {{ old('is_free', $course->is_free) ? 'checked' : '' }}>
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
                                              placeholder="Additional notes about pricing, payment terms, installments, etc.">{{ old('fee_notes', $course->fee_notes) }}</textarea>
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
                                        {{-- Server-side preview --}}
                                        @if($course->is_free)
                                            <span class="badge badge-success px-3 py-2 font-weight-bold">FREE COURSE</span>
                                        @elseif($course->fee)
                                            <strong>Regular Price:</strong> <span class="text-primary">₹{{ number_format($course->fee, 2) }}</span>
                                            @if($course->discount_fee && $course->discount_fee < $course->fee)
                                                <br><strong>Discounted Price:</strong> <span class="text-success font-weight-bold">₹{{ number_format($course->discount_fee, 2) }} ({{ round((($course->fee - $course->discount_fee) / $course->fee) * 100) }}% off)</span>
                                            @endif
                                            @if($course->franchise_fee)
                                                <br><strong>Franchise Price:</strong> <span class="text-info font-weight-bold">₹{{ number_format($course->franchise_fee, 2) }}</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Enter prices above to see preview</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 🔧 NEW: Course Settings Section --}}
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-cog mr-2"></i>Course Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Featured Course</label>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="is_featured" value="1"
                                               class="form-check-input @error('is_featured') is-invalid @enderror"
                                               id="isFeatured" {{ old('is_featured', $course->is_featured) ? 'checked' : '' }}>
                                        <label class="form-check-label font-weight-bold text-warning" for="isFeatured">
                                            <i class="fas fa-star mr-1"></i>Mark as Featured Course
                                        </label>
                                    </div>
                                    @error('is_featured')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="form-text text-muted">Featured courses appear prominently in listings</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_students" class="font-weight-bold">Max Students</label>
                                    <input type="number" class="form-control @error('max_students') is-invalid @enderror"
                                           id="max_students" name="max_students" value="{{ old('max_students', $course->max_students) }}"
                                           min="1" placeholder="Leave empty for unlimited">
                                    @error('max_students')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Maximum enrollment limit (optional)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save mr-2"></i>Update Course
                        </button>
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary btn-lg ml-2">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-info btn-lg ml-2">
                            <i class="fas fa-eye mr-2"></i>View Course
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-md-4">
            <!-- Current Course Stats -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-chart-bar mr-2"></i>Course Stats</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-primary">{{ $course->students()->count() }}</h4>
                            <small>Students</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">₹{{ number_format($course->effective_fee) }}</h4>
                            <small>Current Price</small>
                        </div>
                    </div>
                    <hr>
                    <p><strong>Created:</strong> {{ $course->created_at->format('M d, Y') }}</p>
                    <p><strong>Updated:</strong> {{ $course->updated_at->diffForHumans() }}</p>
                    <p><strong>Status:</strong>
                        <span class="badge badge-{{ $course->status == 'active' ? 'success' : 'secondary' }}">
                            {{ $course->status == 'active' ? '🟢 Active' : '🔴 Inactive' }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- Current Pricing Info -->
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-tag mr-2"></i>Current Pricing</h6>
                </div>
                <div class="card-body">
                    @if($course->is_free)
                        <div class="alert alert-success mb-2">
                            <i class="fas fa-gift mr-2"></i><strong>FREE COURSE</strong>
                        </div>
                    @else
                        <div class="mb-2">
                            <strong>Regular Fee:</strong> <span class="text-primary">₹{{ number_format($course->fee, 2) }}</span>
                        </div>
                        @if($course->discount_fee)
                            <div class="mb-2">
                                <strong>Discount Fee:</strong> <span class="text-success">₹{{ number_format($course->discount_fee, 2) }}</span>
                                <small class="text-muted">({{ round((($course->fee - $course->discount_fee) / $course->fee) * 100) }}% off)</small>
                            </div>
                        @endif
                        @if($course->franchise_fee)
                            <div class="mb-2">
                                <strong>Franchise Fee:</strong> <span class="text-info">₹{{ number_format($course->franchise_fee, 2) }}</span>
                            </div>
                        @endif
                        @if($course->fee_notes)
                            <div class="mt-2">
                                <small><strong>Notes:</strong> {{ $course->fee_notes }}</small>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-bolt mr-2"></i>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <button class="btn btn-warning btn-sm btn-block mb-2" onclick="toggleFeatured({{ $course->id }})">
                        <i class="fas fa-star mr-2"></i>
                        {{ $course->is_featured ? 'Remove Featured' : 'Mark Featured' }}
                    </button>
                    <button class="btn btn-danger btn-sm btn-block" onclick="deleteCourse({{ $course->id }})">
                        <i class="fas fa-trash mr-2"></i>Delete Course
                    </button>
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
        updatePricingPreview();
    });

    // Initial setup
    if (isFreeInput.checked) {
        const priceInputs = [feeInput, discountFeeInput, franchiseFeeInput];
        priceInputs.forEach(input => {
            input.disabled = true;
            input.style.backgroundColor = '#f8f9fa';
        });
    }
});

// Quick Actions Functions
function toggleFeatured(courseId) {
    if (!confirm('Toggle featured status for this course?')) return;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: `/admin/courses/${courseId}/toggle-featured`,
        type: 'POST',
        success: function(response) {
            location.reload();
        },
        error: function(xhr) {
            alert('Error updating course status');
        }
    });
}

function deleteCourse(courseId) {
    if (!confirm('⚠️ Are you sure you want to delete this course?\n\nThis action cannot be undone.')) return;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: `/admin/courses/${courseId}`,
        type: 'DELETE',
        success: function(response) {
            window.location.href = '{{ route("admin.courses.index") }}';
        },
        error: function(xhr) {
            alert('Error deleting course');
        }
    });
}
</script>
@endsection

@section('js')
<script src="{{ asset('js/admin/courses/edit.js') }}"></script>
@endsection
