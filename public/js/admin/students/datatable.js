/**
 * Students DataTable Configuration
 * Contains all DataTable settings and column definitions
 */

// DataTable configuration object
studentsTableConfig = {
    processing: true,
    serverSide: true,
    responsive: false,
    scrollX: true,
    
    // AJAX configuration
    ajax: {
        url: "/admin/students", // This will be set dynamically in the blade
        type: "GET",
        data: function (d) {
            d.status = $('#statusFilter').val();
            d.franchise = $('#franchiseFilter').val();
            d.course = $('#courseFilter').val();
            d.date_range = $('#dateFilter').val();
        },
        error: function(xhr, error, code) {
            console.error('DataTable AJAX Error:', error);
            showToast('error', 'Error loading students data. Please refresh the page.');
        }
    },
    
    // Column definitions
    columns: [
        {
            data: 'checkbox',
            name: 'checkbox',
            title: '<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="selectAll"><label class="custom-control-label" for="selectAll"></label></div>',
            orderable: false,
            searchable: false,
            className: 'text-center',
            width: '50px',
            render: function(data, type, row) {
                return `
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input student-checkbox" id="student_${row.id}" value="${row.id}">
                        <label class="custom-control-label" for="student_${row.id}"></label>
                    </div>
                `;
            }
        },
        {
            data: 'student_details',
            name: 'name',
            title: 'Student Details',
            width: '250px',
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
            title: 'Student ID',
            width: '120px',
            render: function(data, type, row) {
                return `<span class="badge badge-primary" style="font-size: 12px; padding: 8px 12px;">${data}</span>`;
            }
        },
        {
            data: 'contact_info',
            name: 'email',
            title: 'Contact Info',
            width: '200px',
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
            title: 'Location',
            width: '150px',
            render: function(data, type, row) {
                return `
                    <div>
                        <div class="font-weight-bold">${row.city || 'N/A'}</div>
                        <small class="text-muted">${row.state || ''} ${row.pincode ? '- ' + row.pincode : ''}</small>
                    </div>
                `;
            }
        },
        {
            data: 'academic_info',
            name: 'franchise.name',
            title: 'Academic Info',
            width: '200px',
            render: function(data, type, row) {
                return `
                    <div>
                        <div class="mb-1">
                            <i class="fas fa-building contact-icon"></i>
                            <small class="font-weight-bold">${row.franchise_name || 'Not assigned'}</small>
                        </div>
                        <div>
                            <i class="fas fa-book contact-icon"></i>
                            <small>${row.course_name || 'No course'}</small>
                        </div>
                    </div>
                `;
            }
        },
        {
            data: 'status_badge',
            name: 'status',
            title: 'Status',
            className: 'text-center',
            width: '100px',
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
            title: 'Enrollment',
            className: 'text-center',
            width: '120px',
            render: function(data, type, row) {
                return `
                    <div>
                        <div class="font-weight-bold">${row.enrollment_date || 'N/A'}</div>
                        <small class="text-muted">${row.days_since_enrollment || ''}</small>
                    </div>
                `;
            }
        },
        {
            data: 'actions',
            name: 'actions',
            title: 'Actions',
            orderable: false,
            searchable: false,
            className: 'text-center',
            width: '150px',
            render: function(data, type, row) {
                return `
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-info btn-sm" onclick="quickView(${row.id})" title="Quick View" data-toggle="tooltip">
                            <i class="fas fa-eye"></i>
                        </button>
                        <a href="/admin/students/${row.id}" class="btn btn-outline-success btn-sm" title="View Details" data-toggle="tooltip">
                            <i class="fas fa-user"></i>
                        </a>
                        <a href="/admin/students/${row.id}/edit" class="btn btn-outline-primary btn-sm" title="Edit" data-toggle="tooltip">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-outline-danger btn-sm" onclick="deleteStudent(${row.id})" title="Delete" data-toggle="tooltip">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
            }
        }
    ],
    
    // Table settings
    order: [[7, 'desc']], // Order by enrollment date
    pageLength: 25,
    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
    
    // DOM layout
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
         '<"row"<"col-sm-12"tr>>' +
         '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
    
    // Language settings
    language: {
        processing: '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><p class="mt-3">Loading students...</p></div>',
        emptyTable: `
            <div class="text-center py-5">
                <i class="fas fa-users fa-4x text-muted mb-4"></i>
                <h4 class="text-muted">No Students Found</h4>
                <p class="text-muted mb-4">Start building your student database!</p>
                <a href="/admin/students/create" class="btn btn-success btn-lg">
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
    },
    
    // Callbacks
    initComplete: function(settings, json) {
        console.log('DataTable initialized successfully');
        // Initialize tooltips after table is loaded
        $('[data-toggle="tooltip"]').tooltip();
    },
    
    drawCallback: function(settings) {
        // Re-initialize tooltips after each redraw
        $('[data-toggle="tooltip"]').tooltip();
        
        // Update bulk action buttons based on current selections
        updateBulkActionButtons();
    },
    
    preDrawCallback: function(settings) {
        // Code to run before each draw
        return true;
    }
};
