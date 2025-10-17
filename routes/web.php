<?php
// routes/web.php (COMPLETE UPDATED VERSION WITH FRANCHISE SYSTEM)
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
    Route::post('/franchises/{franchise}/toggle-status', [FranchiseController::class, 'toggleStatus'])->name('franchises.toggle-status');
    Route::post('/franchises/{franchise}/create-user', [FranchiseController::class, 'createUser'])->name('franchises.create-user');

    // Course Management (Global Courses)
    Route::resource('courses', CourseController::class);
    Route::post('/courses/{course}/toggle-status', [CourseController::class, 'toggleStatus'])->name('courses.toggle-status');
    Route::post('/courses/{course}/feature', [CourseController::class, 'toggleFeatured'])->name('courses.toggle-featured');

    // Student Management (All Students Across All Franchises)
    Route::resource('students', StudentController::class);
    Route::post('/students/{student}/toggle-status', [StudentController::class, 'toggleStatus'])->name('students.toggle-status');
    Route::get('/students/franchise/{franchise}', [StudentController::class, 'byFranchise'])->name('students.by-franchise');

    // Certificate Management (Approve/Reject Certificates)
    Route::resource('certificates', CertificateController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::post('/certificates/{certificate}/approve', [CertificateController::class, 'approve'])->name('certificates.approve');
    Route::post('/certificates/{certificate}/reject', [CertificateController::class, 'reject'])->name('certificates.reject');
    Route::post('/certificates/{certificate}/reissue', [CertificateController::class, 'reissue'])->name('certificates.reissue');

    // Exam Management
    Route::resource('exams', ExamController::class);
    Route::post('/exams/{exam}/toggle-status', [ExamController::class, 'toggleStatus'])->name('exams.toggle-status');
    Route::get('/exams/{exam}/results', [ExamController::class, 'results'])->name('exams.results');

    // Payment Management (All Payments)
    Route::resource('payments', PaymentController::class)->except(['edit', 'destroy']);
    Route::post('/payments/{payment}/update-status', [PaymentController::class, 'updateStatus'])->name('payments.update-status');
    Route::post('/payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
    Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');

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
    Route::resource('certificates', FranchiseCertificateController::class)->only(['index', 'show', 'create', 'store']);
    Route::post('/certificates/{certificate}/download', [FranchiseCertificateController::class, 'download'])->name('certificates.download');
    Route::get('/certificates/student/{student}', [FranchiseCertificateController::class, 'byStudent'])->name('certificates.by-student');

    // =============================================================================
    // PAYMENT MANAGEMENT (Own Students' Payments)
    // =============================================================================
    Route::resource('payments', FranchisePaymentController::class)->only(['index', 'show', 'create', 'store']);
    Route::post('/payments/{payment}/mark-paid', [FranchisePaymentController::class, 'markPaid'])->name('payments.mark-paid');
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

// Include authentication routes (login, register, password reset, etc.)
require __DIR__.'/auth.php';
