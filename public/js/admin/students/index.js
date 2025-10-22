/**
 * Students Index Page JavaScript
 * Handles all functionality for the students listing page
 */

// Global variables
let studentsTable;
let studentsTableConfig;

/**
 * Initialize the page when document is ready
 */
$(document).ready(function() {
    // Initialize DataTable
    initializeStudentsDataTable();
    
    // Bind event listeners
    bindEventListeners();
    
    // Initialize tooltips
    initializeTooltips();
});

/**
 * Initialize the students DataTable
 */
function initializeStudentsDataTable() {
    studentsTable = $('#studentsTable').DataTable(studentsTableConfig);
}

/**
 * Bind all event listeners
 */
function bindEventListeners() {
    // Select all checkbox functionality
    $('#selectAll').on('change', handleSelectAllChange);
    
    // Individual checkbox change
    $(document).on('change', '.student-checkbox', handleIndividualCheckboxChange);
    
    // Filter form changes
    $('#statusFilter, #franchiseFilter, #courseFilter, #dateFilter').on('change', function() {
        // Auto-apply filters when changed (optional)
        // applyFilters();
    });
}

/**
 * Initialize Bootstrap tooltips
 */
function initializeTooltips() {
    $('[data-toggle="tooltip"]').tooltip();
}

/**
 * Handle select all checkbox change
 */
function handleSelectAllChange() {
    const isChecked = $(this).is(':checked');
    $('.student-checkbox').prop('checked', isChecked);
    updateBulkActionButtons();
}

/**
 * Handle individual checkbox change
 */
function handleIndividualCheckboxChange() {
    const totalCheckboxes = $('.student-checkbox').length;
    const checkedCheckboxes = $('.student-checkbox:checked').length;
    
    // Update select all checkbox state
    $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
    $('#selectAll').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
    
    updateBulkActionButtons();
}

/**
 * Update bulk action buttons based on selection
 */
function updateBulkActionButtons() {
    const checkedCount = $('.student-checkbox:checked').length;
    const bulkActionButtons = $('.bulk-action-btn');
    
    if (checkedCount > 0) {
        bulkActionButtons.removeClass('disabled').prop('disabled', false);
        $('.bulk-selection-count').text(`(${checkedCount} selected)`);
    } else {
        bulkActionButtons.addClass('disabled').prop('disabled', true);
        $('.bulk-selection-count').text('');
    }
}

/**
 * Apply filters to the DataTable
 */
function applyFilters() {
    if (studentsTable) {
        studentsTable.ajax.reload(function(json) {
            showToast('info', 'Filters applied successfully!');
            updateStatistics(); // Update statistics after filtering
        });
    }
}

/**
 * Reset all filters
 */
function resetFilters() {
    $('#statusFilter, #franchiseFilter, #courseFilter, #dateFilter').val('');
    
    if (studentsTable) {
        studentsTable.ajax.reload(function(json) {
            showToast('info', 'Filters reset successfully!');
            updateStatistics();
        });
    }
}

/**
 * Refresh the DataTable
 */
function refreshTable() {
    if (studentsTable) {
        studentsTable.ajax.reload(function(json) {
            showToast('success', 'Table refreshed successfully!');
        });
    }
}

/**
 * Show quick view modal for a student
 */
function quickView(studentId) {
    if (!studentId) {
        showToast('error', 'Invalid student ID');
        return;
    }
    
    // Show modal
    $('#quickViewModal').modal('show');
    
    // Show loading state
    $('#quickViewContent').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Loading student details...</p>
        </div>
    `);
    
    // Fetch student data
    $.ajax({
        url: `/admin/students/${studentId}`,
        type: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            $('#quickViewContent').html(response);
            initializeTooltips(); // Re-initialize tooltips for new content
        },
        error: function(xhr, status, error) {
            console.error('Error loading student details:', error);
            $('#quickViewContent').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Error loading student details. Please try again.
                </div>
            `);
        }
    });
}

/**
 * Delete a student
 */
function deleteStudent(studentId) {
    if (!studentId) {
        showToast('error', 'Invalid student ID');
        return;
    }
    
    // Show confirmation dialog
    if (!confirm('⚠️ Are you sure you want to delete this student?\n\nThis action cannot be undone and will remove all student records.')) {
        return;
    }
    
    // Setup CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Show loading toast
    showToast('info', 'Deleting student...');
    
    // Send delete request
    $.ajax({
        url: `/admin/students/${studentId}`,
        type: 'DELETE',
        success: function(response) {
            // Reload table
            studentsTable.ajax.reload();
            
            // Show success message
            showToast('success', response.message || 'Student deleted successfully!');
            
            // Update statistics
            updateStatistics();
        },
        error: function(xhr) {
            let message = 'Error deleting student.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showToast('error', message);
        }
    });
}

/**
 * Handle bulk actions
 */
function handleBulkAction(action) {
    const selectedIds = $('.student-checkbox:checked').map(function() {
        return $(this).val();
    }).get();

    if (selectedIds.length === 0) {
        showToast('warning', 'Please select at least one student.');
        return;
    }

    let confirmText = '';
    let successText = '';
    
    switch(action) {
        case 'activate':
            confirmText = `Are you sure you want to activate ${selectedIds.length} student(s)?`;
            successText = 'Students activated successfully!';
            break;
        case 'deactivate':
            confirmText = `Are you sure you want to deactivate ${selectedIds.length} student(s)?`;
            successText = 'Students deactivated successfully!';
            break;
        case 'delete':
            confirmText = `⚠️ Are you sure you want to delete ${selectedIds.length} student(s)?\n\nThis action cannot be undone.`;
            successText = 'Students deleted successfully!';
            break;
        default:
            showToast('error', 'Invalid action selected.');
            return;
    }

    if (!confirm(confirmText)) {
        return;
    }

    // Setup CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Show loading
    showToast('info', 'Processing bulk action...');

    // Send bulk action request
    $.ajax({
        url: '/admin/students/bulk-action',
        type: 'POST',
        data: {
            action: action,
            ids: selectedIds
        },
        success: function(response) {
            // Reload table
            studentsTable.ajax.reload();
            
            // Clear selections
            $('#selectAll').prop('checked', false);
            $('.student-checkbox').prop('checked', false);
            updateBulkActionButtons();
            
            // Show success message
            showToast('success', successText);
            
            // Update statistics
            updateStatistics();
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

/**
 * Export students data
 */
function exportStudents(format) {
    if (!format) {
        showToast('error', 'Please specify export format.');
        return;
    }
    
    // Get current filter values
    const filters = {
        status: $('#statusFilter').val(),
        franchise: $('#franchiseFilter').val(),
        course: $('#courseFilter').val(),
        date_range: $('#dateFilter').val()
    };
    
    // Build URL with filters
    const params = new URLSearchParams(filters).toString();
    const url = `/admin/students/export?format=${format}&${params}`;
    
    // Open export URL
    window.open(url, '_blank');
    showToast('info', `Exporting data in ${format.toUpperCase()} format...`);
}

/**
 * Update statistics cards
 */
function updateStatistics() {
    // This would typically make an AJAX call to get updated statistics
    // For now, we'll just reload the page statistics on next page load
    console.log('Statistics update triggered');
}

/**
 * Show toast notification
 */
function showToast(type, message) {
    if (!message) return;
    
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
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        toast.find('.alert').alert('close');
    }, 5000);
}

/**
 * Utility function to format numbers
 */
function formatNumber(num) {
    return new Intl.NumberFormat().format(num);
}

/**
 * Utility function to format dates
 */
function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Export functions for global access
window.applyFilters = applyFilters;
window.resetFilters = resetFilters;
window.refreshTable = refreshTable;
window.quickView = quickView;
window.deleteStudent = deleteStudent;
window.handleBulkAction = handleBulkAction;
window.exportStudents = exportStudents;
