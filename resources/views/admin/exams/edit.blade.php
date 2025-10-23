@extends('layouts.custom-admin')

@section('title', 'Edit Exam')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/exams/create.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h3>Edit Exam: {{ $exam->title }}</h3>
                <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Exams
                </a>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-edit mr-2"></i>Exam Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.exams.update', $exam) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Exam Title -->
                        <div class="form-group">
                            <label for="title">Exam Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $exam->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Course -->
                        <div class="form-group">
                            <label for="course_id">Course <span class="text-danger">*</span></label>
                            <select class="form-control @error('course_id') is-invalid @enderror" 
                                    id="course_id" name="course_id" required>
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id', $exam->course_id) == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $exam->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Row for Date, Time, Duration -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="exam_date">Exam Date</label>
                                    <input type="date" class="form-control @error('exam_date') is-invalid @enderror" 
                                           id="exam_date" name="exam_date" 
                                           value="{{ old('exam_date', $exam->exam_date ? $exam->exam_date->format('Y-m-d') : '') }}">
                                    @error('exam_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_time">Start Time</label>
                                    <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                           id="start_time" name="start_time" 
                                           value="{{ old('start_time', $exam->start_time ? $exam->start_time->format('H:i') : '') }}">
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="duration_minutes">Duration (Minutes) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror" 
                                           id="duration_minutes" name="duration_minutes" 
                                           value="{{ old('duration_minutes', $exam->duration_minutes) }}" 
                                           min="30" required>
                                    @error('duration_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Row for Marks and Status -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="total_marks">Total Marks <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('total_marks') is-invalid @enderror" 
                                           id="total_marks" name="total_marks" 
                                           value="{{ old('total_marks', $exam->total_marks) }}" 
                                           min="1" required>
                                    @error('total_marks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="active" {{ old('status', $exam->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $exam->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save mr-2"></i>Update Exam
                            </button>
                            <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Exam Info -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Exam Details</h5>
                </div>
                <div class="card-body">
                    <p><strong>Created:</strong> {{ $exam->created_at->format('M d, Y') }}</p>
                    <p><strong>Updated:</strong> {{ $exam->updated_at->diffForHumans() }}</p>
                    <p><strong>Course:</strong> {{ $exam->course->name ?? 'N/A' }}</p>
                    <p><strong>Attempts:</strong> {{ $exam->exam_attempts()->count() }}</p>
                    
                    <hr>
                    
                    <!-- Quick Actions -->
                    <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-info btn-sm btn-block mb-2">
                        <i class="fas fa-eye mr-2"></i>View Details
                    </a>
                    
                    <button class="btn btn-danger btn-sm btn-block" onclick="deleteExam({{ $exam->id }})">
                        <i class="fas fa-trash mr-2"></i>Delete Exam
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
function deleteExam(examId) {
    if (!confirm('Are you sure you want to delete this exam?')) return;
    
    $.ajax({
        url: `/admin/exams/${examId}`,
        type: 'DELETE',
        data: { _token: '{{ csrf_token() }}' },
        success: function() {
            alert('Exam deleted!');
            window.location.href = '{{ route("admin.exams.index") }}';
        },
        error: function() {
            alert('Error deleting exam');
        }
    });
}

// Fix dropdown placeholder display
$(document).ready(function() {
    $('select.form-control').each(function() {
        updateSelectColor(this);
    });
    
    $('select.form-control').on('change', function() {
        updateSelectColor(this);
    });
});

function updateSelectColor(selectElement) {
    if ($(selectElement).val() === '' || $(selectElement).val() === null) {
        $(selectElement).css('color', '#6c757d').css('font-style', 'italic');
    } else {
        $(selectElement).css('color', '#495057').css('font-style', 'normal');
    }
}
</script>
@endsection
