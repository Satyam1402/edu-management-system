{{-- resources/views/admin/dashboard.blade.php - OFFICIAL ADMINLTE VERSION --}}
@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard Overview</h1>
@stop

@section('content')
    {{-- Welcome Message --}}
    <div class="row">
        <div class="col-12">
            <div class="callout callout-info">
                <h5><i class="fas fa-info"></i> Welcome back, {{ auth()->user()->name }}!</h5>
                Here's what's happening with your education management system today.
            </div>
        </div>
    </div>

    {{-- Small boxes (Stat box) --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            {{-- small box --}}
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ \App\Models\Franchise::count() }}</h3>
                    <p>Total Franchises</p>
                </div>
                <div class="icon">
                    <i class="fas fa-building"></i>
                </div>
                <a href="{{ route('admin.franchises.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ \App\Models\Student::count() }}</h3>
                    <p>Total Students</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ \App\Models\Course::where('status', 'active')->count() }}</h3>
                    <p>Active Courses</p>
                </div>
                <div class="icon">
                    <i class="fas fa-book"></i>
                </div>
                <a href="{{ route('admin.courses.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ \App\Models\Certificate::where('status', 'requested')->count() }}</h3>
                    <p>Pending Certificates</p>
                </div>
                <div class="icon">
                    <i class="fas fa-certificate"></i>
                </div>
                <a href="{{ route('admin.certificates.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    {{-- Main row --}}
    <div class="row">
        {{-- Left col --}}
        <section class="col-lg-7 connectedSortable">
            {{-- Quick Actions Box --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt mr-1"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-plus"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Add New Franchise</span>
                                    <span class="info-box-number">
                                        <a href="{{ route('admin.franchises.create') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> Create
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-book"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Create Course</span>
                                    <span class="info-box-number">
                                        <a href="{{ route('admin.courses.create') }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-plus"></i> Create
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Right col (fixed) --}}
        <section class="col-lg-5 connectedSortable">
            {{-- Recent Activity --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock mr-1"></i>
                        Recent Activity
                    </h3>
                </div>
                <div class="card-body">
                    <div class="timeline timeline-inverse">
                        @forelse(\App\Models\Franchise::latest()->take(5)->get() as $franchise)
                        <div class="time-label">
                            <span class="bg-success">
                                {{ $franchise->created_at->format('M d') }}
                            </span>
                        </div>
                        <div>
                            <i class="fas fa-building bg-primary"></i>
                            <div class="timeline-item">
                                <h3 class="timeline-header">
                                    <strong>{{ $franchise->name }}</strong> was added
                                </h3>
                                <div class="timeline-body">
                                    New franchise registered with code: {{ $franchise->code }}
                                </div>
                                <div class="timeline-footer">
                                    <small class="text-muted">{{ $franchise->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div>
                            <i class="fas fa-info bg-info"></i>
                            <div class="timeline-item">
                                <h3 class="timeline-header">No recent activity</h3>
                                <div class="timeline-body">
                                    Start by creating your first franchise.
                                </div>
                            </div>
                        </div>
                        @endforelse
                        <div>
                            <i class="far fa-clock bg-gray"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@stop
