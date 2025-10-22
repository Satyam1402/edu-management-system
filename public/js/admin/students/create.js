/**
 * Student Create Form - Simple Version
 * Form validation, auto-resize textareas, and smart defaults
 */

$(document).ready(function() {
    // Auto-resize textareas
    $('textarea').each(function() {
        this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
    }).on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Form validation before submission
    $('#studentForm').on('submit', function(e) {
        let isValid = true;
        
        // Check required fields
        const requiredFields = ['name', 'email', 'phone', 'date_of_birth', 'gender', 'address', 'city', 'state', 'pincode', 'franchise_id', 'status'];
        
        requiredFields.forEach(function(fieldName) {
            const field = $(`#${fieldName}`);
            if (!field.val()) {
                field.addClass('is-invalid');
                isValid = false;
            } else {
                field.removeClass('is-invalid').addClass('is-valid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 100
            }, 500);
        }
    });

    // Form validation with visual feedback
    $('.form-control-enhanced').on('blur', function() {
        const $this = $(this);
        if ($this.val() && this.checkValidity()) {
            $this.removeClass('is-invalid').addClass('is-valid');
        } else if ($this.val()) {
            $this.removeClass('is-valid').addClass('is-invalid');
        } else {
            $this.removeClass('is-valid is-invalid');
        }
    });

    // Phone number formatting
    $('#phone, #guardian_phone').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length > 15) {
            value = value.substring(0, 15);
        }
        $(this).val(value);
    });

    // Pincode formatting
    $('#pincode').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length > 6) {
            value = value.substring(0, 6);
        }
        $(this).val(value);
    });

    // Date validation
    $('#date_of_birth').on('change', function() {
        const birthDate = new Date($(this).val());
        const today = new Date();
        
        if (birthDate >= today) {
            $(this).addClass('is-invalid');
            showAlert('error', 'Birth date cannot be today or in the future.');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
});

// Global reset function
function resetForm() {
    if (confirm('Are you sure you want to reset all form data?')) {
        $('#studentForm')[0].reset();
        $('.form-control').removeClass('is-valid is-invalid');
        
        // Re-size textareas after reset
        $('textarea').each(function() {
            this.style.height = 'auto';
            this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
        });
    }
}

// Show alert function
function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show mt-3" role="alert">
            <i class="${icon} mr-2"></i>${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `;
    
    $('body').prepend(alertHtml);
    
    setTimeout(() => {
        $('.alert').alert('close');
    }, 4000);
}
