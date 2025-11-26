<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Franchise extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'pincode',
        'status',
        'contact_person',
        'established_date',
        'license_number',
        'notes'
    ];

    protected $casts = [
        'established_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // =============================================================================
    // 🔧 EXISTING RELATIONSHIPS (Keep These)
    // =============================================================================

    /**
     * Users belonging to this franchise (franchise owners/managers)
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Students belonging to this franchise (if using Student model)
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Payments through students (if using Student model)
     */
    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Student::class);
    }

    /**
     * Certificates through students (if using Student model)
     */
    public function certificates()
    {
        return $this->hasManyThrough(Certificate::class, Student::class);
    }

    // =============================================================================
    // 🔧 NEW ENROLLMENT & COURSE RELATIONSHIPS
    // =============================================================================

    /**
     * All enrollments created by this franchise
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Students enrolled by this franchise (using enrollments pivot)
     */
    public function enrolledStudents()
    {
        return $this->hasManyThrough(User::class, Enrollment::class, 'franchise_id', 'id', 'id', 'student_id')
                    ->where('users.role', 'student')
                    ->distinct();
    }

    /**
     * Courses that have students enrolled by this franchise
     */
    public function offeredCourses()
    {
        return $this->hasManyThrough(Course::class, Enrollment::class, 'franchise_id', 'id', 'id', 'course_id')
                    ->distinct();
    }

    /**
     * Get all courses with enrollment count for this franchise
     */
    public function coursesWithEnrollmentCount()
    {
        return Course::withCount(['enrollments' => function($q) {
            $q->where('franchise_id', $this->id);
        }]);
    }

    // =============================================================================
    // 🔧 REVENUE & ANALYTICS METHODS
    // =============================================================================

    /**
     * Get total revenue from all enrollments
     */
    public function getTotalRevenue()
    {
        return $this->enrollments()
                   ->where('payment_status', 'paid')
                   ->sum('amount_paid');
    }

    /**
     * Get total students enrolled by this franchise
     */
    public function getTotalStudentsCount()
    {
        return $this->enrollments()
                   ->distinct('student_id')
                   ->count();
    }

    /**
     * Get active enrollments count
     */
    public function getActiveEnrollmentsCount()
    {
        return $this->enrollments()
                   ->where('status', 'active')
                   ->count();
    }

    /**
     * Get revenue for current month
     */
    public function getMonthlyRevenue($month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        return $this->enrollments()
                   ->where('payment_status', 'paid')
                   ->whereMonth('created_at', $month)
                   ->whereYear('created_at', $year)
                   ->sum('amount_paid');
    }

    /**
     * Get top performing courses by revenue
     */
    public function getTopCoursesByRevenue($limit = 5)
    {
        return Course::withSum(['enrollments as total_revenue' => function($q) {
                    $q->where('franchise_id', $this->id)
                      ->where('payment_status', 'paid');
                }], 'amount_paid')
                ->having('total_revenue', '>', 0)
                ->orderByDesc('total_revenue')
                ->limit($limit)
                ->get();
    }

    /**
     * Get recent enrollments
     */
    public function getRecentEnrollments($limit = 10)
    {
        return $this->enrollments()
                   ->with(['student', 'course'])
                   ->orderByDesc('created_at')
                   ->limit($limit)
                   ->get();
    }

    // =============================================================================
    // 🔧 COURSE SPECIFIC METHODS
    // =============================================================================

    /**
     * Get students enrolled in a specific course by this franchise
     */
    public function getStudentsInCourse($courseId)
    {
        return User::where('role', 'student')
                  ->whereHas('enrollments', function($q) use ($courseId) {
                      $q->where('course_id', $courseId)
                        ->where('franchise_id', $this->id);
                  });
    }

    /**
     * Get revenue from a specific course
     */
    public function getCourseRevenue($courseId)
    {
        return $this->enrollments()
                   ->where('course_id', $courseId)
                   ->where('payment_status', 'paid')
                   ->sum('amount_paid');
    }

    /**
     * Check if franchise has students in a specific course
     */
    public function hasStudentsInCourse($courseId)
    {
        return $this->enrollments()
                   ->where('course_id', $courseId)
                   ->exists();
    }

    // =============================================================================
    // 🔧 EXISTING ACCESSORS (Keep These)
    // =============================================================================

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'suspended' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get franchise display name
     */
    public function getDisplayNameAttribute()
    {
        return $this->name . ($this->code ? " ({$this->code})" : '');
    }

    /**
     * Get franchise address formatted
     */
    public function getFullAddressAttribute()
    {
        $address_parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->pincode
        ]);

        return implode(', ', $address_parts);
    }

    // =============================================================================
    // 🔧 EXISTING SCOPES (Keep These)
    // =============================================================================

    /**
     * Scope for active franchises
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for inactive franchises
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope for suspended franchises
     */
    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    // =============================================================================
    // 🔧 NEW SCOPES FOR COURSE MANAGEMENT
    // =============================================================================

    /**
     * Scope for franchises with enrollments
     */
    public function scopeWithEnrollments($query)
    {
        return $query->has('enrollments');
    }

    /**
     * Scope for franchises by city
     */
    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    /**
     * Scope for franchises by state
     */
    public function scopeByState($query, $state)
    {
        return $query->where('state', $state);
    }

    /**
     * Scope for franchises established after a date
     */
    public function scopeEstablishedAfter($query, $date)
    {
        return $query->where('established_date', '>=', $date);
    }

    // =============================================================================
    // 🔧 DASHBOARD STATISTICS METHODS
    // =============================================================================

    /**
     * Get comprehensive dashboard statistics
     */
    public function getDashboardStats()
    {
        return [
            'total_students' => $this->getTotalStudentsCount(),
            'active_enrollments' => $this->getActiveEnrollmentsCount(),
            'total_revenue' => $this->getTotalRevenue(),
            'monthly_revenue' => $this->getMonthlyRevenue(),
            'courses_offered' => $this->offeredCourses()->count(),
            'recent_enrollments' => $this->getRecentEnrollments(5),
            'top_courses' => $this->getTopCoursesByRevenue(3),
        ];
    }

    /**
     * Get monthly statistics for charts
     */
    public function getMonthlyStats($year = null)
    {
        $year = $year ?? now()->year;
        $stats = [];

        for ($month = 1; $month <= 12; $month++) {
            $stats[] = [
                'month' => $month,
                'revenue' => $this->getMonthlyRevenue($month, $year),
                'enrollments' => $this->enrollments()
                                      ->whereMonth('created_at', $month)
                                      ->whereYear('created_at', $year)
                                      ->count(),
            ];
        }

        return $stats;
    }

    public function wallet()
    {
      return $this->hasOne(\App\Models\FranchiseWallet::class);
    }

}
