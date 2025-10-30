@extends('layouts.custom-admin')

@section('title', 'Course Students - ' . $course->name)
@section('page-title', 'Course Students')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.min.css">
@endsection

@section('content')
<div class="container-fluid">

    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3><i class="fas fa-users mr-2 text-primary"></i>Students Enrolled</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('franchise.courses.index') }}">Courses</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $course->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('franchise.courses.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Courses
                    </a>
                </div>
            </div>

            <!-- Course Info Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="card-title">{{ $course->name }}</h5>
                            <p class="card-text text-muted">{{ $course->description }}</p>
                            <div class="row">
                                <div class="col-sm-6">
                                    <small class="text-muted">
                                        <i class="fas fa-code mr-1"></i>Course Code: {{ $course->code }}
                                    </small>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted">
                                        <i class="fas fa-clock mr-1"></i>Duration: {{ $course->duration_months }} months
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="h4 text-primary mb-0">{{ $students->total() }} Students</div>
                            <small class="text-muted">Enrolled by your franchise</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list mr-2"></i>Enrolled Students
            </h5>
        </div>
        <div class="card-body">
            @if($students->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Enrollment Date</th>
                                <th>Payment Status</th>
                                <th>Amount Paid</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                @php
                                    $enrollment = $student->enrollments->first();
                                @endphp
                                <tr>
                                    <td>{{ ($students->currentPage() - 1) * $students->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle mr-3 d-flex align-items-center justify-content-center">
                                                {{ strtoupper(substr($student->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $student->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $student->email }}</td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $enrollment->enrollment_date->format('d M Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        @switch($enrollment->payment_status)
                                            @case('paid')
                                                <span class="badge badge-success">Paid</span>
                                                @break
                                            @case('pending')
                                                <span class="badge badge-warning">Pending</span>
                                                @break
                                            @case('partial')
                                                <span class="badge badge-info">Partial</span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary">{{ ucfirst($enrollment->payment_status) }}</span>
                                        @endswitch
                                    </td>
                                    <td>₹{{ number_format($enrollment->amount_paid ?? 0) }}</td>
                                    <td>
                                        @switch($enrollment->status)
                                            @case('active')
                                                <span class="badge badge-primary">Active</span>
                                                @break
                                            @case('completed')
                                                <span class="badge badge-success">Completed</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge badge-danger">Cancelled</span>
                                                @break
                                            @case('on_hold')
                                                <span class="badge badge-warning">On Hold</span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary">{{ ucfirst($enrollment->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                    onclick="viewStudent({{ $student->id }})"
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-info btn-sm"
                                                    onclick="updateStatus({{ $enrollment->id }})"
                                                    title="Update Status">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @if($enrollment->status === 'completed' && !$enrollment->certificate_issued)
                                                <button type="button" class="btn btn-outline-success btn-sm"
                                                        onclick="generateCertificate({{ $enrollment->id }})"
                                                        title="Generate Certificate">
                                                    <i class="fas fa-certificate"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($students->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $students->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-user-times fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No Students Enrolled</h4>
                    <p class="text-muted">You haven't enrolled any students in this course yet.</p>
                    <a href="{{ route('franchise.courses.index') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus mr-2"></i>Enroll Students
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Enrollment Status</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="statusForm">
                <div class="modal-body">
                    <input type="hidden" id="enrollmentId" name="enrollment_id">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="on_hold">On Hold</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js"></script>

<script>
function viewStudent(studentId) {
    // You can implement detailed student view here
    Swal.fire({
        title: 'Student Details',
        text: 'Student details view will be implemented.',
        icon: 'info'
    });
}

function updateStatus(enrollmentId) {
    $('#enrollmentId').val(enrollmentId);
    $('#statusModal').modal('show');
}

function generateCertificate(enrollmentId) {
    Swal.fire({
        title: 'Generate Certificate?',
        text: 'This will mark the course as completed and generate a certificate.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Generate!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Make AJAX call to generate certificate
            $.ajax({
                url: `/franchise/enrollments/${enrollmentId}/certificate`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Success!', response.message, 'success')
                            .then(() => location.reload());
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                }
            });
        }
    });
}

// Handle status form submission
$('#statusForm').on('submit', function(e) {
    e.preventDefault();

    const enrollmentId = $('#enrollmentId').val();
    const status = $('#status').val();

    $.ajax({
        url: `/franchise/enrollments/${enrollmentId}/status`,
        method: 'PUT',
        data: { status: status },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#statusModal').modal('hide');
                Swal.fire('Success!', response.message, 'success')
                    .then(() => location.reload());
            }
        },
        error: function() {
            Swal.fire('Error!', 'Something went wrong.', 'error');
        }
    });
});
</script>
@endsection
