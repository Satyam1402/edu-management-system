<?php
// routes/web.php (COMPLETE UPDATED VERSION WITH CERTIFICATE REQUEST ROUTES)

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\FranchiseController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\CertificateRequestController;
use App\Http\Controllers\Admin\WalletController;

// Franchise Controllers
use App\Http\Controllers\Franchise\CourseController as FranchiseCourseController;
use App\Http\Controllers\Franchise\PaymentController as FranchisePaymentController;
use App\Http\Controllers\Franchise\StudentController as FranchiseStudentController;
use App\Http\Controllers\Franchise\EnrollmentController as FranchiseEnrollmentController;
use App\Http\Controllers\Franchise\DashboardController as FranchiseDashboardController;
use App\Http\Controllers\Franchise\CertificateController as FranchiseCertificateController;
use App\Http\Controllers\Franchise\CertificateRequestController as FranchiseCertificateRequestController;
use App\Http\Controllers\Franchise\WalletController as FranchiseWalletController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// =============================================================================
// PUBLIC STUDENT PAYMENT ROUTES (No Authentication Required)
// =============================================================================
Route::get('pay/{token}', [App\Http\Controllers\Admin\PaymentController::class, 'studentPayment'])->name('payment.student');
Route::post('pay/{token}/confirm', [App\Http\Controllers\Admin\PaymentController::class, 'studentConfirmUpi'])->name('payment.student.confirm');

// Main dashboard route - redirects based on role
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes (available to all authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =============================================================================
// SUPER ADMIN ROUTES - Full System Access
// =============================================================================
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // =============================================================================
    // CORE MANAGEMENT RESOURCES
    // =============================================================================

    // Franchise Management (Super Admin Only)
    Route::resource('franchises', FranchiseController::class);
    Route::post('franchises/{franchise}/create-user', [FranchiseController::class, 'createUser'])->name('franchises.create-user');
    Route::post('/franchises/{franchise}/toggle-status', [FranchiseController::class, 'toggleStatus'])->name('franchises.toggle-status');

    // =============================================================================
    // ADMIN COURSE MANAGEMENT (Global Courses)
    // =============================================================================
    Route::resource('courses', CourseController::class);
    Route::post('/courses/{course}/toggle-status', [CourseController::class, 'toggleStatus'])->name('courses.toggle-status');
    Route::post('/courses/{course}/toggle-featured', [CourseController::class, 'toggleFeatured'])->name('courses.toggle-featured');
    Route::post('/courses/{course}/activate', [CourseController::class, 'activate'])->name('courses.activate');
    Route::post('/courses/{course}/deactivate', [CourseController::class, 'deactivate'])->name('courses.deactivate');
    Route::post('/courses/{course}/archive', [CourseController::class, 'archive'])->name('courses.archive');
    Route::post('/courses/bulk-action', [CourseController::class, 'bulkAction'])->name('courses.bulk-action');
    Route::post('/courses/bulk-delete', [CourseController::class, 'bulkDelete'])->name('courses.bulk-delete');
    Route::post('/courses/bulk-status-update', [CourseController::class, 'bulkStatusUpdate'])->name('courses.bulk-status-update');
    Route::get('/courses/export', [CourseController::class, 'export'])->name('courses.export');
    Route::post('/courses/import', [CourseController::class, 'import'])->name('courses.import');
    Route::get('/courses/{course}/analytics', [CourseController::class, 'analytics'])->name('courses.analytics');
    Route::get('/courses/{course}/students', [CourseController::class, 'getStudents'])->name('courses.students');
    Route::get('/courses/category/{category}', [CourseController::class, 'byCategory'])->name('courses.by-category');
    Route::get('/courses/search', [CourseController::class, 'search'])->name('courses.search');

    // =============================================================================
    // STUDENT MANAGEMENT (All Students Across All Franchises)
    // =============================================================================
    Route::resource('students', StudentController::class);
    Route::post('/students/{student}/toggle-status', [StudentController::class, 'toggleStatus'])->name('students.toggle-status');
    Route::post('/students/{student}/activate', [StudentController::class, 'activate'])->name('students.activate');
    Route::post('/students/{student}/deactivate', [StudentController::class, 'deactivate'])->name('students.deactivate');
    Route::post('/students/bulk-action', [StudentController::class, 'bulkAction'])->name('students.bulk-action');
    Route::post('/students/bulk-delete', [StudentController::class, 'bulkDelete'])->name('students.bulk-delete');
    Route::get('/students/export', [StudentController::class, 'export'])->name('students.export');
    Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
    Route::get('/students/{student}/payments', [StudentController::class, 'getPayments'])->name('students.payments');
    Route::get('/students/{student}/certificates', [StudentController::class, 'getCertificates'])->name('students.certificates');
    Route::get('/students/franchise/{franchise}', [StudentController::class, 'byFranchise'])->name('students.by-franchise');
    Route::get('/students/course/{course}', [StudentController::class, 'byCourse'])->name('students.by-course');
    Route::get('/students/search', [StudentController::class, 'search'])->name('students.search');

    // =============================================================================
    // CERTIFICATE MANAGEMENT (Issue & Approve Certificates)
    // =============================================================================
    Route::resource('certificates', CertificateController::class);
    Route::post('/certificates/{certificate}/approve', [CertificateController::class, 'approve'])->name('certificates.approve');
    Route::post('/certificates/{certificate}/reject', [CertificateController::class, 'reject'])->name('certificates.reject');
    Route::post('/certificates/{certificate}/issue', [CertificateController::class, 'issue'])->name('certificates.issue');
    Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificates.download');
    Route::get('certificates-export', [CertificateController::class, 'export'])->name('certificates.export');

    // =============================================================================
    // ADMIN CERTIFICATE REQUEST MANAGEMENT (Approve/Reject Franchise Requests)
    // =============================================================================
    Route::prefix('certificate-requests')->name('certificate-requests.')->group(function () {
        // Main CRUD routes
        Route::get('/', [CertificateRequestController::class, 'index'])->name('index');
        Route::get('/{certificateRequest}', [CertificateRequestController::class, 'show'])->name('show');

        // Admin approval actions
        Route::post('/{certificateRequest}/approve', [CertificateRequestController::class, 'approve'])->name('approve');
        Route::post('/{certificateRequest}/reject', [CertificateRequestController::class, 'reject'])->name('reject');
        Route::post('/{certificateRequest}/process', [CertificateRequestController::class, 'process'])->name('process');

        // Bulk operations
        Route::post('/bulk-action', [CertificateRequestController::class, 'bulkAction'])->name('bulk-action');
        Route::post('/bulk-approve', [CertificateRequestController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [CertificateRequestController::class, 'bulkReject'])->name('bulk-reject');

        // Data exports and stats
        Route::get('/stats/overview', [CertificateRequestController::class, 'getStats'])->name('stats');
        Route::get('/export/csv', [CertificateController::class, 'export'])->name('export');
        Route::get('/export/excel', [CertificateController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [CertificateController::class, 'exportPdf'])->name('export.pdf');

        // Filtering and search
        Route::get('/franchise/{franchise}', [CertificateRequestController::class, 'byFranchise'])->name('by-franchise');
        Route::get('/course/{course}', [CertificateRequestController::class, 'byCourse'])->name('by-course');
        Route::get('/status/{status}', [CertificateRequestController::class, 'byStatus'])->name('by-status');
    });

    Route::prefix('wallet')->name('wallet.')->group(function () {
    // Wallet Dashboard
        Route::get('/', [WalletController::class, 'index'])->name('index');
        
        // All Transactions
        Route::get('/transactions', [WalletController::class, 'transactions'])->name('transactions');
        
        // Recharge Requests
        Route::get('/recharge-requests', [WalletController::class, 'rechargeRequests'])->name('recharge-requests');
        Route::post('/recharge-requests/{id}/approve', [WalletController::class, 'approveRecharge'])->name('approve-recharge');
        Route::post('/recharge-requests/{id}/reject', [WalletController::class, 'rejectRecharge'])->name('reject-recharge');
        
        // Manual Transaction
        Route::get('/manual-transaction', [WalletController::class, 'manualTransaction'])->name('manual-transaction');
        Route::post('/manual-transaction', [WalletController::class, 'processManualTransaction'])->name('process-manual');
        
        // Franchise Wallet Details
        Route::get('/franchise/{id}', [WalletController::class, 'franchiseWallet'])->name('franchise-details');
        
        // Audit Logs
        Route::get('/audit-logs', [WalletController::class, 'auditLogs'])->name('audit-logs');
    });

    // Exam Management
    Route::resource('exams', ExamController::class);
    Route::post('/exams/{exam}/toggle-status', [ExamController::class, 'toggleStatus'])->name('exams.toggle-status');
    Route::get('/exams/{exam}/results', [ExamController::class, 'results'])->name('exams.results');

    // =============================================================================
    // PAYMENT MANAGEMENT (COMPLETE WITH ALL GATEWAYS)
    // =============================================================================
    Route::resource('payments', PaymentController::class);
    Route::get('payments/{payment}/razorpay', [PaymentController::class, 'handleRazorpayPayment'])->name('payments.razorpay');
    Route::get('payments/{payment}/upi', [PaymentController::class, 'handleUpiPayment'])->name('payments.upi');
    Route::post('payments/verify-razorpay', [PaymentController::class, 'verifyRazorpay'])->name('payments.verify-razorpay');
    Route::post('payments/{payment}/confirm-upi', [PaymentController::class, 'confirmUpi'])->name('payments.confirm-upi');
    Route::post('payments/{payment}/mark-completed', [PaymentController::class, 'markAsCompleted'])->name('payments.mark-completed');
    Route::post('payments/{payment}/mark-failed', [PaymentController::class, 'markAsFailed'])->name('payments.mark-failed');
    Route::get('payments/{payment}/mark-paid', [PaymentController::class, 'markAsCompleted'])->name('payments.mark-paid');
    Route::post('payments/{payment}/refund', [PaymentController::class, 'processRefund'])->name('payments.refund');
    Route::get('payments-export', [PaymentController::class, 'export'])->name('payments.export');

    // =============================================================================
    // REPORTS & ANALYTICS (Super Admin Only)
    // =============================================================================
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
        Route::get('/students', [ReportController::class, 'students'])->name('students');
        Route::get('/courses', [ReportController::class, 'courses'])->name('courses');
        Route::get('/franchises', [ReportController::class, 'franchises'])->name('franchises');
        Route::get('/certificates', [ReportController::class, 'certificates'])->name('certificates');
        Route::get('/export/students', [ReportController::class, 'exportStudents'])->name('export.students');
        Route::get('/export/payments', [ReportController::class, 'exportPayments'])->name('export.payments');
    });

    // =============================================================================
    // SYSTEM SETTINGS (Super Admin Only)
    // =============================================================================
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', function() { return view('admin.settings.index'); })->name('index');
        Route::get('/system', function() { return view('admin.settings.system'); })->name('system');
        Route::get('/email', function() { return view('admin.settings.email'); })->name('email');
        Route::get('/backup', function() { return view('admin.settings.backup'); })->name('backup');
    });
});

// =============================================================================
// FRANCHISE ROUTES - COMPLETELY UPDATED & WALLET-INTEGRATED
// =============================================================================
Route::middleware(['auth', 'role:franchise'])->prefix('franchise')->name('franchise.')->group(function () {

    // Franchise Dashboard
    Route::get('/', [FranchiseDashboardController::class, 'index'])->name('dashboard');

    // ==================== FRANCHISE WALLET ROUTES ====================
    Route::prefix('wallet')->name('wallet.')->group(function () {
        // Main wallet management
        Route::get('/', [FranchiseWalletController::class, 'index'])->name('index');
        Route::get('/create', [FranchiseWalletController::class, 'create'])->name('create');
        Route::post('/', [FranchiseWalletController::class, 'store'])->name('store');

        // Payment verification routes
        Route::post('/verify-razorpay', [FranchiseWalletController::class, 'verifyRazorpay'])->name('verify-razorpay');
        Route::get('/verify-razorpay', [FranchiseWalletController::class, 'verifyRazorpay'])->name('verify-razorpay-get');
        Route::post('/verify-upi', [FranchiseWalletController::class, 'verifyUpi'])->name('verify-upi');

        // Transaction details
        Route::get('/transaction/{transaction}', [FranchiseWalletController::class, 'show'])->name('show');
        Route::get('/transaction/{transaction}/receipt', [FranchiseWalletController::class, 'downloadReceipt'])->name('receipt');

        // AJAX routes for real-time balance
        Route::get('/balance', [FranchiseWalletController::class, 'getBalance'])->name('balance');
        Route::get('/transactions/recent', [FranchiseWalletController::class, 'getRecentTransactions'])->name('recent-transactions');
    });

    // =============================================================================
    // FRANCHISE COURSE MANAGEMENT
    // =============================================================================
    Route::prefix('courses')->name('courses.')->group(function () {
        // Main course routes
        Route::get('/', [FranchiseCourseController::class, 'index'])->name('index');
        Route::get('/{course}', [FranchiseCourseController::class, 'show'])->name('show');
        Route::get('/{course}/students', [FranchiseCourseController::class, 'students'])->name('students');

        // AJAX routes for certificate requests
        Route::get('/list', [FranchiseCourseController::class, 'list'])->name('list'); // For AJAX dropdown
        Route::get('/{course}/fee', [FranchiseCourseController::class, 'getFee'])->name('fee'); // Get certificate fee

        // Revenue tracking
        Route::get('/revenue/tracking', [FranchiseCourseController::class, 'revenue'])->name('revenue');
    });

    // =============================================================================
    // FRANCHISE ENROLLMENT MANAGEMENT
    // =============================================================================
    Route::prefix('enrollments')->name('enrollments.')->group(function () {
        Route::post('/store', [FranchiseEnrollmentController::class, 'store'])->name('store');
        Route::get('/my-students', [FranchiseEnrollmentController::class, 'myStudents'])->name('my-students');
        Route::get('/{enrollment}', [FranchiseEnrollmentController::class, 'show'])->name('show');
        Route::patch('/{enrollment}/status', [FranchiseEnrollmentController::class, 'updateStatus'])->name('update-status');
        Route::post('/{enrollment}/certificate', [FranchiseEnrollmentController::class, 'generateCertificate'])->name('generate-certificate');
        Route::get('/{enrollment}/certificate/download', [FranchiseEnrollmentController::class, 'downloadCertificate'])->name('download-certificate');
        Route::delete('/{enrollment}', [FranchiseEnrollmentController::class, 'destroy'])->name('destroy');
    });

    // =============================================================================
    // FRANCHISE STUDENT MANAGEMENT
    // =============================================================================
    Route::resource('students', FranchiseStudentController::class);
    Route::post('/students/{student}/toggle-status', [FranchiseStudentController::class, 'toggleStatus'])->name('students.toggle-status');
    Route::get('students-stats', [FranchiseStudentController::class, 'getStats'])->name('students.stats');
    Route::get('/students/{student}/profile', [FranchiseStudentController::class, 'profile'])->name('students.profile');
    Route::get('/students/{student}/course/{course}', [FranchiseStudentController::class, 'showStudentCourse'])->name('students.show-course');
    Route::get('/students/export', [FranchiseStudentController::class, 'export'])->name('students.export');

    // =============================================================================
    // FRANCHISE CERTIFICATE MANAGEMENT (View & Download Issued Certificates)
    // =============================================================================
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/', [FranchiseCertificateController::class, 'index'])->name('index');
        Route::get('/{certificate}', [FranchiseCertificateController::class, 'show'])->name('show');
        Route::get('/{certificate}/download', [FranchiseCertificateController::class, 'download'])->name('download');
        Route::get('/{certificate}/print', [FranchiseCertificateController::class, 'print'])->name('print');
    });

    // =============================================================================
    // 🚀 UPDATED: FRANCHISE CERTIFICATE REQUEST MANAGEMENT (Wallet-Integrated)
    // =============================================================================
    Route::prefix('certificate-requests')->name('certificate-requests.')->group(function () {
        // Main CRUD operations
        Route::get('/', [FranchiseCertificateRequestController::class, 'index'])->name('index');
        Route::get('/create', [FranchiseCertificateRequestController::class, 'create'])->name('create');
        Route::post('/', [FranchiseCertificateRequestController::class, 'store'])->name('store');
        Route::get('/{certificateRequest}', [FranchiseCertificateRequestController::class, 'show'])->name('show');
        Route::get('/{certificateRequest}/edit', [FranchiseCertificateRequestController::class, 'edit'])->name('edit');
    Route::put('/{certificateRequest}', [FranchiseCertificateRequestController::class, 'update'])->name('update');

        // AJAX routes for dynamic functionality (REQUIRED for our views)
        Route::get('/wallet-balance', [FranchiseCertificateRequestController::class, 'getWalletBalance'])->name('wallet-balance');
        Route::post('/calculate-cost', [FranchiseCertificateRequestController::class, 'calculateCost'])->name('calculate-cost');
        Route::get('/course/{course}/fee', [FranchiseCertificateRequestController::class, 'getCourseFee'])->name('course-fee');

        // Student-specific routes
        Route::get('/student/{student}/create', [FranchiseCertificateRequestController::class, 'createForStudent'])->name('create-for-student');

        // Status management (for pending requests)
        Route::post('/{certificateRequest}/cancel', [FranchiseCertificateRequestController::class, 'cancel'])->name('cancel');
        Route::get('/{certificateRequest}/download', [FranchiseCertificateRequestController::class, 'download'])->name('download');

        // Export and reporting
        Route::get('/export', [FranchiseCertificateRequestController::class, 'export'])->name('export');
        Route::get('/export/excel', [FranchiseCertificateRequestController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [FranchiseCertificateRequestController::class, 'exportPdf'])->name('export.pdf');

        // Statistics for dashboard
        Route::get('/stats/overview', [FranchiseCertificateRequestController::class, 'getStats'])->name('stats');
    });

    // =============================================================================
    // FRANCHISE PAYMENT MANAGEMENT (Simplified & Streamlined)
    // =============================================================================
    Route::prefix('payments')->name('payments.')->group(function () {
        // Basic CRUD Operations
        Route::get('/', [FranchisePaymentController::class, 'index'])->name('index');
        Route::get('/create', [FranchisePaymentController::class, 'create'])->name('create');
        Route::post('/', [FranchisePaymentController::class, 'store'])->name('store');
        Route::get('/{payment}', [FranchisePaymentController::class, 'show'])->name('show');

        // QR Code Payment Routes
        Route::post('/generate-qr', [FranchisePaymentController::class, 'generateQR'])->name('generate-qr');
        Route::post('/generate-payment-qr', [FranchisePaymentController::class, 'generatePaymentQR'])->name('generate-payment-qr');

        // Razorpay Integration
        Route::get('/{payment}/pay', [FranchisePaymentController::class, 'pay'])->name('pay');
        Route::get('/{payment}/razorpay', [FranchisePaymentController::class, 'handleRazorpayPayment'])->name('razorpay');
        Route::post('/verify-razorpay', [FranchisePaymentController::class, 'verifyRazorpay'])->name('verify-razorpay');

        // UPI Payment Routes
        Route::get('/{payment}/upi', [FranchisePaymentController::class, 'handleUpiPayment'])->name('upi');
        Route::post('/{payment}/confirm-upi', [FranchisePaymentController::class, 'confirmUpi'])->name('confirm-upi');

        // Payment Status Management
        Route::post('/{payment}/mark-completed', [FranchisePaymentController::class, 'markAsCompleted'])->name('mark-completed');
        Route::post('/{payment}/mark-failed', [FranchisePaymentController::class, 'markAsFailed'])->name('mark-failed');

        // Student-specific Payment Routes
        Route::get('/student/{student}', [FranchisePaymentController::class, 'byStudent'])->name('by-student');

        // Success/Failure Callbacks
        Route::get('/success', [FranchisePaymentController::class, 'paymentSuccess'])->name('success');
        Route::get('/failed', [FranchisePaymentController::class, 'paymentFailed'])->name('failed');

        // Payment Analytics & Reports
        Route::get('/stats/overview', [FranchisePaymentController::class, 'getStats'])->name('stats');
        Route::get('/recent-payments', [FranchisePaymentController::class, 'getRecentPayments'])->name('recent');

        // Bulk Operations
        Route::post('/bulk/mark-completed', [FranchisePaymentController::class, 'bulkMarkCompleted'])->name('bulk.completed');
        Route::post('/bulk/export', [FranchisePaymentController::class, 'exportPayments'])->name('export');

        // Payment Verification
        Route::post('/verify-qr-payment', [FranchisePaymentController::class, 'verifyQrPayment'])->name('verify-qr');
        Route::get('/{payment}/receipt', [FranchisePaymentController::class, 'generateReceipt'])->name('receipt');
    });

    // =============================================================================
    // REVENUE TRACKING ROUTES
    // =============================================================================
    Route::prefix('revenue')->name('revenue.')->group(function () {
        Route::get('/export', [FranchiseCourseController::class, 'exportRevenue'])->name('export');
        Route::get('/courses/excel', [FranchiseCourseController::class, 'exportCourseRevenueExcel'])->name('courses.excel');
        Route::get('/courses/pdf', [FranchiseCourseController::class, 'exportCourseRevenuePDF'])->name('courses.pdf');
    });

    // =============================================================================
    // FRANCHISE REPORTS (Own Data Only)
    // =============================================================================
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', function() { return view('franchise.reports.index'); })->name('index');
        Route::get('/students', function() { return view('franchise.reports.students'); })->name('students');
        Route::get('/payments', function() { return view('franchise.reports.payments'); })->name('payments');
        Route::get('/certificates', function() { return view('franchise.reports.certificates'); })->name('certificates');
    });

    // =============================================================================
    // FRANCHISE PROFILE & SETTINGS
    // =============================================================================
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', function() { return view('franchise.settings.index'); })->name('index');
        Route::get('/profile', function() { return view('franchise.settings.profile'); })->name('profile');
    });
});

// =============================================================================
// STUDENT ROUTES (Course Access for Students)
// =============================================================================
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    // Student Dashboard
    Route::get('/', function() { return view('student.dashboard'); })->name('dashboard');

    // Student Courses
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/', function() { return view('student.courses.index'); })->name('index');
        Route::get('/{course}', function() { return view('student.courses.show'); })->name('show');
    });

    // Student Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', function() { return view('student.profile.index'); })->name('index');
    });
});

// Include authentication routes
require __DIR__.'/auth.php';
