@extends('layouts.custom-admin')

@section('title', 'My Certificates')
@section('page-title', 'My Certificates')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />
<style>
    /* EXACT SAME STYLING AS YOUR CERTIFICATE REQUESTS PAGE */
    .card {
        border-radius: 15px;
        box-shadow: 0 4px 14px #667eea16;
        border: none;
    }
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-pending {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    .status-approved {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .status-rejected {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    .status-completed {
        background-color: #cce5f7;
        color: #004085;
        border: 1px solid #b8daff;
    }
    .status-issued {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .filter-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .btn-group-custom .btn {
        margin-right: 5px;
    }
    .table th {
        background-color: #f1f3f4;
        color: #495057;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.5;
    }
    .student-info {
        line-height: 1.4;
    }

    /* Stats cards with purple gradient to match your theme */
    .stats-row .small-box {
        border-radius: 15px;
        box-shadow: 0 4px 14px rgba(102, 126, 234, 0.15);
        border: none;
    }
    .stats-row .small-box.bg-gradient-purple {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .stats-row .small-box.bg-gradient-blue {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }
    .stats-row .small-box h3,
    .stats-row .small-box p,
    .stats-row .small-box .small-box-footer {
        color: #ffffff !important;
    }

    /* Custom modal styling */
    .modal-xl {
        max-width: 1200px;
    }
    .certificate-modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

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

    {{-- STATS CARDS ROW --}}
    <div class="row mb-4 stats-row">
        <div class="col-lg-6 col-md-6">
            <div class="small-box bg-gradient-purple">
                <div class="inner">
                    <h3>{{ $stats['total_certificates'] }}</h3>
                    <p>Total Certificates</p>
                </div>
                <div class="icon">
                    <i class="fas fa-certificate"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6">
            <div class="small-box bg-gradient-blue">
                <div class="inner">
                    <h3>{{ $stats['this_month'] }}</h3>
                    <p>This Month</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-certificate"></i> Approved Certificates
                    </h5>
                    <button type="button" class="btn btn-light" onclick="refreshTable()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
                <div class="card-body">

                    {{-- FILTER SECTION --}}
                    <div class="filter-card">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="statusFilter" class="form-label">
                                    <i class="fas fa-filter"></i> Filter by Status
                                </label>
                                <select class="form-control" id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="issued">Issued</option>
                                    <option value="approved">Approved</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="searchStudent" class="form-label">
                                    <i class="fas fa-search"></i> Search Student
                                </label>
                                <input type="text" class="form-control" id="searchStudent" placeholder="Search by student name...">
                            </div>
                            <div class="col-md-3">
                                <label for="dateRange" class="form-label">
                                    <i class="fas fa-calendar"></i> Date Range
                                </label>
                                <select class="form-control" id="dateRange">
                                    <option value="">All Time</option>
                                    <option value="today">Today</option>
                                    <option value="week">This Week</option>
                                    <option value="month">This Month</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button class="btn btn-secondary btn-block" id="clearFilters">
                                        <i class="fas fa-times"></i> Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CERTIFICATES TABLE --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="certificates-table">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="25%">Student</th>
                                    <th width="20%">Course</th>
                                    <th width="15%">Certificate Number</th>
                                    <th width="15%">Issued Date</th>
                                    <th width="10%">Status</th>
                                    <th width="10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- DataTables will populate this via AJAX --}}
                            </tbody>
                        </table>
                    </div>

                    {{-- EMPTY STATE (shown when no data) --}}
                    <div class="empty-state d-none" id="empty-state">
                        <i class="fas fa-certificate"></i>
                        <h4>No Certificates Found</h4>
                        <p>No approved certificates are available yet.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CERTIFICATE PREVIEW MODAL --}}
<div class="modal fade" id="certificateModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h4 class="modal-title">
                    <i class="fas fa-certificate mr-2"></i>Certificate Preview
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body certificate-modal-body" id="certificatePreview">
                <!-- Certificate preview will be loaded here -->
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading certificate...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Close
                </button>
                <a href="#" class="btn btn-success" id="downloadFromModal" style="background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%); border: none;">
                    <i class="fas fa-download mr-1"></i>Download PDF
                </a>
                <a href="#" class="btn btn-info" id="printFromModal" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); border: none;">
                    <i class="fas fa-print mr-1"></i>Print
                </a>
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
    var currentCertificateId = null;

    // Initialize DataTable with AJAX
    var table = $('#certificates-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('franchise.certificates.index') }}",
            type: 'GET',
        },
        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'student_info',
                name: 'student.name',
                render: function(data, type, row) {
                    return data; // Already formatted in controller
                }
            },
            {
                data: 'course_info',
                name: 'course.name'
            },
            {
                data: 'certificate_number',
                name: 'number'
            },
            {
                data: 'issued_date',
                name: 'issued_at',
                render: function(data, type, row) {
                    return data; // Already formatted in controller
                }
            },
            {
                data: 'status_badge',
                name: 'status',
                orderable: false,
                searchable: false
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false
            }
        ],
        responsive: true,
        pageLength: 25,
        order: [[4, 'desc']], // Order by issued date (newest first)
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>',
            emptyTable: 'No certificates found',
            zeroRecords: 'No matching certificates found'
        },
        drawCallback: function(settings) {
            // Show/hide empty state
            if (settings.fnRecordsTotal() === 0) {
                $('#certificates-table').hide();
                $('#empty-state').removeClass('d-none');
            } else {
                $('#certificates-table').show();
                $('#empty-state').addClass('d-none');
            }
        }
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert-dismissible').fadeOut('slow');
    }, 5000);

    // Filter functionality
    $('#statusFilter').on('change', function() {
        table.column(5).search(this.value).draw();
    });

    $('#searchStudent').on('keyup', function() {
        table.column(1).search(this.value).draw();
    });

    $('#dateRange').on('change', function() {
        // You can implement date range filtering here
        var range = this.value;
        console.log('Date range selected:', range);
    });

    $('#clearFilters').on('click', function() {
        $('#statusFilter').val('');
        $('#searchStudent').val('');
        $('#dateRange').val('');
        table.search('').columns().search('').draw();
    });

    // Refresh table function
    window.refreshTable = function() {
        table.ajax.reload();
    };

    // Certificate preview function - CORRECTED VERSION
    window.viewCertificate = function(certificateId) {
        currentCertificateId = certificateId;

        // Show modal first with loading state
        $('#certificateModal').modal('show');

        // Reset modal content
        $('#certificatePreview').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2">Loading certificate...</p>
            </div>
        `);

        // Set up download and print links immediately
        $('#downloadFromModal').attr('href', `/franchise/certificates/${certificateId}/download`);
        $('#printFromModal').attr('href', `/franchise/certificates/${certificateId}/print`).attr('target', '_blank');

        // Create an invisible iframe to load the certificate page
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = `/franchise/certificates/${certificateId}`;

        iframe.onload = function() {
            try {
                // Get the certificate content from iframe
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                const certificateContainer = iframeDoc.querySelector('.certificate-container');

                if (certificateContainer) {
                    // Clone the entire container with styles
                    const clonedContainer = certificateContainer.cloneNode(true);

                    // Remove action buttons from the clone
                    const actionButtons = clonedContainer.querySelector('.action-buttons');
                    if (actionButtons) {
                        actionButtons.remove();
                    }

                    // Get all styles from the iframe document
                    const iframeHead = iframeDoc.head;
                    const styles = iframeHead.querySelector('style');

                    // Create a container with styles
                    const styledContainer = document.createElement('div');
                    if (styles) {
                        const styleElement = document.createElement('style');
                        styleElement.textContent = styles.textContent;
                        styledContainer.appendChild(styleElement);
                    }
                    styledContainer.appendChild(clonedContainer);

                    // Update modal with styled certificate
                    $('#certificatePreview').html(styledContainer.outerHTML);
                } else {
                    throw new Error('Certificate content not found');
                }
            } catch (error) {
                console.error('Error loading certificate:', error);
                $('#certificatePreview').html(`
                    <div class="text-center py-5">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                        <h4 class="mt-3">Error Loading Certificate</h4>
                        <p class="text-muted">Unable to load certificate preview. Please try again.</p>
                    </div>
                `);
            } finally {
                // Clean up iframe
                document.body.removeChild(iframe);
            }
        };

        iframe.onerror = function() {
            $('#certificatePreview').html(`
                <div class="text-center py-5">
                    <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                    <h4 class="mt-3">Error Loading Certificate</h4>
                    <p class="text-muted">Unable to load certificate. Please try again.</p>
                </div>
            `);
            document.body.removeChild(iframe);
        };

        // Append iframe to body to load content
        document.body.appendChild(iframe);
    };


    // Direct download function
    window.downloadCertificate = function(certificateId) {
        window.open(`/franchise/certificates/${certificateId}/download`, '_blank');
    };

    // Direct print function
    window.printCertificate = function(certificateId) {
        window.open(`/franchise/certificates/${certificateId}/print`, '_blank');
    };

    // Modal download button click
    $('#downloadFromModal').on('click', function(e) {
        e.preventDefault();
        if (currentCertificateId) {
            downloadCertificate(currentCertificateId);
        }
    });

    // Modal print button click
    $('#printFromModal').on('click', function(e) {
        e.preventDefault();
        if (currentCertificateId) {
            printCertificate(currentCertificateId);
        }
    });

    // Tooltip initialization (if using Bootstrap tooltips)
    $('[data-toggle="tooltip"]').tooltip();

    // Reset modal when closed
    $('#certificateModal').on('hidden.bs.modal', function() {
        currentCertificateId = null;
        $('#certificatePreview').html('');
    });
});
</script>
@endsection
