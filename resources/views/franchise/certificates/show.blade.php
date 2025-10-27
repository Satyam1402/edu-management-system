@extends('layouts.custom-admin')

@section('page-title', 'Certificate Details')
@section('css')
<style>
    .card{border-radius:15px;box-shadow:0 4px 14px #667eea16;border:none;}
    .card-header{background:linear-gradient(135deg,#667eea,#764ba2);color:white;}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h4>Certificate Details</h4>
        </div>
        <div class="col text-right">
            <a href="{{ route('franchise.certificates.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            {{ $certificate->title }}
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Student</dt>
                <dd class="col-sm-9">{{ $certificate->student->name ?? '-' }}</dd>

                <dt class="col-sm-3">Issued Date</dt>
                <dd class="col-sm-9">{{ \Carbon\Carbon::parse($certificate->issued_date)->format('d M Y') }}</dd>

                <dt class="col-sm-3">Description</dt>
                <dd class="col-sm-9">{{ $certificate->description }}</dd>
            </dl>
            {{-- Removed Edit button --}}
            <a href="{{ route('franchise.certificates.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>
</div>
@endsection
