function toggleFeatured(courseId) {
    $.post(`/admin/courses/${courseId}/toggle-featured`, {
        _token: '{{ csrf_token() }}'
    }).done(function(response) {
        alert('Featured status updated!');
    }).fail(function() {
        alert('Error updating status');
    });
}

function deleteCourse(courseId) {
    if (!confirm('Are you sure you want to delete this course?')) return;
    
    $.ajax({
        url: `/admin/courses/${courseId}`,
        type: 'DELETE',
        data: { _token: '{{ csrf_token() }}' },
        success: function() {
            alert('Course deleted!');
            window.location.href = '{{ route("admin.courses.index") }}';
        },
        error: function() {
            alert('Error deleting course');
        }
    });
}