@extends('layouts.custom-admin')

@section('page-title', 'Certificate Requests Dashboard')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css" />
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .stats-card {
        background: var(--primary-gradient);
        border-radius: 15px;
        color: white;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        overflow: hidden;
        position: relative;
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transform: translate(30px, -30px);
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
    }

    .stats-card.success {
        background: var(--success-gradient);
    }

    .stats-card.warning {
        background: var(--warning-gradient);
    }

    .stats-card.info {
        background: var(--info-gradient);
    }

    .stats-number {
        font-size: 2.5rem;
        font-weight: 700;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .main-card {
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
    }

    .main-card .card-header {
        background: var(--primary-gradient);
        color: white;
        border-radius: 20px 20px 0 0!important;
        padding: 2rem;
        border: none;
    }

    .wallet-balance-card {
        background: var(--info-gradient);
        border-radius: 15px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .wallet-balance {
        font-size: 1.8rem;
        font-weight: 700;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .filter-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid #dee2e6;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .form-control, .form-select {
        border-radius: 10px;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
        padding: 0.75rem 1rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .btn {
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        padding: 0.75rem 1.5rem;
    }

    .btn-primary {
        background: var(--primary-gradient);
        border: none;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-success {
        background: var(--success-gradient);
        border: none;
    }

    .btn-info {
        background: var(--info-gradient);
        border: none;
    }

    .btn-warning {
        background: var(--warning-gradient);
        border: none;
    }

    /* Status Badges - Enhanced */
    .status-badge {
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .status-pending {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .status-processing {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }

    .status-approved {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }

    .status-rejected {
        background: linear-gradient(135deg, #ff4757 0%, #ff3742 100%);
        color: white;
    }

    .status-completed {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    /* DataTable Enhancements */
    .table {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    .table thead th {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        color: #495057;
        font-weight: 700;
        border: none;
        padding: 1.2rem 1rem;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    .table tbody tr {
        transition: all 0.3s ease;
        border: none;
    }

    .table tbody tr:hover {
        background-color: #f8f9ff;
        transform: scale(1.01);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.1);
    }

    .table tbody td {
        border: none;
        padding: 1.2rem 1rem;
        vertical-align: middle;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #6c757d;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 20px;
        margin: 2rem 0;
    }

    .empty-state i {
        font-size: 5rem;
        margin-bottom: 2rem;
        opacity: 0.3;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .empty-state h4 {
        color: #495057;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    /* Action Buttons */
    .action-buttons .btn {
        margin: 0 2px;
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
    }

    .student-info {
        line-height: 1.6;
    }

    .student-name {
        font-weight: 600;
        color: #2d3748;
        font-size: 1rem;
    }

    .student-email {
        color: #718096;
        font-size: 0.875rem;
    }

    /* Quick Stats Animation */
    .stats-card .stats-number {
        animation: countUp 1s ease-out;
    }

    @keyframes countUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive Improvements */
    @media (max-width: 768px) {
        .stats-number {
            font-size: 2rem;
        }

        .filter-card .row > div {
            margin-bottom: 1rem;
        }

        .main-card .card-header {
            padding: 1.5rem;
        }
    }

    /* Loading Animation */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        border-radius: 15px;
    }

    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    {{-- SUCCESS/ERROR MESSAGES --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-lg" style="border-radius: 15px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-lg" style="border-radius: 15px;">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- DASHBOARD STATS ROW --}}
    <div class="row mb-4">
        {{-- Quick Stats Cards --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card">
                <div class="card-body text-center position-relative">
                    <i class="fas fa-clock fa-2x mb-3 opacity-75"></i>
                    <div class="stats-number" id="pending-count">-</div>
                    <h6 class="card-title mb-0 opacity-90">Pending Requests</h6>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card success">
                <div class="card-body text-center position-relative">
                    <i class="fas fa-check-circle fa-2x mb-3 opacity-75"></i>
                    <div class="stats-number" id="approved-count">-</div>
                    <h6 class="card-title mb-0 opacity-90">Approved</h6>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card info">
                <div class="card-body text-center position-relative">
                    <i class="fas fa-certificate fa-2x mb-3 opacity-75"></i>
                    <div class="stats-number" id="completed-count">-</div>
                    <h6 class="card-title mb-0 opacity-90">Completed</h6>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card wallet-balance-card">
                <div class="card-body text-center position-relative">
                    <i class="fas fa-wallet fa-2x mb-3 opacity-75"></i>
                    <div class="wallet-balance" id="wallet-balance">₹0.00</div>
                    <h6 class="card-title mb-0 opacity-90">Wallet Balance</h6>
                    <a href="{{ route('franchise.wallet.create') }}" class="btn btn-light btn-sm mt-2">
                        <i class="fas fa-plus"></i> Add Funds
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT CARD --}}
    <div class="row">
        <div class="col-12">
            <div class="card main-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="mb-1">
                            <i class="fas fa-certificate me-2"></i> Certificate Requests Management
                        </h4>
                        <small class="opacity-75">Track and manage all your certificate requests</small>
                    </div>
                    <a href="{{ route('franchise.certificate-requests.create') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-plus me-2"></i> New Request
                    </a>
                </div>

                <div class="card-body position-relative">
                    {{-- Loading Overlay --}}
                    <div class="loading-overlay d-none" id="loading-overlay">
                        <div class="text-center">
                            <div class="loading-spinner"></div>
                            <p class="mt-3 text-muted">Loading certificate requests...</p>
                        </div>
                    </div>

                    {{-- ADVANCED FILTER SECTION --}}
                    <div class="filter-card">
                        <h6 class="text-dark mb-3">
                            <i class="fas fa-filter me-2"></i> Advanced Filters
                        </h6>
                        <div class="row g-3">
                            <div class="col-xl-3 col-md-6">
                                <label for="statusFilter" class="form-label fw-bold">
                                    <i class="fas fa-flag me-1"></i> Status
                                </label>
                                <select class="form-select" id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="pending">🟡 Pending</option>
                                    <option value="processing">🔵 Processing</option>
                                    <option value="approved">🟢 Approved</option>
                                    <option value="rejected">🔴 Rejected</option>
                                    <option value="completed">🟣 Completed</option>
                                </select>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <label for="searchStudent" class="form-label fw-bold">
                                    <i class="fas fa-user-search me-1"></i> Student
                                </label>
                                <input type="text" class="form-control" id="searchStudent"
                                       placeholder="Search by student name...">
                            </div>

                            <div class="col-xl-2 col-md-6">
                                <label for="dateRange" class="form-label fw-bold">
                                    <i class="fas fa-calendar-alt me-1"></i> Period
                                </label>
                                <select class="form-select" id="dateRange">
                                    <option value="">All Time</option>
                                    <option value="today">Today</option>
                                    <option value="week">This Week</option>
                                    <option value="month">This Month</option>
                                    <option value="quarter">This Quarter</option>
                                </select>
                            </div>

                            <div class="col-xl-2 col-md-6">
                                <label for="courseFilter" class="form-label fw-bold">
                                    <i class="fas fa-book me-1"></i> Course
                                </label>
                                <select class="form-select" id="courseFilter">
                                    <option value="">All Courses</option>
                                    {{-- Will be populated via AJAX --}}
                                </select>
                            </div>

                            <div class="col-xl-2 col-md-6">
                                <label class="form-label fw-bold">&nbsp;</label>
                                <div class="d-grid">
                                    <button class="btn btn-outline-secondary" id="clearFilters">
                                        <i class="fas fa-eraser me-1"></i> Clear All
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Quick Filter Buttons --}}
                        <div class="mt-3 pt-3 border-top">
                            <small class="text-muted mb-2 d-block">Quick Filters:</small>
                            <div class="btn-group-toggle" data-toggle="buttons">
                                <button class="btn btn-outline-warning btn-sm me-2" data-filter="pending">
                                    <i class="fas fa-clock me-1"></i> Pending Only
                                </button>
                                <button class="btn btn-outline-success btn-sm me-2" data-filter="approved">
                                    <i class="fas fa-check me-1"></i> Approved Only
                                </button>
                                <button class="btn btn-outline-info btn-sm me-2" data-filter="completed">
                                    <i class="fas fa-certificate me-1"></i> Completed Only
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- CERTIFICATE REQUESTS TABLE --}}
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="requests-table">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="25%">Student Details</th>
                                    <th width="20%">Course</th>
                                    <th width="10%">Amount</th>
                                    <th width="12%">Status</th>
                                    <th width="15%">Request Date</th>
                                    <th width="13%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- DataTables will populate this via AJAX --}}
                            </tbody>
                        </table>
                    </div>

                    {{-- EMPTY STATE --}}
                    <div class="empty-state d-none" id="empty-state">
                        <i class="fas fa-certificate"></i>
                        <h4>No Certificate Requests Found</h4>
                        <p class="text-muted mb-4">You haven't submitted any certificate requests yet.<br>Start by requesting certificates for your students!</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('franchise.certificate-requests.create') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus me-2"></i> Create First Request
                            </a>
                            <a href="{{ route('franchise.wallet.create') }}" class="btn btn-info btn-lg">
                                <i class="fas fa-wallet me-2"></i> Add Wallet Funds
                            </a>
                        </div>
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
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    let table;

    // Initialize DataTable with enhanced configuration
    function initializeDataTable() {
        $('#loading-overlay').removeClass('d-none');

        table = $('#requests-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ route('franchise.certificate-requests.index') }}",
                type: 'GET',
                data: function(d) {
                    d.status_filter = $('#statusFilter').val();
                    d.course_filter = $('#courseFilter').val();
                    d.date_range = $('#dateRange').val();
                },
                dataSrc: function(json) {
                    // Update dashboard stats
                    updateDashboardStats(json.stats || {});
                    $('#loading-overlay').addClass('d-none');
                    return json.data;
                }
            },
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                },
                {
                    data: 'student_name',
                    name: 'student.name',
                    render: function(data, type, row) {
                        if (row.student) {
                            return `
                                <div class="student-info">
                                    <div class="student-name">${row.student.name}</div>
                                    <small class="student-email">
                                        <i class="fas fa-envelope me-1"></i>${row.student.email || 'No email'}
                                    </small>
                                </div>
                            `;
                        }
                        return '<span class="text-muted">N/A</span>';
                    }
                },
                {
                    data: 'course_name',
                    name: 'course.name',
                    render: function(data, type, row) {
                        if (row.course) {
                            return `
                                <div>
                                    <strong>${row.course.name}</strong>
                                    <br><small class="text-muted">
                                        <i class="fas fa-tag me-1"></i>Certificate Fee
                                    </small>
                                </div>
                            `;
                        }
                        return '<em class="text-muted">No Course</em>';
                    }
                },
                {
                    data: 'amount_formatted',
                    name: 'amount',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return `<strong class="text-success fs-6">₹${parseFloat(row.amount || 0).toLocaleString()}</strong>`;
                    }
                },
                {
                    data: 'status_badge',
                    name: 'status',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                },
                {
                    data: 'requested_date',
                    name: 'created_at',
                    render: function(data, type, row) {
                        if (row.created_at) {
                            const date = new Date(row.created_at);
                            return `
                                <div class="text-center">
                                    <strong>${date.toLocaleDateString('en-GB')}</strong>
                                    <br><small class="text-muted">${date.toLocaleTimeString('en-GB', {hour: '2-digit', minute:'2-digit'})}</small>
                                </div>
                            `;
                        }
                        return '<span class="text-muted">N/A</span>';
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-center action-buttons'
                }
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[5, 'desc']], // Order by request date (newest first)
            language: {
                processing: `
                    <div class="text-center p-4">
                        <div class="loading-spinner mx-auto mb-3"></div>
                        <p class="text-muted">Loading certificate requests...</p>
                    </div>
                `,
                emptyTable: 'No certificate requests found',
                zeroRecords: 'No matching records found',
                info: 'Showing _START_ to _END_ of _TOTAL_ requests',
                infoEmpty: 'Showing 0 to 0 of 0 requests',
                infoFiltered: '(filtered from _MAX_ total requests)',
                lengthMenu: 'Show _MENU_ requests per page',
                search: 'Search requests:',
                paginate: {
                    first: 'First',
                    last: 'Last',
                    next: 'Next',
                    previous: 'Previous'
                }
            },
            drawCallback: function(settings) {
                // Show/hide empty state
                if (settings.fnRecordsTotal() === 0) {
                    $('#requests-table').addClass('d-none');
                    $('#empty-state').removeClass('d-none');
                } else {
                    $('#requests-table').removeClass('d-none');
                    $('#empty-state').addClass('d-none');
                }

                // Initialize tooltips for action buttons
                $('[data-bs-toggle="tooltip"]').tooltip();

                // Update row animations
                $('#requests-table tbody tr').each(function(index) {
                    $(this).css('animation-delay', (index * 0.1) + 's');
                });
            },
            initComplete: function() {
                $('#loading-overlay').addClass('d-none');
                loadCourseOptions();
                loadWalletBalance();
            }
        });
    }

    // Update dashboard statistics
    function updateDashboardStats(stats) {
        $('#pending-count').text(stats.pending || 0);
        $('#approved-count').text(stats.approved || 0);
        $('#completed-count').text(stats.completed || 0);

        // Animate number changes
        $('.stats-number').each(function() {
            $(this).addClass('animate__animated animate__pulse');
            setTimeout(() => $(this).removeClass('animate__animated animate__pulse'), 1000);
        });
    }

    // Load wallet balance
    function loadWalletBalance() {
        $.ajax({
            url: "{{ route('franchise.certificate-requests.wallet-balance') }}",
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#wallet-balance').text(response.formatted_balance);
                }
            },
            error: function() {
                $('#wallet-balance').text('₹0.00');
            }
        });
    }

    // Load course options for filter
    function loadCourseOptions() {
        $.ajax({
            url: "{{ route('franchise.courses.list') }}", // You'll need to create this route
            type: 'GET',
            success: function(courses) {
                const $courseFilter = $('#courseFilter');
                $courseFilter.find('option:not(:first)').remove();

                courses.forEach(function(course) {
                    $courseFilter.append(`<option value="${course.id}">${course.name}</option>`);
                });
            },
            error: function() {
                console.log('Could not load course options');
            }
        });
    }

    // Auto-hide alerts
    setTimeout(() => $('.alert-dismissible').fadeOut('slow'), 6000);

    // Filter functionality
    $('#statusFilter, #courseFilter, #dateRange').on('change', function() {
        table.ajax.reload(null, false);
    });

    $('#searchStudent').on('keyup debounce', function() {
        table.search(this.value).draw();
    });

    // Quick filter buttons
    $('[data-filter]').on('click', function() {
        const filter = $(this).data('filter');
        $('#statusFilter').val(filter).trigger('change');

        // Update button states
        $('[data-filter]').removeClass('active');
        $(this).addClass('active');
    });

    // Clear all filters
    $('#clearFilters').on('click', function() {
        $('#statusFilter, #courseFilter, #dateRange').val('');
        $('#searchStudent').val('');
        $('[data-filter]').removeClass('active');
        table.search('').ajax.reload(null, false);
    });

    // Debounce function for search
    let searchTimer;
    $('#searchStudent').on('keyup', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            table.search(this.value).draw();
        }, 500);
    });

    // Initialize everything
    initializeDataTable();

    // Refresh data every 30 seconds
    setInterval(() => {
        table.ajax.reload(null, false);
        loadWalletBalance();
    }, 30000);

    // Export functionality (optional)
    $('#export-requests').on('click', function() {
        window.open("{{ route('franchise.certificate-requests.export') }}", '_blank');
    });
});
</script>
@endsection
