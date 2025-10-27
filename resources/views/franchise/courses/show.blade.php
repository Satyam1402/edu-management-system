@extends('layouts.custom-admin')

@section('page-title', 'Course Details')

@section('content')
<div class="container-fluid">
    <a href="{{ route('franchise.courses.index') }}" class="btn btn-secondary mb-3">Back</a>
    <div class="card">
        <div class="card-header">
            {{ $course->name }}
        </div>
        <div class="card-body">
            <p><strong>Description:</strong> {{ $course->description }}</p>
            <p><strong>Duration:</strong> {{ $course->duration }}</p>
            <!-- Add more fields as needed -->
        </div>
    </div>
</div>
@endsection
