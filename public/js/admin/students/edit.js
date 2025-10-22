/**
 * Student Edit Form - Simple Version
 * Auto-resize textareas, validation, and reset functionality
 */

$(document).ready(function() {
    // Auto-resize textareas
    $('textarea').each(function() {
        this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
    }).on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Form validation feedback
    $('.form-control').on('blur', function() {
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
});

// Global reset function
function resetForm() {
    if (confirm('Are you sure you want to reset all changes?')) {
        location.reload();
    }
}
