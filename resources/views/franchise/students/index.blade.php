@extends('layouts.custom-admin')

@section('title', 'My Students Management')
@section('page-title', 'My Students Management')

@section('css')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
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
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        color: white;
        margin-bottom: 15px;
    }

    .stat-box .number {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .stat-box .label {
        font-size: 1rem;
        opacity: 0.9;
    }

    .dataTables_wrapper {
        padding: 20px;
    }

    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        margin-right: 2px;
    }

    table.dataTable tbody tr {
        transition: background-color 0.3s ease;
    }

    table.dataTable tbody tr:hover {
        background-color: #f8f9fa;
    }

    .badge {
        font-size: 0.8em;
        padding: 0.35em 0.65em;
    }

    .alert {
        border-radius: 10px;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

    {{-- SUCCESS/ERROR MESSAGES --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- STATS ROW --}}
    <div class="row stats-row">
        <div class="col-md-3">
            <div class="stat-box">
                <div class="number" id="totalStudents">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <div class="label">Total Students</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <div class="number" id="activeStudents">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <div class="label">Active Students</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <div class="number" id="graduatedStudents">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <div class="label">Graduated</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <div class="number" id="thisMonth">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <div class="label">This Month</div>
            </div>
        </div>
    </div>


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
                <table class="table table-striped table-hover" id="students-table">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="12%">Student ID</th>
                            <th width="20%">Name</th>
                            <th width="18%">Email</th>
                            <th width="12%">Phone</th>
                            <th width="15%">Course</th>
                            <th width="8%">Status</th>
                            <th width="12%">Enrollment Date</th>
                            <th width="15%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will populate this -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    // Load real stats on page load
    loadRealStats();

    // Initialize DataTable
    var table = $('#students-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('franchise.students.index') }}",
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function(xhr, error, code) {
                console.log('AJAX Error:', xhr.responseText);
                showToast('error', 'Error loading students: ' + xhr.responseText);
            }
        },
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
                className: 'btn btn-success btn-sm mr-2'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> Export PDF',
                className: 'btn btn-danger btn-sm mr-2'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-info btn-sm'
            }
        ],
        responsive: true,
        order: [[7, 'desc']], // Order by enrollment date
        pageLength: 25,
        language: {
            processing: '<div class="spinner-border text-primary"><span class="sr-only">Loading...</span></div>',
            emptyTable: "No students found. Click 'Add New Student' to get started.",
            zeroRecords: "No matching students found."
        },
        drawCallback: function(settings) {
            // Initialize tooltips after table draw
            $('[data-toggle="tooltip"]').tooltip();

            // Refresh stats when table reloads
            loadRealStats();
        }
    });

    // Load real statistics from backend
    function loadRealStats() {
        $.ajax({
            url: "{{ route('franchise.students.stats') }}",
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Update stats with real data
                    $('#totalStudents').html(response.data.total_students);
                    $('#activeStudents').html(response.data.active_students);
                    $('#graduatedStudents').html(response.data.graduated_students);
                    $('#thisMonth').html(response.data.this_month);
                } else {
                    // Show error message
                    console.error('Failed to load stats:', response.message);
                    showDefaultStats();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading stats:', error);
                showDefaultStats();
                showToast('error', 'Failed to load statistics');
            }
        });
    }

    // Show default stats on error
    function showDefaultStats() {
        $('#totalStudents').html('0');
        $('#activeStudents').html('0');
        $('#graduatedStudents').html('0');
        $('#thisMonth').html('0');
    }

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Auto-hide alerts
    setTimeout(function() {
        $('.alert-dismissible').fadeOut('slow');
    }, 5000);
});


// Delete student function
function deleteStudent(id) {
    if (!confirm('Are you sure you want to delete this student? This action cannot be undone.')) {
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
            } else {
                showToast('error', response.message);
            }
        },
        error: function(xhr) {
            let message = 'Error deleting student';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showToast('error', message);
        }
    });
}

// Toast notification function
function showToast(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';

    const toast = $(`
        <div class="position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
            <div class="alert ${alertClass} alert-dismissible fade show shadow-lg" style="min-width: 300px;">
                <i class="fas ${icon} mr-2"></i>
                <strong>${message}</strong>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        </div>
    `);

    $('body').append(toast);
    setTimeout(() => toast.find('.alert').alert('close'), 4000);
}

// Success message from session
@if(session('success'))
    showToast('success', '{{ session('success') }}');
@endif

@if(session('error'))
    showToast('error', '{{ session('error') }}');
@endif
</script>
@endsection
