// Certificate Show Page JavaScript - FIXED VERSION
$(document).ready(function() {
    console.log('Certificate show page loaded');
    
    // Initialize animations
    initializeAnimations();
    
    // Add CSRF token to all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});

function approveCertificate(certificateId) {
    if (!confirm('Are you sure you want to approve this certificate?')) return;
    
    // Show loading state
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Approving...';
    button.disabled = true;
    
    $.ajax({
        url: `/admin/certificates/${certificateId}/approve`,
        type: 'POST',
        data: { 
            _token: $('meta[name="csrf-token"]').attr('content'),
            _method: 'POST'
        },
        success: function(response) {
            showToast('success', response.message || 'Certificate approved successfully!');
            setTimeout(() => {
                location.reload();
            }, 1500);
        },
        error: function(xhr) {
            console.error('Approval error:', xhr);
            let message = 'Error approving certificate';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            } else if (xhr.status === 404) {
                message = 'Certificate not found';
            } else if (xhr.status === 403) {
                message = 'Not authorized to approve certificates';
            }
            
            showToast('error', message);
            
            // Restore button
            button.innerHTML = originalText;
            button.disabled = false;
        }
    });
}

function issueCertificate(certificateId) {
    if (!confirm('Are you sure you want to issue this certificate?')) return;
    
    // Show loading state
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Issuing...';
    button.disabled = true;
    
    $.ajax({
        url: `/admin/certificates/${certificateId}/issue`,
        type: 'POST',
        data: { 
            _token: $('meta[name="csrf-token"]').attr('content'),
            _method: 'POST'
        },
        success: function(response) {
            showToast('success', response.message || 'Certificate issued successfully!');
            setTimeout(() => {
                location.reload();
            }, 1500);
        },
        error: function(xhr) {
            console.error('Issue error:', xhr);
            let message = 'Error issuing certificate';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            } else if (xhr.status === 404) {
                message = 'Certificate not found';
            } else if (xhr.status === 403) {
                message = 'Not authorized to issue certificates';
            }
            
            showToast('error', message);
            
            // Restore button
            button.innerHTML = originalText;
            button.disabled = false;
        }
    });
}

function downloadPDF(certificateId) {
    showToast('info', 'Generating PDF certificate...');
    
    // Create download URL
    const downloadUrl = `/admin/certificates/${certificateId}/download`;
    
    // Method 1: Direct download (works better for PDF files)
    const link = document.createElement('a');
    link.href = downloadUrl;
    link.download = `certificate-${certificateId}.pdf`;
    link.style.display = 'none';
    document.body.appendChild(link);
    
    // Add event listeners to handle success/error
    link.addEventListener('click', function() {
        setTimeout(() => {
            showToast('success', 'PDF download started!');
        }, 500);
    });
    
    // Trigger download
    link.click();
    
    // Cleanup
    setTimeout(() => {
        document.body.removeChild(link);
    }, 100);
    
    // Fallback method using window.open
    setTimeout(() => {
        // Check if download failed by trying to fetch the URL
        fetch(downloadUrl, { method: 'HEAD' })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Download failed');
                }
                // If we reach here, the download should have worked
            })
            .catch(error => {
                console.error('Download error:', error);
                // Fallback: open in new window
                window.open(downloadUrl, '_blank');
            });
    }, 1000);
}

function enhancedPrint() {
    // Add print-specific styles
    const printStyles = `
        <style type="text/css" media="print" id="printStyles">
            @page { 
                margin: 0.5in; 
                size: A4;
            }
            
            body { 
                font-family: 'Arial', sans-serif !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .certificate-display { 
                background: white !important; 
                box-shadow: none !important;
                page-break-inside: avoid;
                margin: 0 !important;
                padding: 10px !important;
            }
            
            .certificate-inner {
                border: 3px solid #000 !important;
                box-shadow: none !important;
                background: white !important;
                page-break-inside: avoid;
            }
            
            .certificate-badge {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .text-success {
                color: #28a745 !important;
                -webkit-print-color-adjust: exact;
            }
            
            .text-primary {
                color: #007bff !important;
                -webkit-print-color-adjust: exact;
            }
            
            /* Hide all non-certificate elements */
            .no-print {
                display: none !important;
            }
            
            /* Ensure only certificate shows */
            .col-lg-4 {
                display: none !important;
            }
            
            .col-lg-8 {
                width: 100% !important;
                max-width: 100% !important;
            }
        </style>
    `;
    
    // Remove existing print styles
    $('#printStyles').remove();
    
    // Add new print styles
    $('head').append(printStyles);
    
    // Small delay to ensure styles are applied
    setTimeout(() => {
        window.print();
        
        // Remove print styles after printing
        setTimeout(() => {
            $('#printStyles').remove();
        }, 1000);
    }, 100);
}

function printCertificate() {
    enhancedPrint();
}

function showToast(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'error' ? 'alert-danger' : 'alert-info';
    
    const iconClass = type === 'success' ? 'check-circle' : 
                      type === 'error' ? 'exclamation-triangle' : 'info-circle';
    
    const toast = $(`
        <div class="position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
            <div class="alert ${alertClass} alert-dismissible fade show shadow-lg" style="min-width: 300px; border-radius: 10px; border: none;">
                <i class="fas fa-${iconClass} mr-2"></i>
                <strong>${message}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    `);
    
    $('body').append(toast);
    
    // Auto dismiss after 4 seconds
    setTimeout(() => {
        toast.find('.alert').alert('close');
    }, 4000);
    
    // Remove from DOM after animation
    setTimeout(() => {
        toast.remove();
    }, 4500);
}

function initializeAnimations() {
    // Animate info sections on load
    $('.info-section').each(function(index) {
        $(this).css('opacity', '0').css('transform', 'translateY(20px)');
        $(this).delay(index * 100).animate({
            opacity: 1
        }, 500, function() {
            $(this).css('transform', 'translateY(0)');
        });
    });
    
    // Add hover effects to action buttons
    $('.action-buttons .btn').hover(
        function() {
            $(this).addClass('shadow-sm').css('transform', 'translateY(-2px)');
        },
        function() {
            $(this).removeClass('shadow-sm').css('transform', 'translateY(0)');
        }
    );
    
    // Animate timeline items
    $('.timeline-item').each(function(index) {
        $(this).css('opacity', '0').css('transform', 'translateX(-20px)');
        $(this).delay(index * 200).animate({
            opacity: 1
        }, 500, function() {
            $(this).css('transform', 'translateX(0)');
        });
    });
}

// Export function for certificate list page
function exportCertificates() {
    showToast('info', 'Preparing certificate export...');
    
    // Create export URL
    const exportUrl = '/admin/certificates/export';
    
    // Try to download
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = 'certificates-export.xlsx';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    setTimeout(() => {
        showToast('success', 'Export completed successfully!');
    }, 2000);
}

// Keyboard shortcuts
$(document).keydown(function(e) {
    // Ctrl + P for print
    if (e.ctrlKey && e.keyCode === 80) {
        e.preventDefault();
        if ($('.btn-info:contains("Print")').length > 0) {
            enhancedPrint();
        }
    }
    
    // Ctrl + D for download
    if (e.ctrlKey && e.keyCode === 68) {
        e.preventDefault();
        const downloadBtn = $('.btn-secondary:contains("Download")');
        if (downloadBtn.length > 0) {
            const certificateId = downloadBtn.attr('onclick').match(/\d+/)[0];
            downloadPDF(certificateId);
        }
    }
});

// Handle print button clicks with different methods
$(document).on('click', '[onclick*="window.print"]', function(e) {
    e.preventDefault();
    enhancedPrint();
});

$(document).on('click', '[onclick*="enhancedPrint"]', function(e) {
    e.preventDefault();
    enhancedPrint();
});

// Debug function to check if everything is loaded
function debugCertificatePage() {
    console.log('=== Certificate Page Debug ===');
    console.log('jQuery loaded:', typeof $ !== 'undefined');
    console.log('CSRF token:', $('meta[name="csrf-token"]').attr('content'));
    console.log('Print buttons:', $('.btn-info:contains("Print")').length);
    console.log('Download buttons:', $('.btn-secondary:contains("Download")').length);
    console.log('=== End Debug ===');
}

// Call debug function
$(document).ready(function() {
    debugCertificatePage();
});
