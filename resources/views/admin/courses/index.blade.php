@extends('layouts.custom-admin')

@section('title', 'Courses')
@section('page-title', 'Course Management')

@section('css')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/courses/index.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1 font-weight-bold">
                            <i class="fas fa-book mr-3"></i>Course Management
                        </h2>
                        <p class="mb-0 opacity-75">Manage and organize all educational courses</p>
                    </div>
                    <div class="ml-auto">
                        <a href="{{ route('admin.courses.create') }}" class="btn btn-light btn-lg">
                            <i class="fas fa-plus mr-2"></i>Create New Course
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="course-card">
                <div class="course-icon bg-primary">
                    <i class="fas fa-book"></i>
                </div>
                <div class="course-number">{{ \App\Models\Course::count() }}</div>
                <div class="course-label">Total Courses</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="course-card">
                <div class="course-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="course-number">{{ \App\Models\Course::where('status', 'active')->count() }}</div>
                <div class="course-label">Active Courses</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="course-card">
                <div class="course-icon bg-warning">
                    <i class="fas fa-star"></i>
                </div>
                <div class="course-number">{{ \App\Models\Course::where('is_featured', true)->count() }}</div>
                <div class="course-label">Featured Courses</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="course-card">
                <div class="course-icon bg-info">
                    <i class="fas fa-users"></i>
                </div>
                <div class="course-number">{{ \App\Models\Course::withCount('students')->get()->sum('students_count') }}</div>
                <div class="course-label">Total Enrolled</div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row">
        <div class="col-12">
            <div class="filter-section">
                <h6 class="mb-3 font-weight-bold text-gray-800">
                    <i class="fas fa-filter mr-2"></i>Advanced Filters
                </h6>
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label font-weight-bold">Status</label>
                        <select id="statusFilter" class="form-control">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            {{-- <option value="draft">Draft</option>
                            <option value="archived">Archived</option> --}}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label font-weight-bold">Level</label>
                        <select id="levelFilter" class="form-control">
                            <option value="">All Levels</option>
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label font-weight-bold">Category</label>
                        <select id="categoryFilter" class="form-control">
                            <option value="">All Categories</option>
                            <option value="technology">Technology</option>
                            <option value="business">Business</option>
                            <option value="design">Design</option>
                            <option value="marketing">Marketing</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label font-weight-bold">Featured</label>
                        <select id="featuredFilter" class="form-control">
                            <option value="">All Courses</option>
                            <option value="yes">Featured Only</option>
                            <option value="no">Non-Featured</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary" onclick="applyFilters()">
                            <i class="fas fa-search mr-2"></i>Apply Filters
                        </button>
                        <button type="button" class="btn btn-outline-secondary ml-2" onclick="resetFilters()">
                            <i class="fas fa-undo mr-2"></i>Reset
                        </button>
                        <div class="float-right">
                            <button type="button" class="btn btn-info bulk-action-btn disabled" onclick="handleBulkAction('feature')">
                                <i class="fas fa-star mr-1"></i>Feature Selected
                            </button>
                            <button type="button" class="btn btn-danger bulk-action-btn disabled ml-2" onclick="handleBulkAction('delete')">
                                <i class="fas fa-trash mr-1"></i>Delete Selected
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses DataTable -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold text-white">
                        <i class="fas fa-table mr-2"></i>All Courses <span class="bulk-selection-count"></span>
                    </h6>
                    <div class="btn-group btn-group-sm ml-auto">
                        <button type="button" class="btn btn-light" onclick="refreshTable()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body" style="padding: 20px;">
                    <!-- DataTable will be inserted here -->
                    <table class="table mb-0" id="coursesTable">
                        <thead>
                            <tr>
                                <th width="50">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="selectAll">
                                        <label class="custom-control-label" for="selectAll"></label>
                                    </div>
                                </th>
                                <th width="300">Course Details</th>
                                <th width="120">Course Code</th>
                                <th width="120">Duration</th>
                                <th width="120">Fee</th>
                                <th width="150">Students</th>
                                <th width="100" class="text-center">Status</th>
                                <th width="150" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick View Modal -->
<div class="modal fade" id="quickViewModal" tabindex="-1" role="dialog" aria-labelledby="quickViewModalLabel">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content" style="border-radius: 15px; border: none;">
            <div class="modal-header" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; border-radius: 15px 15px 0 0; border: none;">
                <h5 class="modal-title" id="quickViewModalLabel">
                    <i class="fas fa-book mr-2"></i>Course Quick View
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 1;">
                    <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="quickViewContent" style="padding: 30px; max-height: 70vh; overflow-y: auto;">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading course details...</p>
                </div>
            </div>
            <div class="modal-footer" style="border: none; padding: 20px 30px; background: #f8f9fa; border-radius: 0 0 15px 15px;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

<script>
let coursesTable;

$(document).ready(function() {
    console.log('Courses Index - Initializing...');

    // Initialize DataTable
    coursesTable = $('#coursesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: false,
        scrollX: true,
        ajax: {
            url: "{{ route('admin.courses.index') }}",
            type: "GET",
            data: function (d) {
                d.status = $('#statusFilter').val();
                d.level = $('#levelFilter').val();
                d.category = $('#categoryFilter').val();
                d.featured = $('#featuredFilter').val();
            },
            error: function(xhr, error, thrown) {
                console.error('DataTable AJAX Error:', error, thrown, xhr.responseText);
                showToast('error', 'Error loading data. Please refresh the page.');
            }
        },
        columns: [
            { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false, className: 'text-center' },
            { data: 'course_details', name: 'name' },
            { data: 'course_code', name: 'code', className: 'text-center' },
            { data: 'duration', name: 'duration_months', className: 'text-center' },
            { data: 'fee', name: 'fee', className: 'text-center' },
            { data: 'students', name: 'students_count', className: 'text-center' },
            { data: 'status', name: 'status', className: 'text-center' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[1, 'asc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        language: {
            processing: '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><p class="mt-3">Loading courses...</p></div>',
            emptyTable: `
                <div class="text-center py-5">
                    <i class="fas fa-book fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">No Courses Found</h4>
                    <p class="text-muted mb-4">Start building your course catalog!</p>
                    <a href="{{ route('admin.courses.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus mr-2"></i>Create First Course
                    </a>
                </div>
            `,
            zeroRecords: `
                <div class="text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">No matching courses found</h4>
                    <p class="text-muted">Try adjusting your search criteria.</p>
                </div>
            `,
            info: "Showing _START_ to _END_ of _TOTAL_ courses",
            infoEmpty: "No courses available",
            infoFiltered: "(filtered from _MAX_ total courses)",
            lengthMenu: "Show _MENU_ courses per page",
            search: "Search courses:",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        drawCallback: function() {
            $('[data-toggle="tooltip"], [title]').tooltip();
            updateBulkActionButtons();
        },
        initComplete: function() {
            console.log('DataTable initialized successfully');
            $('[data-toggle="tooltip"], [title]').tooltip();
        }
    });

    // Bind event listeners
    bindEventListeners();

    console.log('Courses Index - Initialization complete');
});

function bindEventListeners() {
    // Select all checkbox
    $(document).on('change', '#selectAll', function() {
        $('.course-checkbox').prop('checked', this.checked);
        updateBulkActionButtons();
    });

    // Individual checkbox change
    $(document).on('change', '.course-checkbox', function() {
        const totalCheckboxes = $('.course-checkbox').length;
        const checkedCheckboxes = $('.course-checkbox:checked').length;

        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
        updateBulkActionButtons();
    });
}

function updateBulkActionButtons() {
    const checkedCount = $('.course-checkbox:checked').length;
    const bulkActionButtons = $('.bulk-action-btn');

    if (checkedCount > 0) {
        bulkActionButtons.removeClass('disabled').prop('disabled', false);
        $('.bulk-selection-count').text(`(${checkedCount} selected)`);
    } else {
        bulkActionButtons.addClass('disabled').prop('disabled', true);
        $('.bulk-selection-count').text('');
    }
}

function applyFilters() {
    if (coursesTable) {
        coursesTable.ajax.reload();
        showToast('info', 'Filters applied successfully!');
    }
}

function resetFilters() {
    $('#statusFilter, #levelFilter, #categoryFilter, #featuredFilter').val('');
    if (coursesTable) {
        coursesTable.ajax.reload();
        showToast('info', 'Filters reset successfully!');
    }
}

function refreshTable() {
    if (coursesTable) {
        coursesTable.ajax.reload();
        showToast('success', 'Table refreshed successfully!');
    }
}

function quickView(courseId) {
    $('#quickViewModal').modal('show');

    $('#quickViewContent').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Loading course details...</p>
        </div>
    `);

    $.ajax({
        url: `/admin/courses/${courseId}`,
        type: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            $('#quickViewContent').html(response);
            $('[data-toggle="tooltip"]').tooltip();
        },
        error: function(xhr, status, error) {
            console.error('Error loading course details:', error);
            $('#quickViewContent').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Error loading course details. Please try again.
                </div>
            `);
        }
    });
}

function deleteCourse(courseId) {
    if (!confirm('⚠️ Are you sure you want to delete this course?\n\nThis action cannot be undone.')) {
        return;
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: `/admin/courses/${courseId}`,
        type: 'DELETE',
        success: function(response) {
            coursesTable.ajax.reload();
            showToast('success', response.message || 'Course deleted successfully!');
        },
        error: function(xhr) {
            let message = 'Error deleting course.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showToast('error', message);
        }
    });
}

function toggleFeatured(courseId) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: `/admin/courses/${courseId}/toggle-featured`,
        type: 'POST',
        success: function(response) {
            coursesTable.ajax.reload();
            showToast('success', response.message);
        },
        error: function(xhr) {
            let message = 'Error updating course.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showToast('error', message);
        }
    });
}

function handleBulkAction(action) {
    const selectedIds = $('.course-checkbox:checked').map(function() {
        return $(this).val();
    }).get();

    if (selectedIds.length === 0) {
        showToast('warning', 'Please select at least one course.');
        return;
    }

    let confirmText = `Are you sure you want to ${action} ${selectedIds.length} course(s)?`;
    if (action === 'delete') {
        confirmText = `⚠️ Are you sure you want to delete ${selectedIds.length} course(s)?\n\nThis action cannot be undone.`;
    }

    if (!confirm(confirmText)) {
        return;
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: '{{ route("admin.courses.bulk-action") }}',
        type: 'POST',
        data: {
            action: action,
            ids: selectedIds
        },
        success: function(response) {
            coursesTable.ajax.reload();
            $('#selectAll').prop('checked', false);
            $('.course-checkbox').prop('checked', false);
            updateBulkActionButtons();
            showToast('success', response.message);
        },
        error: function(xhr) {
            let message = 'Error performing bulk action.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showToast('error', message);
        }
    });
}

function showToast(type, message) {
    const alertClass = type === 'success' ? 'alert-success' :
                      type === 'error' ? 'alert-danger' :
                      type === 'warning' ? 'alert-warning' : 'alert-info';

    const icon = type === 'success' ? 'fas fa-check-circle' :
                 type === 'error' ? 'fas fa-exclamation-triangle' :
                 type === 'warning' ? 'fas fa-exclamation-circle' : 'fas fa-info-circle';

    const toast = $(`
        <div class="position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.2); min-width: 300px;">
                <i class="${icon} mr-2"></i>${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        </div>
    `);

    $('body').append(toast);

    setTimeout(() => {
        toast.find('.alert').alert('close');
    }, 5000);
}

function toggleFeatured(courseId) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: `/admin/courses/${courseId}/toggle-featured`,
        type: 'POST',
        success: function(response) {
            coursesTable.ajax.reload(null, false);
            showToast('success', 'Course featured status updated!');
        },
        error: function(xhr) {
            showToast('error', 'Error updating course status');
        }
    });
}

function deleteCourse(courseId) {
    if (!confirm('Are you sure you want to delete this course?')) {
        return;
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: `/admin/courses/${courseId}`,
        type: 'DELETE',
        success: function(response) {
            coursesTable.ajax.reload(null, false);
            showToast('success', 'Course deleted successfully!');
        },
        error: function(xhr) {
            showToast('error', 'Error deleting course');
        }
    });
}

function showToast(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';

    const toast = $(`
        <div class="position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
            <div class="alert ${alertClass} alert-dismissible fade show">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        </div>
    `);

    $('body').append(toast);
    setTimeout(() => toast.find('.alert').alert('close'), 3000);
}

</script>
@endsection
