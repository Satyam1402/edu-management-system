@extends('layouts.custom-admin')

@section('title', 'Certificate Requests')
@section('page-title', 'Certificate Requests')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
<style>
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .status-pending {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffc107;
    }
    .status-approved {
        background: #d1ecf1;
        color: #0c5460;
        border: 1px solid #17a2b8;
    }
    .status-paid {
        background: #d4edda;
        color: #155724;
        border: 1px solid #28a745;
    }
    .status-completed {
        background: #cce5ff;
        color: #004085;
        border: 1px solid #007bff;
    }
    .status-rejected {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #dc3545;
    }
    .pay-now-btn {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
        50% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
    }
    .stats-card {
        transition: transform 0.2s;
    }
    .stats-card:hover {
        transform: translateY(-5px);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Certificate Requests</h2>
            <p class="text-muted mb-0">Manage and track all certificate requests</p>
        </div>
        <div>
            <a href="{{ route('franchise.wallet.index') }}" class="btn btn-success mr-2">
                <i class="fas fa-wallet mr-2"></i>Wallet: ₹{{ number_format($walletBalance, 2) }}
            </a>
            <a href="{{ route('franchise.certificate-requests.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>New Request
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 stats-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Review
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingCount">
                                {{ $stats['pending'] }}
                            </div>
                            <small class="text-muted">Awaiting admin approval</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 stats-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Awaiting Payment
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="approvedCount">
                                {{ $stats['approved'] }}
                            </div>
                            <small class="text-muted">Approved - Pay now</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 stats-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Completed
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="completedCount">
                                {{ $stats['completed'] }}
                            </div>
                            <small class="text-muted">Certificates issued</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-certificate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 stats-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Wallet Balance
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₹{{ number_format($walletBalance, 2) }}
                            </div>
                            <small class="text-muted">
                                <a href="{{ route('franchise.wallet.index') }}" class="text-primary">Add funds</a>
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert for Approved Requests -->
    @if($stats['approved'] > 0)
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle mr-2"></i>
        <strong>Action Required!</strong> You have <strong>{{ $stats['approved']}}</strong> approved request(s) waiting for payment.
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
    @endif

    <!-- Main DataTable Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Certificate Requests</h6>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-primary" onclick="$('#requestsTable').DataTable().ajax.reload();">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="requestsTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="15%">Student</th>
                            <th width="15%">Course</th>
                            <th width="10%">Amount</th>
                            <th width="12%">Status</th>
                            <th width="12%">Payment</th>
                            <th width="15%">Requested Date</th>
                            <th width="16%">Actions</th>
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

<!-- Payment Confirmation Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-money-bill-wave mr-2"></i>Confirm Payment
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="paymentForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-certificate fa-4x text-success mb-3"></i>
                        <h5>You are about to pay for a certificate</h5>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <strong>Student:</strong>
                                </div>
                                <div class="col-6 mb-2 text-right" id="modalStudentName">
                                    -
                                </div>
                                <div class="col-6 mb-2">
                                    <strong>Course:</strong>
                                </div>
                                <div class="col-6 mb-2 text-right" id="modalCourseName">
                                    -
                                </div>
                                <div class="col-12"><hr></div>
                                <div class="col-6 mb-2">
                                    <strong>Certificate Fee:</strong>
                                </div>
                                <div class="col-6 mb-2 text-right">
                                    <span class="h5 text-success mb-0" id="modalAmount">₹0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-light border">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Current Balance:</span>
                            <strong class="text-primary">₹{{ number_format($walletBalance, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Amount to Pay:</span>
                            <strong class="text-danger" id="modalAmountText">₹0.00</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span><strong>Balance After Payment:</strong></span>
                            <strong class="text-success" id="modalBalanceAfter">₹0.00</strong>
                        </div>
                    </div>

                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        This action will deduct the amount from your wallet. The certificate will be processed after payment.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check mr-2"></i>Confirm & Pay
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    const table = $('#requestsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('franchise.certificate-requests.index') }}",
            type: 'GET'
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'student_name', name: 'student_name' },
            { data: 'course_name', name: 'course_name' },
            { data: 'amount_formatted', name: 'amount_formatted' },
            { data: 'status_badge', name: 'status', orderable: false },
            { data: 'payment_badge', name: 'payment_status', orderable: false },
            { data: 'requested_date', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[6, 'desc']],
        language: {
            emptyTable: "No certificate requests found",
            zeroRecords: "No matching requests found"
        },
        drawCallback: function(settings) {
            $('[data-toggle="tooltip"]').tooltip();

            // Update stats from server response
            if (settings.json && settings.json.stats) {
                $('#pendingCount').text(settings.json.stats.pending);
                $('#approvedCount').text(settings.json.stats.approved);
                $('#completedCount').text(settings.json.stats.completed);
            }
        }
    });

    // Handle Pay Now button click
    $(document).on('click', '.pay-now-btn', function(e) {
        e.preventDefault();

        const requestId = $(this).data('id');
        const studentName = $(this).data('student');
        const courseName = $(this).data('course');
        const amount = parseFloat($(this).data('amount'));
        const currentBalance = {{ $walletBalance }};
        const balanceAfter = currentBalance - amount;

        // Populate modal
        $('#modalStudentName').text(studentName);
        $('#modalCourseName').text(courseName);
        $('#modalAmount').text('₹' + amount.toFixed(2));
        $('#modalAmountText').text('₹' + amount.toFixed(2));
        $('#modalBalanceAfter').text('₹' + balanceAfter.toFixed(2));

        // Set form action
        $('#paymentForm').attr('action', `/franchise/certificate-requests/${requestId}/pay`);

        // Check if sufficient balance
        if (balanceAfter < 0) {
            $('#modalBalanceAfter').removeClass('text-success').addClass('text-danger');
            $('#paymentForm button[type="submit"]').prop('disabled', true).html('<i class="fas fa-times mr-2"></i>Insufficient Balance');
        } else {
            $('#modalBalanceAfter').removeClass('text-danger').addClass('text-success');
            $('#paymentForm button[type="submit"]').prop('disabled', false).html('<i class="fas fa-check mr-2"></i>Confirm & Pay');
        }

        // Show modal
        $('#paymentModal').modal('show');
    });

    // Handle payment form submission
    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();

        // Disable submit button and show loading
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');

        // Submit form
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                $('#paymentModal').modal('hide');

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Payment Successful!',
                    text: 'Your certificate is now being processed.',
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    // Reload table and page to update wallet balance
                    location.reload();
                });
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html(originalText);

                let errorMessage = 'Payment failed. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Payment Failed',
                    text: errorMessage,
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    });

    // Auto-refresh table every 30 seconds
    setInterval(function() {
        table.ajax.reload(null, false);
    }, 30000);
});
</script>

<!-- SweetAlert2 for better alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
