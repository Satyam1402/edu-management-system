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
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-edit mr-2"></i>Course Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.courses.update', $course) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
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

                        <!-- Row for Fee, Duration, Level -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fee">Fee (₹) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('fee') is-invalid @enderror" 
                                           id="fee" name="fee" value="{{ old('fee', $course->fee) }}" 
                                           min="0" step="0.01" required>
                                    @error('fee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
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
                            <div class="col-md-4">
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
                        </div>

                        <!-- Category and Status -->
                        <div class="row">
                            <div class="col-md-6">
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="active" {{ old('status', $course->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $course->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="draft" {{ old('status', $course->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save mr-2"></i>Update Course
                            </button>
                            <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Course Info -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Course Details</h5>
                </div>
                <div class="card-body">
                    <p><strong>Created:</strong> {{ $course->created_at->format('M d, Y') }}</p>
                    <p><strong>Updated:</strong> {{ $course->updated_at->diffForHumans() }}</p>
                    <p><strong>Students:</strong> {{ $course->students()->count() }}</p>
                    <p><strong>Featured:</strong> {{ $course->is_featured ? 'Yes' : 'No' }}</p>
                    
                    <hr>
                    
                    <!-- Quick Actions -->
                    <div class="form-group">
                        <label>
                            <input type="checkbox" {{ $course->is_featured ? 'checked' : '' }} 
                                   onchange="toggleFeatured({{ $course->id }})"> 
                            Featured Course
                        </label>
                    </div>
                    
                    <button class="btn btn-danger btn-sm btn-block" onclick="deleteCourse({{ $course->id }})">
                        <i class="fas fa-trash mr-2"></i>Delete Course
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('js/admin/courses/edit.js') }}"></script>
@endsection
