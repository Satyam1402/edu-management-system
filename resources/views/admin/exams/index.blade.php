@extends('layouts.custom-admin')

@section('page-title', 'Exam Management')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/exams/index.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <!-- <h3 class="mb-1">Exam Management</h3> -->
                    <h4 class="text-muted mb-0">Manage and schedule exams</>
                </div>
                <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i>Schedule New Exam
                </a>
            </div>
        </div>
    </div>

    <!-- Exams DataTable -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold text-white">
                        <i class="fas fa-clipboard-list mr-2"></i>All Exams
                    </h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-light" onclick="refreshTable()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table mb-0" id="examsTable">
                        <thead>
                            <tr>
                                <th width="300">Exam Details</th>
                                <th width="150">Course</th>
                                <th width="120">Date & Time</th>
                                <th width="100">Duration</th>
                                <th width="100">Marks</th>
                                <th width="100">Status</th>
                                <th width="150" class="text-center">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    window.examsTable = $('#examsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.exams.index') }}",
        columns: [
            { data: 'exam_details', name: 'title' },
            { data: 'course', name: 'course.name', orderable: false },
            { data: 'exam_date', name: 'exam_date' },
            { data: 'duration', name: 'duration_minutes' },
            { data: 'marks', name: 'total_marks' },
            { data: 'status', name: 'status' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[2, 'desc']],
        responsive: true,
        pageLength: 25,
        language: {
            processing: "Loading exams...",
            emptyTable: "No exams found"
        }
    });
});

function refreshTable() {
    examsTable.ajax.reload();
}

function deleteExam(examId) {
    if (!confirm('Are you sure you want to delete this exam?')) return;
    
    $.ajax({
        url: `/admin/exams/${examId}`,
        type: 'DELETE',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            examsTable.ajax.reload();
            showToast('success', response.message);
        },
        error: function() {
            showToast('error', 'Error deleting exam');
        }
    });
}

function showToast(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    
    const toast = $(`
        <div class="position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
            <div class="alert ${alertClass} alert-dismissible fade show">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        </div>
    `);
    
    $('body').append(toast);
    setTimeout(() => toast.find('.alert').alert('close'), 3000);
}
</script>
@endsection
