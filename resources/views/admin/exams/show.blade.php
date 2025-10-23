@extends('layouts.custom-admin')

@section('title', $exam->title)

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/exams/show.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <!-- Exam Header -->
    <div class="exam-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1>{{ $exam->title }}</h1>
                @if($exam->description)
                    <p class="lead">{{ $exam->description }}</p>
                @endif
                <div>
                    <span class="badge badge-light mr-2">{{ $exam->course->name ?? 'No Course' }}</span>
                    <span class="badge badge-{{ $exam->status == 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($exam->status) }}
                    </span>
                    @if($exam->exam_date)
                        <span class="badge badge-info ml-2">
                            <i class="fas fa-calendar mr-1"></i>{{ $exam->exam_date->format('M d, Y') }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-light">
                    <i class="fas fa-edit mr-2"></i>Edit Exam
                </a>
                <a href="{{ route('admin.exams.index') }}" class="btn btn-outline-light ml-2">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">{{ $exam->exam_attempts()->count() }}</div>
                <div class="text-muted">Total Attempts</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">{{ $exam->total_marks }}</div>
                <div class="text-muted">Max Marks</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">{{ $exam->duration_minutes }}</div>
                <div class="text-muted">Minutes</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">{{ $exam->exam_attempts()->where('result', 'pass')->count() }}</div>
                <div class="text-muted">Passed</div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-md-8">
            <!-- Exam Info -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Exam Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Course:</strong></td>
                            <td>{{ $exam->course->name ?? 'Not assigned' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Exam Date:</strong></td>
                            <td>{{ $exam->exam_date ? $exam->exam_date->format('M d, Y') : 'Not scheduled' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Start Time:</strong></td>
                            <td>{{ $exam->start_time ? $exam->start_time->format('g:i A') : 'Not set' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Duration:</strong></td>
                            <td>{{ $exam->duration_text }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total Marks:</strong></td>
                            <td>{{ $exam->total_marks }}</td>
                        </tr>
                        <tr>
                            <td><strong>Mode:</strong></td>
                            <td>{{ ucfirst($exam->mode ?? 'offline') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <span class="badge badge-{{ $exam->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($exam->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Created:</strong></td>
                            <td>{{ $exam->created_at->format('M d, Y g:i A') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Updated:</strong></td>
                            <td>{{ $exam->updated_at->format('M d, Y g:i A') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Student Attempts -->
            @if($exam->exam_attempts->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Student Attempts ({{ $exam->exam_attempts->count() }})</h5>
                </div>
                <div class="card-body">
                    @foreach($exam->exam_attempts->take(10) as $attempt)
                    <div class="attempt-item">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center mr-3" 
                                         style="width: 40px; height: 40px; font-size: 14px; font-weight: bold;">
                                        {{ strtoupper(substr($attempt->student->name ?? 'N/A', 0, 2)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $attempt->student->name ?? 'Unknown Student' }}</strong><br>
                                        <small class="text-muted">{{ $attempt->student->email ?? 'No email' }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 text-center">
                                <strong>{{ $attempt->score ?? 0 }}/{{ $attempt->total_marks }}</strong><br>
                                <small class="text-muted">{{ $attempt->percentage }}%</small>
                            </div>
                            <div class="col-md-2 text-center">
                                @if($attempt->result)
                                    <span class="badge result-badge badge-{{ $attempt->result == 'pass' ? 'success' : ($attempt->result == 'fail' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($attempt->result) }}
                                    </span>
                                @else
                                    <span class="badge result-badge badge-secondary">Pending</span>
                                @endif
                            </div>
                            <div class="col-md-2 text-center">
                                @if($attempt->started_at)
                                    <small>{{ $attempt->started_at->format('M d') }}</small>
                                @else
                                    <small class="text-muted">Not started</small>
                                @endif
                            </div>
                            <div class="col-md-2 text-center">
                                @if($attempt->completed_at)
                                    <small class="text-success">Completed</small>
                                @elseif($attempt->started_at)
                                    <small class="text-warning">In Progress</small>
                                @else
                                    <small class="text-muted">Not started</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                    
                    @if($exam->exam_attempts->count() > 10)
                        <p class="text-muted text-center mt-3">Showing 10 of {{ $exam->exam_attempts->count() }} attempts</p>
                    @endif
                </div>
            </div>
            @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Attempts Yet</h5>
                    <p class="text-muted">Students haven't taken this exam yet.</p>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-warning btn-block mb-2">
                        <i class="fas fa-edit mr-2"></i>Edit Exam
                    </a>
                    @if($exam->course)
                    <a href="{{ route('admin.courses.show', $exam->course) }}" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-book mr-2"></i>View Course
                    </a>
                    @endif
                    <button class="btn btn-danger btn-block" onclick="deleteExam({{ $exam->id }})">
                        <i class="fas fa-trash mr-2"></i>Delete Exam
                    </button>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Statistics</h5>
                </div>
                <div class="card-body">
                    @php
                        $totalAttempts = $exam->exam_attempts->count();
                        $passedAttempts = $exam->exam_attempts->where('result', 'pass')->count();
                        $failedAttempts = $exam->exam_attempts->where('result', 'fail')->count();
                        $pendingAttempts = $exam->exam_attempts->whereNull('result')->count();
                        
                        $passRate = $totalAttempts > 0 ? round(($passedAttempts / $totalAttempts) * 100, 1) : 0;
                        $avgScore = $exam->exam_attempts->whereNotNull('score')->avg('score');
                        $avgScore = $avgScore ? round($avgScore, 1) : 0;
                    @endphp
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Pass Rate</small>
                        <div class="progress mb-1" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $passRate }}%"></div>
                        </div>
                        <small>{{ $passRate }}% ({{ $passedAttempts }}/{{ $totalAttempts }})</small>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Average Score</small>
                        <h6 class="mb-0">{{ $avgScore }}/{{ $exam->total_marks }}</h6>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <small class="text-success">✓ Passed: {{ $passedAttempts }}</small>
                        </div>
                        <div class="col-6">
                            <small class="text-danger">✗ Failed: {{ $failedAttempts }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
function deleteExam(examId) {
    if (!confirm('Are you sure you want to delete this exam? This will also delete all student attempts.')) return;
    
    $.ajax({
        url: `/admin/exams/${examId}`,
        type: 'DELETE',
        data: { _token: '{{ csrf_token() }}' },
        success: function() {
            alert('Exam deleted successfully!');
            window.location.href = '{{ route("admin.exams.index") }}';
        },
        error: function() {
            alert('Error deleting exam');
        }
    });
}
</script>
@endsection
