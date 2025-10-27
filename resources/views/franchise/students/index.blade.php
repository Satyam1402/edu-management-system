@extends('layouts.custom-admin')

@section('page-title', 'My Students Management')

@section('css')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<style>
    .card {
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: none;
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px 15px 0 0 !important;
        padding: 20px;
    }
    
    .stats-row {
        margin-bottom: 20px;
    }
    
    .stat-box {
        background: white;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        text-align: center;
    }
    
    .stat-box .number {
        font-size: 2rem;
        font-weight: bold;
        color: #667eea;
    }
    
    .stat-box .label {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .dataTables_wrapper {
        padding: 20px;
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    table.dataTable tbody tr {
        transition: background-color 0.3s ease;
    }
    
    table.dataTable tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">My Students Management</h4>
                    <p class="text-muted mb-0">Manage all your franchise students</p>
                </div>
                <a href="{{ route('franchise.students.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> Add New Student
                </a>
            </div>
        </div>
    </div>
    
    <!-- Main Card -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-users mr-2"></i>Students List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="students-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Course</th>
                            <th>Status</th>
                            <th>Enrollment Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#students-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('franchise.students.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'student_id', name: 'student_id'},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'phone', name: 'phone'},
            {data: 'course_name', name: 'course_name', orderable: false},
            {data: 'status_badge', name: 'status', orderable: false, searchable: false},
            {data: 'enrollment_date', name: 'enrollment_date'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Export Excel',
                className: 'btn btn-success btn-sm'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> Export PDF',
                className: 'btn btn-danger btn-sm'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-info btn-sm'
            }
        ],
        responsive: true,
        order: [[1, 'desc']],
        pageLength: 25,
        language: {
            processing: '<i class="fas fa-spinner fa-spin fa-3x text-primary"></i>',
            emptyTable: "No students found. Click 'Add New Student' to get started.",
            zeroRecords: "No matching students found."
        }
    });
});

// Delete student function
function deleteStudent(id) {
    if (!confirm('Are you sure you want to delete this student?')) {
        return;
    }
    
    $.ajax({
        url: `/franchise/students/${id}`,
        type: 'DELETE',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                $('#students-table').DataTable().ajax.reload();
                showToast('success', response.message);
            }
        },
        error: function(xhr) {
            showToast('error', 'Error deleting student');
        }
    });
}

// Toast notification
function showToast(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
    
    const toast = $(`
        <div class="position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
            <div class="alert ${alertClass} alert-dismissible fade show shadow-lg">
                <i class="fas ${icon} mr-2"></i>
                <strong>${message}</strong>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        </div>
    `);
    
    $('body').append(toast);
    setTimeout(() => toast.find('.alert').alert('close'), 3000);
}

// Success message from session
@if(session('success'))
    showToast('success', '{{ session('success') }}');
@endif
</script>
@endsection
