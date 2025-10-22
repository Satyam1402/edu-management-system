/**
 * Franchise Create Form - Simple Version
 * User toggle, auto-generation, validation, and textarea resize
 */

$(document).ready(function() {
    // Toggle user fields based on checkbox
    $('#create_user').on('change', function() {
        const userFields = $('#user-fields');
        const userNameField = $('#user_name');
        const userEmailField = $('#user_email');
        
        if (this.checked) {
            userFields.slideDown(300);
            userNameField.attr('required', true);
            userEmailField.attr('required', true);
        } else {
            userFields.slideUp(300);
            userNameField.attr('required', false);
            userEmailField.attr('required', false);
        }
    });

    // Auto-generate franchise code from name
    $('#name').on('input', function() {
        const name = $(this).val();
        const code = name.toUpperCase()
            .replace(/[^A-Z0-9\s]/g, '')
            .split(' ')
            .map(word => word.substring(0, 3))
            .join('')
            .substring(0, 6);
        
        if (code && !$('#code').val()) {
            $('#code').val(code + '001');
        }
    });

    // Copy email to user email if empty
    $('#email').on('blur', function() {
        const email = $(this).val();
        if (email && !$('#user_email').val()) {
            $('#user_email').val(email);
        }
    });

    // Form validation before submission
    $('#franchiseForm').on('submit', function(e) {
        let isValid = true;
        
        // Check required fields
        const requiredFields = ['name', 'code', 'email', 'phone', 'status'];
        
        requiredFields.forEach(function(fieldName) {
            const field = $(`#${fieldName}`);
            if (!field.val()) {
                field.addClass('is-invalid');
                isValid = false;
            } else {
                field.removeClass('is-invalid').addClass('is-valid');
            }
        });

        // Check user fields if checkbox is checked
        if ($('#create_user').is(':checked')) {
            const userFields = ['user_name', 'user_email'];
            userFields.forEach(function(fieldName) {
                const field = $(`#${fieldName}`);
                if (!field.val()) {
                    field.addClass('is-invalid');
                    isValid = false;
                } else {
                    field.removeClass('is-invalid').addClass('is-valid');
                }
            });
        }

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

    // Enhanced textarea auto-resize
    $('textarea').each(function() {
        this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
    }).on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
});

// Global reset function
function resetForm() {
    if (confirm('Are you sure you want to reset all form data?')) {
        $('#franchiseForm')[0].reset();
        $('#user-fields').slideDown(300);
        $('#create_user').prop('checked', true);
        $('.form-control').removeClass('is-valid is-invalid');
    }
}
