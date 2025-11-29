/**
 * Course Edit Page JavaScript
 * Handles quick actions and AJAX operations
 */

$(document).ready(function() {
    console.log('Course Edit - JavaScript Loaded Successfully');

    // Check if jQuery is available
    if (typeof $ === 'undefined') {
        console.error('jQuery is not loaded!');
        return;
    }

    // Check if CSRF token exists
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    if (!csrfToken) {
        console.error('CSRF token not found in meta tag!');
    } else {
        console.log('CSRF token found');
    }

    // Setup CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });
});

/**
 * Toggle Featured Status
 * @param {number} courseId - The course ID
 */
function toggleFeatured(courseId) {
    console.log('toggleFeatured called for course ID:', courseId);

    if (!courseId) {
        console.error('Course ID is missing!');
        alert('Error: Course ID is missing');
        return;
    }

    // Confirmation dialog
    if (!confirm('Are you sure you want to toggle the featured status of this course?')) {
        console.log('User cancelled the action');
        return;
    }

    console.log('Sending AJAX request to toggle featured status...');

    $.ajax({
        url: `/admin/courses/${courseId}/toggle-featured`,
        type: 'POST',
        dataType: 'json',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            console.log('Sending request...');
        },
        success: function(response) {
            console.log('Success response:', response);

            // Show success message
            const message = response.message || 'Featured status updated successfully!';
            alert(message);

            // Reload the page to reflect changes
            console.log('Reloading page...');
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Error response:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });

            let errorMessage = 'Error updating featured status.';

            // Try to parse error response
            try {
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    errorMessage = xhr.responseText;
                }
            } catch (e) {
                console.error('Error parsing response:', e);
            }

            // Show error message
            alert(errorMessage);
        },
        complete: function() {
            console.log('Request completed');
        }
    });
}

/**
 * Delete Course
 * @param {number} courseId - The course ID
 */
function deleteCourse(courseId) {
    console.log('deleteCourse called for course ID:', courseId);

    if (!courseId) {
        console.error('Course ID is missing!');
        alert('Error: Course ID is missing');
        return;
    }

    // Confirmation dialog with warning
    if (!confirm('⚠️ WARNING: Are you sure you want to delete this course?\n\nThis action CANNOT be undone!\n\nAll associated data will be permanently removed.')) {
        console.log('User cancelled deletion');
        return;
    }

    // Double confirmation for safety
    if (!confirm('Final confirmation: Delete this course permanently?')) {
        console.log('User cancelled on second confirmation');
        return;
    }

    console.log('Sending AJAX request to delete course...');

    $.ajax({
        url: `/admin/courses/${courseId}`,
        type: 'DELETE',
        dataType: 'json',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            console.log('Sending delete request...');
        },
        success: function(response) {
            console.log('Delete success response:', response);

            // Show success message
            const message = response.message || 'Course deleted successfully!';
            alert(message);

            // Redirect to courses index page
            console.log('Redirecting to courses index...');
            window.location.href = '/admin/courses';
        },
        error: function(xhr, status, error) {
            console.error('Delete error response:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });

            let errorMessage = 'Error deleting course.';

            // Try to parse error response
            try {
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    errorMessage = xhr.responseText;
                } else if (xhr.status === 404) {
                    errorMessage = 'Course not found.';
                } else if (xhr.status === 403) {
                    errorMessage = 'You do not have permission to delete this course.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error. Please try again later.';
                }
            } catch (e) {
                console.error('Error parsing delete response:', e);
            }

            // Show error message
            alert(errorMessage);
        },
        complete: function() {
            console.log('Delete request completed');
        }
    });
}

/**
 * Optional: Add smooth scroll to validation errors
 */
$(document).ready(function() {
    // Scroll to first validation error if exists
    const firstError = $('.is-invalid').first();
    if (firstError.length) {
        console.log('Validation error found, scrolling to it');
        $('html, body').animate({
            scrollTop: firstError.offset().top - 100
        }, 500);
    }
});

/**
 * Optional: Add form change detection
 */
let formChanged = false;

$(document).ready(function() {
    // Track form changes
    $('form :input').on('change', function() {
        formChanged = true;
        console.log('Form changed');
    });

    // Warn user before leaving if form changed
    $(window).on('beforeunload', function() {
        if (formChanged) {
            return 'You have unsaved changes. Are you sure you want to leave?';
        }
    });

    // Don't warn on form submit
    $('form').on('submit', function() {
        formChanged = false;
    });
});

// Export functions for testing (optional)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        toggleFeatured,
        deleteCourse
    };
}

console.log('Course Edit JavaScript initialized successfully');
