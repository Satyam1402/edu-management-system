// Student Show Page JavaScript
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Smooth scroll for anchor links
    $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if( target.length ) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 100
            }, 1000);
        }
    });
});

// Change student status function
function changeStatus(newStatus) {
    if (confirm(`Are you sure you want to mark this student as ${newStatus}?`)) {
        // Show loading indicator
        showLoading();
        
        // Create a form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = window.studentUpdateRoute; // This will be set in blade
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        // Add method
        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'PUT';
        form.appendChild(method);
        
        // Add status
        const status = document.createElement('input');
        status.type = 'hidden';
        status.name = 'status';
        status.value = newStatus;
        form.appendChild(status);
        
        // Add other required fields (copy from current student data)
        const requiredFields = window.studentData; // This will be set in blade
        
        Object.keys(requiredFields).forEach(key => {
            if (requiredFields[key]) {
                const field = document.createElement('input');
                field.type = 'hidden';
                field.name = key;
                field.value = requiredFields[key];
                form.appendChild(field);
            }
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Print student details
function printStudent() {
    // Hide action buttons before printing
    const actionButtons = document.querySelector('.action-buttons');
    if (actionButtons) {
        actionButtons.style.display = 'none';
    }
    
    window.print();
    
    // Show action buttons after printing
    setTimeout(() => {
        if (actionButtons) {
            actionButtons.style.display = 'block';
        }
    }, 1000);
}

// Export student data
function exportStudent() {
    // Show loading
    showLoading('Preparing export...');
    
    // Create export URL
    const exportUrl = window.studentExportRoute; // This will be set in blade
    
    // Create temporary link and click
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = `student_${window.studentData.student_id}_data.pdf`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Hide loading
    hideLoading();
}

// Show loading indicator
function showLoading(message = 'Loading...') {
    // Remove existing loading if any
    hideLoading();
    
    const loading = document.createElement('div');
    loading.id = 'loadingIndicator';
    loading.innerHTML = `
        <div class="d-flex justify-content-center align-items-center" 
             style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
                    background: rgba(0,0,0,0.5); z-index: 9999;">
            <div class="bg-white p-4 rounded shadow text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div>${message}</div>
            </div>
        </div>
    `;
    document.body.appendChild(loading);
}

// Hide loading indicator
function hideLoading() {
    const loading = document.getElementById('loadingIndicator');
    if (loading) {
        loading.remove();
    }
}

// Statistics card animation
function animateStats() {
    $('.stats-number').each(function() {
        const $this = $(this);
        const countTo = $this.text().replace(/[₹,%]/g, '');
        
        if (!isNaN(countTo) && countTo !== '') {
            const countNum = parseFloat(countTo);
            
            $({ countNum: 0 }).animate({
                countNum: countNum
            }, {
                duration: 2000,
                easing: 'linear',
                step: function() {
                    const formattedNum = Math.floor(this.countNum);
                    if ($this.text().includes('₹')) {
                        $this.text('₹' + formattedNum.toLocaleString());
                    } else if ($this.text().includes('%')) {
                        $this.text(formattedNum + '%');
                    } else {
                        $this.text(formattedNum.toLocaleString());
                    }
                },
                complete: function() {
                    if ($this.text().includes('₹')) {
                        $this.text('₹' + countNum.toLocaleString());
                    } else if ($this.text().includes('%')) {
                        $this.text(countNum + '%');
                    } else {
                        $this.text(countNum.toLocaleString());
                    }
                }
            });
        }
    });
}

// Initialize animations when page loads
$(document).ready(function() {
    // Animate stats after a short delay
    setTimeout(() => {
        animateStats();
    }, 500);
});

// Handle responsive menu
function toggleMobileMenu() {
    const mobileMenu = document.querySelector('.mobile-menu');
    if (mobileMenu) {
        mobileMenu.classList.toggle('show');
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl + P for print
    if (e.ctrlKey && e.key === 'p') {
        e.preventDefault();
        printStudent();
    }
    
    // Ctrl + E for edit
    if (e.ctrlKey && e.key === 'e') {
        e.preventDefault();
        const editButton = document.querySelector('a[href*="edit"]');
        if (editButton) {
            editButton.click();
        }
    }
});
