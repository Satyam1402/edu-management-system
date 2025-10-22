function createUser(franchiseId) {
    $('#createUserModal').modal('show');

    document.getElementById('createUserForm').onsubmit = function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch(`/admin/franchises/${franchiseId}/create-user`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#createUserModal').modal('hide');

                // Show success alert (Bootstrap 4 compatible)
                const alertHtml = `
                    <div class="alert credential-alert alert-dismissible fade show" role="alert">
                        <h5><i class="fas fa-check-circle mr-2"></i>User Created Successfully!</h5>
                        <hr style="border-color: rgba(255,255,255,0.3);">
                        <div class="mt-3">
                            <strong><i class="fas fa-key mr-1"></i>LOGIN CREDENTIALS:</strong><br>
                            <strong>Name:</strong> <code class="credential-code">${data.user.name}</code><br>
                            <strong>Email:</strong> <code class="credential-code">${data.user.email}</code><br>
                            <strong>Password:</strong> <code class="credential-code">${data.user.password}</code><br>
                            <strong>Login URL:</strong> <code class="credential-code">${window.location.origin}/franchise</code>
                        </div>
                        <div class="mt-3">
                            <small>
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                <strong>Important:</strong> Share these credentials securely with the user.
                            </small>
                        </div>
                        <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `;
                document.querySelector('.content .container-fluid').insertAdjacentHTML('afterbegin', alertHtml);

                // Reload after 5 seconds to update user count
                setTimeout(() => location.reload(), 5000);
            } else {
                alert('Error: ' + (data.message || 'Unknown error occurred'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error creating user. Please try again.');
        });
    };
}