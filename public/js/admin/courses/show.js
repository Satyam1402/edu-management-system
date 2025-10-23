
function toggleFeatured(courseId) {
    $.post(`/admin/courses/${courseId}/toggle-featured`, {
        _token: '{{ csrf_token() }}'
    }).done(function(response) {
        location.reload();
    });
}

function deleteCourse(courseId) {
    if (!confirm('Are you sure?')) return;
    
    $.ajax({
        url: `/admin/courses/${courseId}`,
        type: 'DELETE',
        data: { _token: '{{ csrf_token() }}' },
        success: function() {
            window.location.href = '{{ route("admin.courses.index") }}';
        }
    });
}