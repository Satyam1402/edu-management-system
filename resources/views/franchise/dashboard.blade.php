@extends('layouts.custom-admin')

@section('page-title', 'Franchise Dashboard')

@section('css')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: transform 0.2s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0;
    }
    
    .stat-label {
        color: #6c757d;
        font-size: 0.9rem;
        margin-top: 5px;
    }
    
    .recent-list {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .list-item {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }
    
    .list-item:hover {
        background: #f8f9fa;
    }
    
    .list-item:last-child {
        border-bottom: none;
    }
    
    .badge-status {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2>Welcome back, {{ Auth::user()->name }}!</h2>
            <p class="text-muted">Here's what's happening with your franchise today</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-3 mb-4">
        <!-- Total Students -->
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-number text-primary">{{ $stats['total_students'] }}</p>
                        <p class="stat-label mb-0">Total Students</p>
                    </div>
                    <div class="text-primary" style="font-size: 3rem; opacity: 0.2;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Students -->
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-number text-success">{{ $stats['active_students'] }}</p>
                        <p class="stat-label mb-0">Active Students</p>
                    </div>
                    <div class="text-success" style="font-size: 3rem; opacity: 0.2;">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graduated -->
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-number text-info">{{ $stats['graduated_students'] }}</p>
                        <p class="stat-label mb-0">Graduated</p>
                    </div>
                    <div class="text-info" style="font-size: 3rem; opacity: 0.2;">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dropped -->
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-number text-warning">{{ $stats['dropped_students'] }}</p>
                        <p class="stat-label mb-0">Dropped</p>
                    </div>
                    <div class="text-warning" style="font-size: 3rem; opacity: 0.2;">
                        <i class="fas fa-user-times"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row Stats -->
    <div class="row g-3 mb-4">
        <!-- Total Certificates -->
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-number text-primary">{{ $stats['total_certificates'] }}</p>
                        <p class="stat-label mb-0">Total Certificates</p>
                    </div>
                    <div class="text-primary" style="font-size: 3rem; opacity: 0.2;">
                        <i class="fas fa-certificate"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Certificates -->
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-number text-warning">{{ $stats['pending_certificates'] }}</p>
                        <p class="stat-label mb-0">Pending Approval</p>
                    </div>
                    <div class="text-warning" style="font-size: 3rem; opacity: 0.2;">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-number text-success">₹{{ number_format($stats['total_payments']) }}</p>
                        <p class="stat-label mb-0">Total Revenue</p>
                    </div>
                    <div class="text-success" style="font-size: 3rem; opacity: 0.2;">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-number text-danger">{{ $stats['pending_payments_count'] }}</p>
                        <p class="stat-label mb-0">Pending Payments</p>
                    </div>
                    <div class="text-danger" style="font-size: 3rem; opacity: 0.2;">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row g-3">
        <!-- Recent Students -->
        <div class="col-md-4">
            <div class="recent-list">
                <h5 class="mb-3"><i class="fas fa-user-plus text-primary mr-2"></i>Recent Students</h5>
                @forelse($recentStudents as $student)
                <div class="list-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $student->name }}</strong>
                            <small class="d-block text-muted">{{ $student->email }}</small>
                        </div>
                        <span class="badge-status bg-{{ $student->status == 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($student->status) }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center py-3">No recent students</p>
                @endforelse
                <div class="text-center mt-3">
                    <a href="{{ route('franchise.students.index') }}" class="btn btn-sm btn-outline-primary">
                        View All Students <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Certificates -->
        <div class="col-md-4">
            <div class="recent-list">
                <h5 class="mb-3"><i class="fas fa-certificate text-info mr-2"></i>Recent Certificates</h5>
                @forelse($recentCertificates as $certificate)
                <div class="list-item">
                    <div>
                        <strong>{{ $certificate->student->name }}</strong>
                        <small class="d-block text-muted">{{ $certificate->course->name ?? 'N/A' }}</small>
                        <span class="badge-status bg-{{ $certificate->status == 'approved' ? 'success' : ($certificate->status == 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($certificate->status) }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center py-3">No recent certificates</p>
                @endforelse
                <div class="text-center mt-3">
                    <a href="{{ route('franchise.certificates.index') }}" class="btn btn-sm btn-outline-info">
                        View All Certificates <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="col-md-4">
            <div class="recent-list">
                <h5 class="mb-3"><i class="fas fa-rupee-sign text-success mr-2"></i>Recent Payments</h5>
                @forelse($recentPayments as $payment)
                <div class="list-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $payment->student->name }}</strong>
                            <small class="d-block text-muted">₹{{ number_format($payment->amount) }}</small>
                        </div>
                        <span class="badge-status bg-{{ $payment->status == 'completed' ? 'success' : ($payment->status == 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center py-3">No recent payments</p>
                @endforelse
                <div class="text-center mt-3">
                    <a href="{{ route('franchise.payments.index') }}" class="btn btn-sm btn-outline-success">
                        View All Payments <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
