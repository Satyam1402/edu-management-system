@extends('layouts.custom-admin')

@section('title', 'Students')
@section('page-title', 'Student Management')

@section('css')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">

<style>
/* Enhanced DataTable Styling */
.dataTables_wrapper {
    padding: 20px;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    margin-bottom: 20px;
}

.dataTables_wrapper .dataTables_filter input {
    border: 2px solid #e3e6f0;
    border-radius: 10px;
    padding: 8px 15px;
    font-size: 14px;
    width: 300px;
}

.dataTables_wrapper .dataTables_filter input:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

/* Table Styling */
.table {
    font-size: 14px;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

.table thead th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    font-weight: 600;
    font-size: 13px;
    padding: 15px 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table tbody tr {
    border-bottom: 1px solid #f8f9fa;
    transition: all 0.3s ease;
}

.table tbody tr:hover {
    background-color: #f8f9ff;
    transform: scale(1.01);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.1);
}

.table tbody td {
    padding: 15px 12px;
    vertical-align: middle;
    border: none;
}

/* Avatar Styling */
.student-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    margin-right: 12px;
    border: 3px solid #f8f9fa;
    box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
}

/* Badge Styling */
.badge {
    padding: 8px 16px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-success { background: linear-gradient(135deg, #28a745, #20c997); }
.badge-info { background: linear-gradient(135deg, #17a2b8, #6f42c1); }
.badge-warning { background: linear-gradient(135deg, #ffc107, #fd7e14); }
.badge-danger { background: linear-gradient(135deg, #dc3545, #e83e8c); }
.badge-secondary { background: linear-gradient(135deg, #6c757d, #495057); }

/* Action Buttons */
.btn-group-sm .btn {
    padding: 6px 12px;
    font-size: 12px;
    border-radius: 8px;
    margin: 0 2px;
    transition: all 0.3s ease;
}

.btn-outline-info:hover {
    background: #17a2b8;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3);
}

.btn-outline-success:hover {
    background: #28a745;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.btn-outline-primary:hover {
    background: #007bff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}

/* Card Styling */
.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px 15px 0 0;
    padding: 20px;
    border: none;
}

/* Statistics Cards */
.stats-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    border: 2px solid transparent;
    transition: all 0.3s ease;
    margin-bottom: 20px;
}

.stats-card:hover {
    border-color: #667eea;
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    font-size: 24px;
    color: white;
}

.stats-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 5px;
}

.stats-label {
    color: #718096;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Filter Section */
.filter-section {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}

.filter-section .form-control {
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 15px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.filter-section .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

/* Pagination */
.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 8px 16px;
    margin: 0 2px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    background: white;
    color: #4a5568;
    transition: all 0.3s ease;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    color: white;
    transform: translateY(-2px);
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    color: white;
}

/* Contact Info Icons */
.contact-icon {
    color: #667eea;
    margin-right: 8px;
    width: 16px;
}

/* Responsive */
@media (max-width: 768px) {
    .stats-card {
        margin-bottom: 15px;
    }

    .table {
        font-size: 12px;
    }

    .student-avatar {
        width: 35px;
        height: 35px;
    }
}
</style>
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
                            <i class="fas fa-graduation-cap mr-3"></i>Student Management
                        </h2>
                        <p class="mb-0 opacity-75">Manage and track all students across franchises</p>
                    </div>
                    <div class="ml-auto">
                        <a href="{{ route('admin.students.create') }}" class="btn btn-light btn-lg">
                            <i class="fas fa-user-plus mr-2"></i>Add New Student
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-number">{{ \App\Models\Student::count() }}</div>
                <div class="stats-label">Total Students</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-success">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stats-number">{{ \App\Models\Student::where('status', 'active')->count() }}</div>
                <div class="stats-label">Active Students</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-info">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <div class="stats-number">{{ \App\Models\Student::whereMonth('created_at', date('m'))->count() }}</div>
                <div class="stats-label">This Month</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-warning">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stats-number">{{ \App\Models\Franchise::count() }}</div>
                <div class="stats-label">Franchises</div>
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
                            {{-- <option value="graduated">Graduated</option>
                            <option value="dropped">Dropped</option>
                            <option value="suspended">Suspended</option> --}}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label font-weight-bold">Franchise</label>
                        <select id="franchiseFilter" class="form-control">
                            <option value="">All Franchises</option>
                            @foreach(\App\Models\Franchise::all() as $franchise)
                                <option value="{{ $franchise->id }}">{{ $franchise->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label font-weight-bold">Course</label>
                        <select id="courseFilter" class="form-control">
                            <option value="">All Courses</option>
                            @foreach(\App\Models\Course::all() as $course)
                                <option value="{{ $course->id }}">{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label font-weight-bold">Date Range</label>
                        <select id="dateFilter" class="form-control">
                            <option value="">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTable -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <table class="table mb-0" id="studentsTable">
                        <thead>
                            <tr>
                                <th width="50">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="selectAll">
                                        <label class="custom-control-label" for="selectAll"></label>
                                    </div>
                                </th>
                                <th width="250">Student Details</th>
                                <th width="120">Student ID</th>
                                <th width="200">Contact Info</th>
                                <th width="150">Location</th>
                                {{-- <th width="200">Academic Info</th> --}}
                                <th width="100" class="text-center">Status</th>
                                <th width="120" class="text-center">Enrollment</th>
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
<div class="modal fade" id="quickViewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0;">
                <h5 class="modal-title">
                    <i class="fas fa-user mr-2"></i>Student Quick View
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="quickViewContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
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
let studentsTable;

$(document).ready(function() {
    // Initialize DataTable
    studentsTable = $('#studentsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: false, // Disable responsive for better control
        scrollX: true, // Enable horizontal scrolling
        ajax: {
            url: "{{ route('admin.students.index') }}",
            type: "GET",
            data: function (d) {
                d.status = $('#statusFilter').val();
                d.franchise = $('#franchiseFilter').val();
                d.course = $('#courseFilter').val();
                d.date_range = $('#dateFilter').val();
            }
        },
        columns: [
            {
                data: 'checkbox',
                name: 'checkbox',
                orderable: false,
                searchable: false,
                className: 'text-center'
            },
            {
                data: 'student_details',
                name: 'name',
                render: function(data, type, row) {
                    const firstLetter = row.name.charAt(0).toUpperCase();
                    const genderIcon = row.gender === 'male' ? '👨' : row.gender === 'female' ? '👩' : '🧑';

                    return `
                        <div class="d-flex align-items-center">
                            <div class="student-avatar">
                                ${firstLetter}
                            </div>
                            <div>
                                <h6 class="mb-1 font-weight-bold">${row.name}</h6>
                                <small class="text-muted">${genderIcon} ${row.age ? row.age + ' years' : 'Age N/A'}</small>
                            </div>
                        </div>
                    `;
                }
            },
            {
                data: 'student_id',
                name: 'student_id',
                render: function(data) {
                    return `<span class="badge badge-primary" style="font-size: 12px; padding: 8px 12px;">${data}</span>`;
                }
            },
            {
                data: 'contact_info',
                name: 'email',
                render: function(data, type, row) {
                    return `
                        <div>
                            <div class="mb-1">
                                <i class="fas fa-envelope contact-icon"></i>
                                <small>${row.email}</small>
                            </div>
                            <div>
                                <i class="fas fa-phone contact-icon"></i>
                                <small>${row.phone}</small>
                            </div>
                        </div>
                    `;
                }
            },
            {
                data: 'location_info',
                name: 'city',
                render: function(data, type, row) {
                    return `
                        <div>
                            <div class="font-weight-bold">${row.city || 'N/A'}</div>
                            <small class="text-muted">${row.state || ''} ${row.pincode ? '- ' + row.pincode : ''}</small>
                        </div>
                    `;
                }
            },
            // {
            //     data: 'academic_info',
            //     name: 'franchise.name',
            //     render: function(data, type, row) {
            //         return `
            //             <div>
            //                 <div class="mb-1">
            //                     <i class="fas fa-building contact-icon"></i>
            //                     <small class="font-weight-bold">${row.franchise_name || 'Not assigned'}</small>
            //                 </div>
            //                 <div>
            //                     <i class="fas fa-book contact-icon"></i>
            //                     <small>${row.course_name || 'No course'}</small>
            //                 </div>
            //             </div>
            //         `;
            //     }
            // },
            {
                data: 'status_badge',
                name: 'status',
                className: 'text-center',
                render: function(data, type, row) {
                    const statusColors = {
                        'active': 'success',
                        'inactive': 'secondary',
                        'graduated': 'info',
                        'dropped': 'danger',
                        'suspended': 'warning'
                    };
                    const color = statusColors[row.status] || 'secondary';
                    return `<span class="badge badge-${color}">${row.status.charAt(0).toUpperCase() + row.status.slice(1)}</span>`;
                }
            },
            {
                data: 'enrollment_info',
                name: 'enrollment_date',
                className: 'text-center',
                // render: function(data, type, row) {
                //     return `
                //         <div>
                //             <div class="font-weight-bold">${row.enrollment_date || 'N/A'}</div>
                //             <small class="text-muted">${row.days_since_enrollment || ''}</small>
                //         </div>
                //     `;
                // }
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    return `
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-info btn-sm" onclick="quickView(${row.id})" title="Quick View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <a href="/admin/students/${row.id}" class="btn btn-outline-success btn-sm" title="View Details">
                                <i class="fas fa-user"></i>
                            </a>
                            <a href="/admin/students/${row.id}/edit" class="btn btn-outline-primary btn-sm" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-outline-danger btn-sm" onclick="deleteStudent(${row.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[7, 'desc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        language: {
            processing: '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><p class="mt-3">Loading students...</p></div>',
            emptyTable: `
                <div class="text-center py-5">
                    <i class="fas fa-users fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">No Students Found</h4>
                    <p class="text-muted mb-4">Start building your student database!</p>
                    <a href="{{ route('admin.students.create') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-user-plus mr-2"></i>Add First Student
                    </a>
                </div>
            `,
            zeroRecords: `
                <div class="text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">No matching records found</h4>
                    <p class="text-muted">Try adjusting your search criteria.</p>
                </div>
            `,
            info: "Showing _START_ to _END_ of _TOTAL_ students",
            infoEmpty: "No students available",
            infoFiltered: "(filtered from _MAX_ total students)",
            lengthMenu: "Show _MENU_ students per page",
            search: "Search students:",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });

    // Handle select all checkbox
    $('#selectAll').change(function() {
        $('.student-checkbox').prop('checked', this.checked);
    });
});

// Apply filters
function applyFilters() {
    studentsTable.ajax.reload();
    showToast('info', 'Filters applied successfully!');
}

// Reset filters
function resetFilters() {
    $('#statusFilter, #franchiseFilter, #courseFilter, #dateFilter').val('');
    studentsTable.ajax.reload();
    showToast('info', 'Filters reset successfully!');
}

// Quick view function
function quickView(studentId) {
    $('#quickViewModal').modal('show');

    $.ajax({
        url: `/admin/students/${studentId}`,
        type: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            $('#quickViewContent').html(response);
        },
        error: function() {
            $('#quickViewContent').html('<div class="alert alert-danger">Error loading student details.</div>');
        }
    });
}

// Delete student function
function deleteStudent(id) {
    if (confirm('Are you sure you want to delete this student?')) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: `/admin/students/${id}`,
            type: 'DELETE',
            success: function(response) {
                studentsTable.ajax.reload();
                showToast('success', response.message || 'Student deleted successfully!');
            },
            error: function(xhr) {
                showToast('error', xhr.responseJSON?.message || 'Error deleting student');
            }
        });
    }
}

// Toast notification function
function showToast(type, message) {
    const alertClass = type === 'success' ? 'alert-success' :
                      type === 'error' ? 'alert-danger' : 'alert-info';

    const toast = $(`
        <div class="position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
                ${message}
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
</script>
@endsection
