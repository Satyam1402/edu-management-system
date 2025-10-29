@extends('layouts.custom-admin')

@section('title', 'Payment Records')
@section('page-title', 'Payment Records')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />
<style>
    .card {
        border-radius: 15px;
        box-shadow: 0 4px 14px #667eea16;
        border: none;
    }
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px 15px 0 0;
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-completed { background: #d4edda; color: #155724; }
    .badge-failed { background: #f8d7da; color: #721c24; }
    .badge-refunded { background: #d1ecf1; color: #0c5460; }

    .payment-stats {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

    {{-- PAYMENT STATS --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="payment-stats">
                <h4 id="totalPayments">₹0</h4>
                <p>Total Payments</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="payment-stats">
                <h4 id="pendingPayments">0</h4>
                <p>Pending Payments</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="payment-stats">
                <h4 id="completedPayments">0</h4>
                <p>Completed</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="payment-stats">
                <h4 id="thisMonthPayments">₹0</h4>
                <p>This Month</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card"></i> Payment Records
                    </h5>
                    <div class="card-tools">
                        <button class="btn btn-light btn-sm" onclick="refreshPayments()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                        <a href="{{ route('franchise.payments.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> New Payment
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    {{-- SUCCESS/ERROR MESSAGES --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    {{-- FILTERS --}}
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-control" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                                <option value="failed">Failed</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchStudent" placeholder="Search student...">
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="dateFilter">
                                <option value="">All Time</option>
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-secondary" id="clearFilters">Clear Filters</button>
                        </div>
                    </div>

                    {{-- PAYMENTS TABLE --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="paymentsTable">
                            <thead>
                                <tr>
                                    <th width="8%">ID</th>
                                    <th width="20%">Student</th>
                                    <th width="18%">Course</th>
                                    <th width="12%">Amount</th>
                                    <th width="10%">Method</th>
                                    <th width="10%">Status</th>
                                    <th width="12%">Date</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will populate this -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#paymentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('franchise.payments.index') }}",
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function(xhr, error, code) {
                console.log('AJAX Error:', xhr.responseText);
                alert('Error loading payments: ' + xhr.responseText);
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'student_name', name: 'student.name'},
            {data: 'course_name', name: 'course.name'},
            {data: 'formatted_amount', name: 'amount'},
            {data: 'payment_method', name: 'payment_method'},
            {data: 'status_badge', name: 'status'},
            {data: 'formatted_date', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[6, 'desc']], // Order by date (newest first)
        pageLength: 25,
        responsive: true,
        language: {
            processing: '<div class="spinner-border text-primary"><span class="sr-only">Loading...</span></div>',
            emptyTable: 'No payment records found',
            zeroRecords: 'No matching payment records found'
        },
        drawCallback: function(settings) {
            // Initialize tooltips after table draw
            $('[data-toggle="tooltip"]').tooltip();

            // Update stats (you can make separate API call for this)
            loadPaymentStats();
        }
    });

    // Filter functionality
    $('#statusFilter').on('change', function() {
        table.column(5).search(this.value).draw();
    });

    $('#searchStudent').on('keyup', function() {
        table.column(1).search(this.value).draw();
    });

    $('#dateFilter').on('change', function() {
        // Implement date filtering logic
        console.log('Date filter:', this.value);
    });

    $('#clearFilters').on('click', function() {
        $('#statusFilter').val('');
        $('#searchStudent').val('');
        $('#dateFilter').val('');
        table.search('').columns().search('').draw();
    });

    // Refresh function
    window.refreshPayments = function() {
        table.ajax.reload();
        loadPaymentStats();
    };

    // Load payment stats
    function loadPaymentStats() {
        // You can make separate API call for stats
        // For now, showing placeholder values
        $('#totalPayments').text('₹' + Math.floor(Math.random() * 100000));
        $('#pendingPayments').text(Math.floor(Math.random() * 50));
        $('#completedPayments').text(Math.floor(Math.random() * 200));
        $('#thisMonthPayments').text('₹' + Math.floor(Math.random() * 50000));
    }

    // Auto-hide alerts
    setTimeout(function() {
        $('.alert-dismissible').fadeOut('slow');
    }, 5000);

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endsection
