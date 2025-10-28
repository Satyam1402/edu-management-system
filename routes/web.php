<?php
// routes/web.php (COMPLETE UPDATED VERSION WITH CERTIFICATE SYSTEM)

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

// Franchise Controllers
use App\Http\Controllers\Franchise\CourseController as FranchiseCourseController;
use App\Http\Controllers\Franchise\PaymentController as FranchisePaymentController;
use App\Http\Controllers\Franchise\StudentController as FranchiseStudentController;
use App\Http\Controllers\Franchise\DashboardController as FranchiseDashboardController;
use App\Http\Controllers\Franchise\CertificateController as FranchiseCertificateController;
use App\Http\Controllers\Franchise\CertificateRequestController as FranchiseCertificateRequestController;

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
    // COURSE MANAGEMENT (Global Courses)
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
    // CERTIFICATE REQUEST MANAGEMENT (Admin Approval System)
    // =============================================================================
    Route::resource('certificate-requests', CertificateRequestController::class)->only(['index', 'show']);
    Route::post('/certificate-requests/{certificateRequest}/approve', [CertificateRequestController::class, 'approve'])->name('certificate-requests.approve');
    Route::post('/certificate-requests/{certificateRequest}/reject', [CertificateRequestController::class, 'reject'])->name('certificate-requests.reject');
    Route::post('/certificate-requests/bulk-action', [CertificateRequestController::class, 'bulkAction'])->name('certificate-requests.bulk-action');
    Route::get('/certificate-requests/stats', [CertificateRequestController::class, 'getStats'])->name('certificate-requests.stats');
    Route::get('/certificate-requests/export', [CertificateRequestController::class, 'export'])->name('certificate-requests.export');

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
// FRANCHISE ROUTES - Limited to Own Data
// =============================================================================
Route::middleware(['auth', 'role:franchise'])->prefix('franchise')->name('franchise.')->group(function () {

    // Franchise Dashboard
    Route::get('/', [FranchiseDashboardController::class, 'index'])->name('dashboard');

    // =============================================================================
    // FRANCHISE STUDENT MANAGEMENT (Own Students Only)
    // =============================================================================
    Route::resource('students', FranchiseStudentController::class);
    Route::post('/students/{student}/toggle-status', [FranchiseStudentController::class, 'toggleStatus'])->name('students.toggle-status');
    Route::get('/students/{student}/profile', [FranchiseStudentController::class, 'profile'])->name('students.profile');

    // =============================================================================
    // COURSE MANAGEMENT (View Available Courses)
    // =============================================================================
    Route::resource('courses', FranchiseCourseController::class)->only(['index', 'show']);
    Route::post('/courses/{course}/enroll-student', [FranchiseCourseController::class, 'enrollStudent'])->name('courses.enroll-student');

    // =============================================================================
    // 🆕 FRANCHISE CERTIFICATE MANAGEMENT (View & Download Issued Certificates)
    // =============================================================================
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/', [FranchiseCertificateController::class, 'index'])->name('index');
        Route::get('/{certificate}', [FranchiseCertificateController::class, 'show'])->name('show');
        Route::get('/{certificate}/download', [FranchiseCertificateController::class, 'download'])->name('download');
        Route::get('/{certificate}/print', [FranchiseCertificateController::class, 'print'])->name('print');
    });

    // =============================================================================
    // 🆕 CERTIFICATE REQUEST MANAGEMENT (Payment Required First)
    // =============================================================================
    Route::resource('certificate-requests', FranchiseCertificateRequestController::class);
    Route::get('/certificate-requests/student/{student}', [FranchiseCertificateRequestController::class, 'createForStudent'])->name('certificate-requests.create-for-student');
    Route::post('/certificate-requests/check-payment', [FranchiseCertificateRequestController::class, 'checkPayment'])->name('certificate-requests.check-payment');

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
// ADDITIONAL SHARED ROUTES (Available to both roles)
// =============================================================================
Route::middleware('auth')->group(function () {
    // Search functionality
    Route::get('/search/students', function() {
        // Global student search logic here
    })->name('search.students');

    Route::get('/search/courses', function() {
        // Course search logic here
    })->name('search.courses');

    // Quick actions
    Route::post('/quick/student-status', function() {
        // Quick student status change
    })->name('quick.student-status');

    Route::post('/quick/payment-record', function() {
        // Quick payment recording
    })->name('quick.payment-record');
});

// =============================================================================
// API ROUTES (for AJAX requests)
// =============================================================================
Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    // Student API endpoints
    Route::get('/students/search', [StudentController::class, 'search'])->name('students.search');
    Route::get('/students/{student}/payments', [StudentController::class, 'getPayments'])->name('students.payments');

    // Course API endpoints
    Route::get('/courses/search', [CourseController::class, 'search'])->name('courses.search');
    Route::get('/courses/{course}/students', [CourseController::class, 'getStudents'])->name('courses.students');

    // Franchise API endpoints (if super admin)
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/franchises/search', [FranchiseController::class, 'search'])->name('franchises.search');
        Route::get('/franchises/{franchise}/stats', [FranchiseController::class, 'getStats'])->name('franchises.stats');
    });
});

// =============================================================================
// TESTING ROUTES (Remove in Production)
// =============================================================================
Route::get('/test-upi', function() {
    $service = new \App\Services\PaymentGatewayService();
    try {
        $qrData = $service->generateUpiQrCode(100, 'Test User', 'Test Payment');
        return response()->json($qrData);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// Include authentication routes (login, register, password reset, etc.)
require __DIR__.'/auth.php';
