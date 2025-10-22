
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
});

// Global reset function
function resetForm() {
    if (confirm('Are you sure you want to reset all changes?')) {
        location.reload();
    }
}
