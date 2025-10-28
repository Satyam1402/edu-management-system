@extends('layouts.custom-admin')

@section('page-title', 'Certificates List')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Certificates</h4>
        {{-- Removed Issue Certificate button for franchise --}}
    </div>
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0" id="certificates-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Title</th>
                            <th>Issued On</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    {{-- Dynamic DataTable content --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
$(function() {
    $('#certificates-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("franchise.certificates.index") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'student', name: 'student.name', orderable: false, searchable: true },
            { data: 'title', name: 'title' },
            { data: 'issued_date', name: 'issued_date' },
            { data: 'description', name: 'description' },
            // Example: only allow viewing certificate details; no edit or delete buttons.
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
});
</script>
@endsection
