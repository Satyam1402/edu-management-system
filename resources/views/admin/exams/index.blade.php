{{-- resources/views/admin/exams/index.blade.php --}}
@extends('layouts.custom-admin')

@section('title', 'Exams')
@section('page-title', 'Exam Management')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow" style="border: none; border-radius: 10px;">
                <div class="card-header" style="background: linear-gradient(45deg, #fd7e14, #e65100); color: white; border-radius: 10px 10px 0 0;">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-clipboard-list mr-2"></i> All Exams ({{ \App\Models\Exam::count() }})
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.exams.create') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-plus mr-1"></i> Schedule New Exam
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead style="background: #f8f9fa;">
                                <tr>
                                    <th class="pl-4">Exam Details</th>
                                    <th>Course</th>
                                    <th>Date & Time</th>
                                    <th>Duration</th>
                                    <th>Marks</th>
                                    <th>Status</th>
                                    <th class="pr-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(\App\Models\Exam::with('course')->get() as $exam)
                                <tr>
                                    <td class="pl-4">
                                        <div>
                                            <strong class="text-dark">{{ $exam->title }}</strong><br>
                                            <small class="text-muted">Code: {{ $exam->exam_code }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary px-3 py-1">{{ $exam->course->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div class="text-info">
                                            <i class="fas fa-calendar mr-1"></i>{{ $exam->exam_date->format('M d, Y') }}<br>
                                            <small class="text-muted">{{ $exam->exam_date->format('h:i A') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-warning">
                                            <i class="fas fa-clock mr-1"></i>{{ $exam->duration }} min
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <strong class="text-success">{{ $exam->total_marks }}</strong><br>
                                            <small class="text-muted">Pass: {{ $exam->passing_marks }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'scheduled' => 'primary',
                                                'ongoing' => 'warning',
                                                'completed' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge badge-{{ $statusColors[$exam->status] ?? 'secondary' }} px-3 py-1">
                                            {{ ucfirst($exam->status) }}
                                        </span>
                                    </td>
                                    <td class="pr-4">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-info btn-sm" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-success btn-sm" title="Results" onclick="viewResults({{ $exam->id }})">
                                                <i class="fas fa-chart-line"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                                            <h5>No Exams Scheduled</h5>
                                            <p>Create your first exam to get started!</p>
                                            <a href="{{ route('admin.exams.create') }}" class="btn btn-warning">
                                                <i class="fas fa-plus mr-1"></i> Schedule Exam
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function viewResults(examId) {
            alert('Exam results feature coming soon!');
        }
    </script>
@endsection
