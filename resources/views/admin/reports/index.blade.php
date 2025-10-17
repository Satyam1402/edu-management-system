{{-- resources/views/admin/reports/index.blade.php --}}
@extends('layouts.custom-admin')

@section('title', 'Reports & Analytics')
@section('page-title', 'Reports Dashboard')

@section('content')
    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="card text-white" style="background: linear-gradient(45deg, #007bff, #0056b3);">
                <div class="card-body text-center">
                    <h3 class="mb-1">₹{{ number_format($totalRevenue) }}</h3>
                    <p class="mb-0">Total Revenue</p>
                    <small>₹{{ number_format($monthlyRevenue) }} this month</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="card text-white" style="background: linear-gradient(45deg, #28a745, #1e7e34);">
                <div class="card-body text-center">
                    <h3 class="mb-1">{{ $totalStudents }}</h3>
                    <p class="mb-0">Total Students</p>
                    <small>{{ $newStudentsThisMonth }} new this month</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="card text-white" style="background: linear-gradient(45deg, #ffc107, #d39e00);">
                <div class="card-body text-center">
                    <h3 class="mb-1">{{ $activeCourses }}</h3>
                    <p class="mb-0">Active Courses</p>
                    <small>{{ $totalCourses }} total courses</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="card text-white" style="background: linear-gradient(45deg, #dc3545, #a71d2a);">
                <div class="card-body text-center">
                    <h3 class="mb-1">{{ $pendingCertificates }}</h3>
                    <p class="mb-0">Pending Certificates</p>
                    <small>{{ $approvedCertificates }} approved</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Categories -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card hover-card" onclick="location.href='{{ route('admin.reports.financial') }}'">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-chart-line fa-3x text-success"></i>
                    </div>
                    <h5>Financial Reports</h5>
                    <p class="text-muted">Revenue, payments, and financial analytics</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card hover-card" onclick="location.href='{{ route('admin.reports.students') }}'">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-users fa-3x text-primary"></i>
                    </div>
                    <h5>Student Reports</h5>
                    <p class="text-muted">Enrollment, performance, and demographics</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card hover-card" onclick="location.href='{{ route('admin.reports.courses') }}'">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-book fa-3x text-warning"></i>
                    </div>
                    <h5>Course Reports</h5>
                    <p class="text-muted">Course popularity and effectiveness</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card hover-card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-building fa-3x text-info"></i>
                    </div>
                    <h5>Franchise Reports</h5>
                    <p class="text-muted">Franchise performance and growth</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-user-plus mr-2"></i> Recent Students
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Enrolled</th>
                                    <th>Franchise</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentStudents as $student)
                                <tr>
                                    <td>
                                        <strong>{{ $student->name }}</strong><br>
                                        <small class="text-muted">{{ $student->student_id }}</small>
                                    </td>
                                    <td>{{ $student->enrollment_date->diffForHumans() }}</td>
                                    <td>{{ $student->franchise->name ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-rupee-sign mr-2"></i> Recent Payments
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPayments as $payment)
                                <tr>
                                    <td>
                                        <strong>{{ $payment->student->name ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">{{ $payment->payment_type }}</small>
                                    </td>
                                    <td class="text-success font-weight-bold">₹{{ number_format($payment->amount) }}</td>
                                    <td>{{ $payment->payment_date->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .hover-card {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
@endsection
