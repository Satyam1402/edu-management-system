@extends('layouts.custom-admin')

@section('title', 'Certificates Management')
@section('page-title', 'Certificates')

@section('content')
<div class="container-fluid">
    <!-- Header with Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 font-weight-bold text-gray-800 mb-0">Certificates Management</h2>
            <p class="text-muted">Manage student certificates</p>
        </div>
        <a href="{{ route('admin.certificates.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i>Create Certificate
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row">
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="requested" {{ request('status') == 'requested' ? 'selected' : '' }}>Requested</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>Issued</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search certificates..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary mr-2">Filter</button>
                    <a href="{{ route('admin.certificates.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Certificates Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Certificate #</th>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Status</th>
                            <th>Issued Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($certificates as $certificate)
                        <tr>
                            <td>
                                <strong>{{ $certificate->number }}</strong>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $certificate->student->name }}</strong><br>
                                    <small class="text-muted">ID: {{ $certificate->student->student_id }}</small>
                                </div>
                            </td>
                            <td>{{ $certificate->course->name }}</td>
                            <td>{!! $certificate->status_badge !!}</td>
                            <td>{{ $certificate->formatted_issued_date }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.certificates.show', $certificate) }}" 
                                       class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($certificate->canBeModified())
                                    <a href="{{ route('admin.certificates.edit', $certificate) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    
                                    @if($certificate->status === 'requested')
                                    <form action="{{ route('admin.certificates.approve', $certificate) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-certificate fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No certificates found</p>
                                <a href="{{ route('admin.certificates.create') }}" class="btn btn-primary">
                                    Create First Certificate
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $certificates->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
