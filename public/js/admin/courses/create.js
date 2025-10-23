$(document).ready(function() {
    // Form progress tracking
    updateFormProgress();
    
    // Auto-generate course code from name
    $('#name').on('input', function() {
        const name = $(this).val();
        if (name && !$('#code').val()) {
            const code = generateCourseCode(name);
            $('#code').val(code);
        }
        updateFormProgress();
    });

    // Add learning outcome
    $(document).on('click', '.add-outcome', function() {
        const outcomeHtml = `
            <div class="learning-outcome-item mb-2">
                <div class="input-group">
                    <input type="text" class="form-control" name="learning_outcomes[]" 
                           placeholder="What will students be able to do after this course?">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-outcome">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#learningOutcomes').append(outcomeHtml);
    });

    // Remove learning outcome
    $(document).on('click', '.remove-outcome', function() {
        $(this).closest('.learning-outcome-item').remove();
    });

    // Add course tag
    $(document).on('click', '.add-tag', function() {
        const tagHtml = `
            <div class="tag-input-item mb-2">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" name="tags[]" placeholder="Add a tag">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-tag">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#courseTags').append(tagHtml);
    });

    // Remove course tag
    $(document).on('click', '.remove-tag', function() {
        $(this).closest('.tag-input-item').remove();
    });

    // Form validation and progress
    $('input, textarea, select').on('change input', function() {
        updateFormProgress();
    });

    // Form submission
    $('#createCourseForm').on('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            showToast('error', 'Please fill in all required fields correctly.');
        }
    });
});

function generateCourseCode(name) {
    const cleanName = name.replace(/[^A-Za-z0-9]/g, '');
    const prefix = cleanName.substring(0, 4).toUpperCase() || 'COURSE';
    const year = new Date().getFullYear().toString().substr(-2);
    const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
    return prefix + year + random;
}

function updateFormProgress() {
    const requiredFields = $('input[required], textarea[required], select[required]');
    let filledFields = 0;
    
    requiredFields.each(function() {
        if ($(this).val().trim()) {
            filledFields++;
        }
    });
    
    const progress = (filledFields / requiredFields.length) * 100;
    $('#formProgress').css('width', progress + '%');
}

function validateForm() {
    let isValid = true;
    const requiredFields = $('input[required], textarea[required], select[required]');
    
    requiredFields.each(function() {
        const field = $(this);
        const value = field.val().trim();
        
        if (!value) {
            field.addClass('is-invalid');
            isValid = false;
        } else {
            field.removeClass('is-invalid').addClass('is-valid');
        }
    });
    
    return isValid;
}

function showToast(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'error' ? 'alert-danger' : 'alert-info';
    
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
    setTimeout(() => toast.find('.alert').alert('close'), 5000);
}