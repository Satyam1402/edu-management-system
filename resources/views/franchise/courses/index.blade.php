@extends('layouts.custom-admin')

@section('page-title', 'Courses List')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">Courses</h4>
    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Course Name</th>
                            <th>Description</th>
                            <th>Duration</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($courses as $course)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $course->name }}</td>
                            <td>{{ $course->description }}</td>
                            <td>{{ $course->duration }}</td>
                            <td>
                                <a href="{{ route('franchise.courses.show', $course) }}" class="btn btn-info btn-sm">View</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
