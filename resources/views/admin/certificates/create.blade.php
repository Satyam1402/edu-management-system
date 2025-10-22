@extends('layouts.custom-admin')

@section('title', 'Create Certificate')
@section('page-title', 'Create New Certificate')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Certificate Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.certificates.store') }}" method="POST">
                        @csrf

                        <!-- Student Selection -->
                        <div class="form-group">
                            <label for="student_id" class="required-label">Student</label>
                            <select name="student_id" id="student_id" class="form-control @error('student_id') is-invalid @enderror" required>
                                <option value="">Select Student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" 
                                            data-course="{{ $student->course_id }}"
                                            {{ old('student_id', $selectedStudent?->id) == $student->id ? 'selected' : '' }}>
                                        {{ $student->name }} ({{ $student->student_id }}) - {{ $student->franchise->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Course Selection -->
                        <div class="form-group">
                            <label for="course_id" class="required-label">Course</label>
                            <select name="course_id" id="course_id" class="form-control @error('course_id') is-invalid @enderror" required>
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id', $selectedStudent?->course_id) == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }} ({{ $course->duration }})
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label for="status" class="required-label">Status</label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                <option value="requested" {{ old('status') == 'requested' ? 'selected' : '' }}>Requested</option>
                                <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="issued" {{ old('status') == 'issued' ? 'selected' : '' }}>Issued</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Actions -->
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-certificate mr-2"></i>Create Certificate
                            </button>
                            <a href="{{ route('admin.certificates.index') }}" class="btn btn-secondary ml-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-populate course when student is selected
$('#student_id').change(function() {
    const selectedOption = $(this).find('option:selected');
    const courseId = selectedOption.data('course');
    
    if (courseId) {
        $('#course_id').val(courseId);
    }
});
</script>
@endpush
@endsection
