$(document).ready(function() {
    // Initialize Select2
    $('#student_id, #course_id').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: function() {
            return $(this).data('placeholder');
        }
    });

    // Status card selection
    $('.status-card').click(function() {
        $('.status-card').removeClass('active');
        $(this).addClass('active');
        
        const status = $(this).data('status');
        $('#status').val(status);
        
        updatePreviewStatus(status);
    });

    // Student selection change
    $('#student_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        const studentName = selectedOption.text().split(' - ')[0];
        const studentEmail = selectedOption.data('email');
        
        if (selectedOption.val()) {
            $('#studentInfo').show();
            $('#studentDetails').html(`
                <div><strong>Name:</strong> ${studentName}</div>
                <div><strong>Email:</strong> ${studentEmail}</div>
            `);
            $('#previewStudentName').text(studentName);
        } else {
            $('#studentInfo').hide();
            $('#previewStudentName').text('Student Name');
        }
        
        generatePreviewCertNumber();
    });

    // Course selection change
    $('#course_id').change(function() {
        const courseName = $(this).find('option:selected').text();
        
        if ($(this).val()) {
            $('#previewCourseName').text(courseName);
        } else {
            $('#previewCourseName').text('Course Name');
        }
    });

    function updatePreviewStatus(status) {
        const statusBadges = {
            'requested': '<span class="badge badge-warning">Requested</span>',
            'approved': '<span class="badge badge-success">Approved</span>',
            'issued': '<span class="badge badge-primary">Issued</span>'
        };
        
        $('#previewStatus').html(statusBadges[status]);
    }

    function generatePreviewCertNumber() {
        const randomStr = Math.random().toString(36).substring(2, 10).toUpperCase();
        $('#previewCertNumber').text(`CERT-${randomStr}`);
    }

    // Form submission
    $('#certificateForm').submit(function(e) {
        const studentId = $('#student_id').val();
        const courseId = $('#course_id').val();
        
        if (!studentId || !courseId) {
            e.preventDefault();
            alert('Please select both student and course.');
            return false;
        }
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Creating...');
    });

    // Initial preview setup
    generatePreviewCertNumber();
});