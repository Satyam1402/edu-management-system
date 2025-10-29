@extends('layouts.custom-admin')

@section('title', 'Enroll Students')
@section('page-title', 'Enroll Students')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-user-plus mr-2"></i>Enroll Students in {{ $course->name }}</h5>
        </div>
        <div class="card-body">
            @if($availableStudents->count() > 0)
                <form action="{{ route('franchise.courses.enroll', $course) }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Select Students *</label>
                                <div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 5px;">
                                    @foreach($availableStudents as $student)
                                        <div class="form-check mb-2">
                                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                                   class="form-check-input" id="student-{{ $student->id }}"
                                                   {{ in_array($student->id, old('student_ids', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="student-{{ $student->id }}">
                                                <strong>{{ $student->name }}</strong><br>
                                                <small class="text-muted">{{ $student->email }}</small>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('student_ids')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Enrollment Date *</label>
                                <input type="date" name="enrollment_date" class="form-control"
                                       value="{{ old('enrollment_date', date('Y-m-d')) }}" required>
                                @error('enrollment_date')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Notes (Optional)</label>
                                <textarea name="notes" class="form-control" rows="4"
                                          placeholder="Add any notes about this enrollment...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save mr-1"></i> Enroll Selected Students
                        </button>
                        <a href="{{ route('franchise.courses.show', $course) }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times mr-1"></i> Cancel
                        </a>
                    </div>
                </form>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                    <h5>No Available Students</h5>
                    <p class="text-muted">All active students are already enrolled in this course.</p>
                    <a href="{{ route('franchise.courses.show', $course) }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Course
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
