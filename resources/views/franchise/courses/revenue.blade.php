@extends('layouts.custom-admin')

@section('title', 'Revenue Tracking')
@section('page-title', 'Revenue Tracking')

@section('css')
<link rel="stylesheet" href="{{ asset('css/franchise/revenue/index.css') }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="fas fa-chart-line mr-2 text-success"></i>Revenue Tracking</h3>
                <div class="d-flex gap-3">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary active" onclick="showPeriod('monthly')">Monthly</button>
                        <button type="button" class="btn btn-outline-primary" onclick="showPeriod('quarterly')">Quarterly</button>
                        <button type="button" class="btn btn-outline-primary" onclick="showPeriod('yearly')">Yearly</button>
                    </div>
                    <button class="btn btn-success" onclick="exportRevenue()">
                        <i class="fas fa-download mr-2"></i>Export Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-left-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($totalRevenue) }}</div>
                            <div class="text-success small">
                                <i class="fas fa-arrow-up mr-1"></i>+15.2% from last month
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-rupee-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-left-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                This Month
                            </div>
                            @php
                                $thisMonthRevenue = $courseRevenues->sum(function($course) {
                                    return $course->enrollments()
                                        ->whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year)
                                        ->where('payment_status', 'paid')
                                        ->sum('amount_paid');
                                });
                            @endphp
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($thisMonthRevenue) }}</div>
                            <div class="text-primary small">
                                <i class="fas fa-calendar mr-1"></i>{{ now()->format('M Y') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-month fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-left-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Best Course
                            </div>
                            @php
                                $bestCourse = $courseRevenues->sortByDesc('total_revenue')->first();
                            @endphp
                            <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $bestCourse->name ?? 'N/A' }}</div>
                            <div class="text-info small">
                                @if($bestCourse)
                                    ₹{{ number_format($bestCourse->total_revenue) }} earned
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-trophy fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-left-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Avg. Per Course
                            </div>
                            @php
                                $avgRevenue = $courseRevenues->count() > 0 ? $totalRevenue / $courseRevenues->count() : 0;
                            @endphp
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($avgRevenue) }}</div>
                            <div class="text-warning small">
                                <i class="fas fa-calculator mr-1"></i>Average earnings
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Revenue Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-area mr-2"></i>Revenue Trends</h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-secondary active" onclick="updateChart('revenue')">Revenue</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="updateChart('students')">Students</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="updateChart('courses')">Courses</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Performing Courses -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-medal mr-2"></i>Top Performing Courses</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($courseRevenues->take(5) as $index => $course)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="rank-badge mr-3">
                                    @if($index === 0)
                                        <i class="fas fa-trophy text-warning"></i>
                                    @elseif($index === 1)
                                        <i class="fas fa-medal text-secondary"></i>
                                    @elseif($index === 2)
                                        <i class="fas fa-medal text-warning"></i>
                                    @else
                                        <span class="badge badge-light">{{ $index + 1 }}</span>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $course->name }}</h6>
                                    <small class="text-muted">{{ $course->code }}</small>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-weight-bold text-success">₹{{ number_format($course->total_revenue) }}</div>
                                <small class="text-muted">{{ $course->enrollments->count() }} students</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('franchise.courses.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye mr-1"></i>View All Courses
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue by Course Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-table mr-2"></i>Revenue Breakdown by Course</h5>
            <div>
                <button class="btn btn-outline-success btn-sm" onclick="exportCourseRevenue()">
                    <i class="fas fa-file-excel mr-1"></i>Export to Excel
                </button>
                <button class="btn btn-outline-danger btn-sm" onclick="exportCoursePDF()">
                    <i class="fas fa-file-pdf mr-1"></i>Export to PDF
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Course Details</th>
                            <th>Students Enrolled</th>
                            <th>Total Revenue</th>
                            <th>Avg. Revenue/Student</th>
                            <th>Payment Status</th>
                            <th>Last Enrollment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courseRevenues as $course)
                        @php
                            $enrollments = $course->enrollments;
                            $studentsCount = $enrollments->count();
                            $avgRevenue = $studentsCount > 0 ? $course->total_revenue / $studentsCount : 0;
                            $paidCount = $enrollments->where('payment_status', 'paid')->count();
                            $pendingCount = $enrollments->where('payment_status', 'pending')->count();
                            $lastEnrollment = $enrollments->sortByDesc('created_at')->first();
                        @endphp
                        <tr>
                            <td>
                                <div>
                                    <div class="font-weight-bold">{{ $course->name }}</div>
                                    <small class="text-muted">{{ $course->code }}</small>
                                    <div class="mt-1">
                                        @if($course->category)
                                            <span class="badge badge-outline-primary badge-sm">{{ ucfirst($course->category) }}</span>
                                        @endif
                                        @if($course->level)
                                            <span class="badge badge-outline-secondary badge-sm">{{ ucfirst($course->level) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-center">
                                    <div class="h6 mb-0">{{ $studentsCount }}</div>
                                    <small class="text-muted">Total Students</small>
                                </div>
                            </td>
                            <td>
                                <div class="font-weight-bold text-success h6 mb-0">₹{{ number_format($course->total_revenue) }}</div>
                            </td>
                            <td>
                                <div class="font-weight-bold">₹{{ number_format($avgRevenue) }}</div>
                            </td>
                            <td>
                                <div>
                                    @if($paidCount > 0)
                                        <span class="badge badge-success badge-sm">{{ $paidCount }} Paid</span><br>
                                    @endif
                                    @if($pendingCount > 0)
                                        <span class="badge badge-warning badge-sm">{{ $pendingCount }} Pending</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($lastEnrollment)
                                    <div class="small">{{ $lastEnrollment->created_at->diffForHumans() }}</div>
                                @else
                                    <span class="text-muted">No enrollments</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('franchise.courses.show', $course) }}"
                                       class="btn btn-outline-primary" title="View Course">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('franchise.courses.students', $course) }}"
                                       class="btn btn-outline-success" title="View Students">
                                        <i class="fas fa-users"></i>
                                    </a>
                                    <button class="btn btn-outline-info"
                                            onclick="viewCourseAnalytics({{ $course->id }})"
                                            title="View Analytics">
                                        <i class="fas fa-chart-pie"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Revenue Data</h5>
                                <p class="text-muted">Start enrolling students to see revenue analytics.</p>
                                <a href="{{ route('franchise.courses.index') }}" class="btn btn-primary">
                                    <i class="fas fa-graduation-cap mr-2"></i>Browse Courses
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
// Revenue Chart
let revenueChart;

document.addEventListener('DOMContentLoaded', function() {
    initializeRevenueChart();
});

function initializeRevenueChart() {
    const ctx = document.getElementById('revenueChart').getContext('2d');

    // Sample data - replace with actual data from controller
    const chartData = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: 'Revenue (₹)',
            data: [12000, 19000, 15000, 25000, 22000, 30000, 35000, 32000, 40000, 45000, 42000, 50000],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.4,
            fill: true
        }]
    };

    revenueChart = new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Monthly Revenue Trends'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value, index, values) {
                            return '₹' + value.toLocaleString();
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
}

function updateChart(type) {
    // Update chart based on type (revenue, students, courses)
    const buttons = document.querySelectorAll('.btn-group .btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    // Update chart data based on type
    // This would typically fetch new data via AJAX
    console.log('Updating chart to show:', type);
}

function showPeriod(period) {
    // Update data for different periods
    const buttons = document.querySelectorAll('.btn-group .btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    console.log('Showing period:', period);
}

function viewCourseAnalytics(courseId) {
    // Show detailed analytics for a specific course
    alert(`View analytics for course ID: ${courseId}`);
}

function exportRevenue() {
    window.open('/franchise/revenue/export', '_blank');
}

function exportCourseRevenue() {
    window.open('/franchise/revenue/courses/excel', '_blank');
}

function exportCoursePDF() {
    window.open('/franchise/revenue/courses/pdf', '_blank');
}
</script>
@endsection
