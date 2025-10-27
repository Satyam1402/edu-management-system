@extends('layouts.custom-admin')

@section('page-title', 'Payments')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card"></i> Payment Records
                    </h5>
                    <div class="card-tools">
                        <a href="{{ route('franchise.payments.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> New Payment
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="paymentsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
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
<script>
$(document).ready(function() {
    $('#paymentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('franchise.payments.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'student_name', name: 'student.name'},
            {data: 'course_name', name: 'course.name'},
            {data: 'formatted_amount', name: 'amount'},
            {data: 'status_badge', name: 'status'},
            {data: 'created_at', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });
});
</script>
@endsection
