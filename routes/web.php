<?php
// routes/web.php (COMPLETE UPDATED VERSION WITH COMPLETE PAYMENT SYSTEM)
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\FranchiseController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReportController;

// Franchise Controllers
use App\Http\Controllers\Franchise\DashboardController as FranchiseDashboardController;
use App\Http\Controllers\Franchise\StudentController as FranchiseStudentController;
use App\Http\Controllers\Franchise\CourseController as FranchiseCourseController;
use App\Http\Controllers\Franchise\CertificateController as FranchiseCertificateController;
use App\Http\Controllers\Franchise\PaymentController as FranchisePaymentController;

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
    Route::post('/franchises/{franchise}/create-user', [FranchiseController::class, 'createUser'])->name('franchises.create-user');

    // =============================================================================
    // COURSE MANAGEMENT (Global Courses)
    // =============================================================================
    
    // Core Course CRUD Operations
    Route::resource('courses', CourseController::class);
    
    // Course Status Management
    Route::post('/courses/{course}/toggle-status', [CourseController::class, 'toggleStatus'])->name('courses.toggle-status');
    Route::post('/courses/{course}/toggle-featured', [CourseController::class, 'toggleFeatured'])->name('courses.toggle-featured');
    Route::post('/courses/{course}/activate', [CourseController::class, 'activate'])->name('courses.activate');
    Route::post('/courses/{course}/deactivate', [CourseController::class, 'deactivate'])->name('courses.deactivate');
    Route::post('/courses/{course}/archive', [CourseController::class, 'archive'])->name('courses.archive');
    
    // Bulk Operations
    Route::post('/courses/bulk-action', [CourseController::class, 'bulkAction'])->name('courses.bulk-action');
    Route::post('/courses/bulk-delete', [CourseController::class, 'bulkDelete'])->name('courses.bulk-delete');
    Route::post('/courses/bulk-status-update', [CourseController::class, 'bulkStatusUpdate'])->name('courses.bulk-status-update');
    Route::post('/courses/bulk-feature', [CourseController::class, 'bulkFeature'])->name('courses.bulk-feature');
    Route::post('/courses/bulk-unfeature', [CourseController::class, 'bulkUnfeature'])->name('courses.bulk-unfeature');
    
    // Import/Export Operations
    Route::get('/courses/export', [CourseController::class, 'export'])->name('courses.export');
    Route::post('/courses/import', [CourseController::class, 'import'])->name('courses.import');
    Route::get('/courses/download-template', [CourseController::class, 'downloadTemplate'])->name('courses.download-template');
    
    // Course Analytics & Reports
    Route::get('/courses/{course}/analytics', [CourseController::class, 'analytics'])->name('courses.analytics');
    Route::get('/courses/{course}/performance-report', [CourseController::class, 'performanceReport'])->name('courses.performance-report');
    Route::get('/courses/{course}/enrollment-report', [CourseController::class, 'enrollmentReport'])->name('courses.enrollment-report');
    Route::get('/courses/{course}/revenue-report', [CourseController::class, 'revenueReport'])->name('courses.revenue-report');
    
    // Course Relationships & Data
    Route::get('/courses/{course}/students', [CourseController::class, 'getStudents'])->name('courses.students');
    Route::get('/courses/{course}/exams', [CourseController::class, 'getExams'])->name('courses.exams');
    Route::get('/courses/{course}/certificates', [CourseController::class, 'getCertificates'])->name('courses.certificates');
    Route::get('/courses/{course}/payments', [CourseController::class, 'getPayments'])->name('courses.payments');
    Route::get('/courses/{course}/instructors', [CourseController::class, 'getInstructors'])->name('courses.instructors');
    
    // Filtering & Search
    Route::get('/courses/category/{category}', [CourseController::class, 'byCategory'])->name('courses.by-category');
    Route::get('/courses/level/{level}', [CourseController::class, 'byLevel'])->name('courses.by-level');
    Route::get('/courses/status/{status}', [CourseController::class, 'byStatus'])->name('courses.by-status');
    Route::get('/courses/featured', [CourseController::class, 'featured'])->name('courses.featured');
    Route::get('/courses/popular', [CourseController::class, 'popular'])->name('courses.popular');
    Route::get('/courses/search', [CourseController::class, 'search'])->name('courses.search');
    
    // Advanced Course Operations
    Route::post('/courses/{course}/duplicate', [CourseController::class, 'duplicate'])->name('courses.duplicate');
    Route::post('/courses/{course}/assign-instructor', [CourseController::class, 'assignInstructor'])->name('courses.assign-instructor');
    Route::post('/courses/{course}/update-pricing', [CourseController::class, 'updatePricing'])->name('courses.update-pricing');
    Route::post('/courses/{course}/update-curriculum', [CourseController::class, 'updateCurriculum'])->name('courses.update-curriculum');
    
    // Course Content Management
    Route::post('/courses/{course}/upload-material', [CourseController::class, 'uploadMaterial'])->name('courses.upload-material');
    Route::get('/courses/{course}/materials', [CourseController::class, 'getMaterials'])->name('courses.materials');
    Route::delete('/courses/{course}/materials/{material}', [CourseController::class, 'deleteMaterial'])->name('courses.delete-material');
    
    // Course Settings & Configuration
    Route::post('/courses/{course}/update-settings', [CourseController::class, 'updateSettings'])->name('courses.update-settings');
    Route::post('/courses/{course}/reset-progress', [CourseController::class, 'resetProgress'])->name('courses.reset-progress');
    Route::post('/courses/{course}/generate-certificates', [CourseController::class, 'generateCertificates'])->name('courses.generate-certificates');
    
    // Course Communication
    Route::post('/courses/{course}/send-announcement', [CourseController::class, 'sendAnnouncement'])->name('courses.send-announcement');
    Route::post('/courses/{course}/notify-students', [CourseController::class, 'notifyStudents'])->name('courses.notify-students');
    Route::post('/courses/{course}/send-reminder', [CourseController::class, 'sendReminder'])->name('courses.send-reminder');
    
    // Course Enrollment Management
    Route::post('/courses/{course}/enroll-student/{student}', [CourseController::class, 'enrollStudent'])->name('courses.enroll-student');
    Route::post('/courses/{course}/unenroll-student/{student}', [CourseController::class, 'unenrollStudent'])->name('courses.unenroll-student');
    Route::post('/courses/{course}/transfer-students', [CourseController::class, 'transferStudents'])->name('courses.transfer-students');
    
    // Course Scheduling & Batches
    Route::get('/courses/{course}/batches', [CourseController::class, 'getBatches'])->name('courses.batches');
    Route::post('/courses/{course}/create-batch', [CourseController::class, 'createBatch'])->name('courses.create-batch');
    Route::post('/courses/{course}/schedule-session', [CourseController::class, 'scheduleSession'])->name('courses.schedule-session');
    
    // Course Prerequisites & Requirements
    Route::post('/courses/{course}/update-prerequisites', [CourseController::class, 'updatePrerequisites'])->name('courses.update-prerequisites');
    Route::post('/courses/{course}/add-requirement', [CourseController::class, 'addRequirement'])->name('courses.add-requirement');
    Route::delete('/courses/{course}/requirements/{requirement}', [CourseController::class, 'removeRequirement'])->name('courses.remove-requirement');
    
    // Course Reviews & Feedback
    Route::get('/courses/{course}/reviews', [CourseController::class, 'getReviews'])->name('courses.reviews');
    Route::post('/courses/{course}/request-feedback', [CourseController::class, 'requestFeedback'])->name('courses.request-feedback');
    Route::get('/courses/{course}/feedback-summary', [CourseController::class, 'getFeedbackSummary'])->name('courses.feedback-summary');

    // =============================================================================
    // STUDENT MANAGEMENT (All Students Across All Franchises)
    // =============================================================================
    
    // Core Student CRUD Operations
    Route::resource('students', StudentController::class);
    
    // Bulk Operations
    Route::post('/students/bulk-action', [StudentController::class, 'bulkAction'])->name('students.bulk-action');
    Route::post('/students/bulk-delete', [StudentController::class, 'bulkDelete'])->name('students.bulk-delete');
    Route::post('/students/bulk-status-update', [StudentController::class, 'bulkStatusUpdate'])->name('students.bulk-status-update');
    
    // Import/Export Operations
    Route::get('/students/export', [StudentController::class, 'export'])->name('students.export');
    Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
    Route::get('/students/download-template', [StudentController::class, 'downloadTemplate'])->name('students.download-template');
    
    // Status Management
    Route::post('/students/{student}/toggle-status', [StudentController::class, 'toggleStatus'])->name('students.toggle-status');
    Route::post('/students/{student}/activate', [StudentController::class, 'activate'])->name('students.activate');
    Route::post('/students/{student}/deactivate', [StudentController::class, 'deactivate'])->name('students.deactivate');
    Route::post('/students/{student}/graduate', [StudentController::class, 'graduate'])->name('students.graduate');
    Route::post('/students/{student}/suspend', [StudentController::class, 'suspend'])->name('students.suspend');
    
    // Student Relationships & Data
    Route::get('/students/{student}/payments', [StudentController::class, 'getPayments'])->name('students.payments');
    Route::get('/students/{student}/certificates', [StudentController::class, 'getCertificates'])->name('students.certificates');
    Route::get('/students/{student}/exams', [StudentController::class, 'getExams'])->name('students.exams');
    Route::get('/students/{student}/attendance', [StudentController::class, 'getAttendance'])->name('students.attendance');
    Route::get('/students/{student}/progress', [StudentController::class, 'getProgress'])->name('students.progress');
    
    // Filtering & Search
    Route::get('/students/franchise/{franchise}', [StudentController::class, 'byFranchise'])->name('students.by-franchise');
    Route::get('/students/course/{course}', [StudentController::class, 'byCourse'])->name('students.by-course');
    Route::get('/students/status/{status}', [StudentController::class, 'byStatus'])->name('students.by-status');
    Route::get('/students/search', [StudentController::class, 'search'])->name('students.search');
    
    // Advanced Student Operations
    Route::post('/students/{student}/enroll-course', [StudentController::class, 'enrollCourse'])->name('students.enroll-course');
    Route::post('/students/{student}/transfer-franchise', [StudentController::class, 'transferFranchise'])->name('students.transfer-franchise');
    Route::post('/students/{student}/change-course', [StudentController::class, 'changeCourse'])->name('students.change-course');
    
    // Student Documents & Files
    Route::post('/students/{student}/upload-document', [StudentController::class, 'uploadDocument'])->name('students.upload-document');
    Route::get('/students/{student}/documents', [StudentController::class, 'getDocuments'])->name('students.documents');
    Route::delete('/students/{student}/documents/{document}', [StudentController::class, 'deleteDocument'])->name('students.delete-document');
    
    // Communication & Notifications
    Route::post('/students/{student}/send-notification', [StudentController::class, 'sendNotification'])->name('students.send-notification');
    Route::post('/students/{student}/send-email', [StudentController::class, 'sendEmail'])->name('students.send-email');
    Route::post('/students/{student}/send-sms', [StudentController::class, 'sendSms'])->name('students.send-sms');
    
    // Student Analytics & Reports
    Route::get('/students/{student}/analytics', [StudentController::class, 'analytics'])->name('students.analytics');
    Route::get('/students/{student}/performance-report', [StudentController::class, 'performanceReport'])->name('students.performance-report');
    Route::get('/students/{student}/attendance-report', [StudentController::class, 'attendanceReport'])->name('students.attendance-report');
    
    // Quick Actions (AJAX endpoints)
    Route::post('/students/{student}/quick-note', [StudentController::class, 'addQuickNote'])->name('students.quick-note');
    Route::post('/students/{student}/quick-payment', [StudentController::class, 'addQuickPayment'])->name('students.quick-payment');
    Route::post('/students/{student}/quick-certificate', [StudentController::class, 'issueQuickCertificate'])->name('students.quick-certificate');

    // Certificate Management (Approve/Reject Certificates)
    Route::resource('certificates', CertificateController::class);
    Route::post('/certificates/{certificate}/approve', [CertificateController::class, 'approve'])->name('certificates.approve');
    Route::post('/certificates/{certificate}/reject', [CertificateController::class, 'reject'])->name('certificates.reject');
    Route::post('/certificates/{certificate}/issue', [CertificateController::class, 'issue'])->name('certificates.issue');
    Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificates.download');
    Route::get('certificates-export', [CertificateController::class, 'export'])->name('certificates.export');

    // Exam Management
    Route::resource('exams', ExamController::class);
    Route::post('/exams/{exam}/toggle-status', [ExamController::class, 'toggleStatus'])->name('exams.toggle-status');
    Route::get('/exams/{exam}/results', [ExamController::class, 'results'])->name('exams.results');

    // =============================================================================
    // PAYMENT MANAGEMENT (COMPLETE WITH ALL GATEWAYS)
    // =============================================================================
    
    // Basic Payment CRUD
    Route::resource('payments', App\Http\Controllers\Admin\PaymentController::class);
    
    // Payment Gateway Routes
    Route::get('payments/{payment}/razorpay', [PaymentController::class, 'handleRazorpayPayment'])->name('payments.razorpay');
    Route::get('payments/{payment}/upi', [PaymentController::class, 'handleUpiPayment'])->name('payments.upi');
    Route::post('payments/verify-razorpay', [PaymentController::class, 'verifyRazorpay'])->name('payments.verify-razorpay');
    
    // UPI Payment Routes
    Route::post('payments/{payment}/confirm-upi', [PaymentController::class, 'confirmUpi'])->name('payments.confirm-upi');
    
    // Payment Status Management
    Route::post('payments/{payment}/mark-completed', [PaymentController::class, 'markAsCompleted'])->name('payments.mark-completed');
    Route::post('payments/{payment}/mark-failed', [PaymentController::class, 'markAsFailed'])->name('payments.mark-failed');
    Route::get('payments/{payment}/mark-paid', [PaymentController::class, 'markAsCompleted'])->name('payments.mark-paid');
    
    // Payment Actions
    Route::post('payments/{payment}/refund', [PaymentController::class, 'processRefund'])->name('payments.refund');
    
    // Export & Reports
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
    // CERTIFICATE MANAGEMENT (Issue for Own Students)
    // =============================================================================
    Route::resource('certificates', FranchiseCertificateController::class)->only(['index', 'show']);
    Route::resource('courses', FranchiseCourseController::class)->only(['index', 'show']);
    Route::post('/certificates/{certificate}/download', [FranchiseCertificateController::class, 'download'])->name('certificates.download');
    Route::get('/certificates/student/{student}', [FranchiseCertificateController::class, 'byStudent'])->name('certificates.by-student');

    // =============================================================================
    // FRANCHISE PAYMENT MANAGEMENT (Own Students' Payments with All Gateways)
    // =============================================================================
    
    // Basic Payment CRUD (Franchise Limited)
    Route::resource('payments', FranchisePaymentController::class);
    
    // Payment Gateway Routes (Franchise)
    Route::get('payments/{payment}/razorpay', [FranchisePaymentController::class, 'handleRazorpayPayment'])->name('payments.razorpay');
    Route::get('payments/{payment}/upi', [FranchisePaymentController::class, 'handleUpiPayment'])->name('payments.upi');
    Route::post('payments/verify-razorpay', [FranchisePaymentController::class, 'verifyRazorpay'])->name('payments.verify-razorpay');
    
    // UPI Payment Routes (Franchise)
    Route::post('payments/{payment}/confirm-upi', [FranchisePaymentController::class, 'confirmUpi'])->name('payments.confirm-upi');
    
    // Payment Status Management (Franchise)
    Route::post('payments/{payment}/mark-completed', [FranchisePaymentController::class, 'markAsCompleted'])->name('payments.mark-completed');
    Route::post('payments/{payment}/mark-failed', [FranchisePaymentController::class, 'markAsFailed'])->name('payments.mark-failed');
    Route::post('payments/{payment}/refund', [FranchisePaymentController::class, 'processRefund'])->name('payments.refund');
    
    // Student-specific Payment Routes
    Route::get('/payments/student/{student}', [FranchisePaymentController::class, 'byStudent'])->name('payments.by-student');

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
