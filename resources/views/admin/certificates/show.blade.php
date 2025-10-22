@extends('layouts.custom-admin')

@section('title', 'Certificate Details')
@section('page-title', 'Certificate #' . $certificate->number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Certificate Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Certificate Information</h6>
                            <p><strong>Number:</strong> {{ $certificate->number }}</p>
                            <p><strong>Status:</strong> {!! $certificate->status_badge !!}</p>
                            <p><strong>Created:</strong> {{ $certificate->created_at->format('M d, Y H:i A') }}</p>
                            @if($certificate->issued_at)
                            <p><strong>Issued:</strong> {{ $certificate->issued_at->format('M d, Y H:i A') }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Student Information</h6>
                            <p><strong>Name:</strong> {{ $certificate->student->name }}</p>
                            <p><strong>ID:</strong> {{ $certificate->student->student_id }}</p>
                            <p><strong>Course:</strong> {{ $certificate->course->name }}</p>
                            <p><strong>Franchise:</strong> {{ $certificate->student->franchise->name }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="btn-group">
                        @if($certificate->canBeModified())
                        <a href="{{ route('admin.certificates.edit', $certificate) }}" class="btn btn-primary">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                        @endif
                        
                        @if($certificate->status === 'requested')
                        <form action="{{ route('admin.certificates.approve', $certificate) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check mr-2"></i>Approve
                            </button>
                        </form>
                        @endif
                        
                        <a href="{{ route('admin.certificates.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-2"></i>Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
