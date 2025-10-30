<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'status',
        'franchise_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // =============================================================================
    // 🔧 EXISTING RELATIONSHIPS (Keep These)
    // =============================================================================

    /**
     * Relationship with Franchise
     */
    public function franchise()
    {
        return $this->belongsTo(Franchise::class);
    }

    // =============================================================================
    // 🔧 NEW COURSE & ENROLLMENT RELATIONSHIPS
    // =============================================================================

    /**
     * Get all enrollments for this student
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    /**
     * Get all course enrollments for this student (alias for enrollments)
     */
    public function courseEnrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    /**
     * Get courses this student is enrolled in
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'enrollments', 'student_id', 'course_id')
                    ->withPivot(['franchise_id', 'enrollment_date', 'payment_status', 'amount_paid', 'status'])
                    ->withTimestamps();
    }

    /**
     * Get enrollments by a specific franchise (for this student)
     */
    public function enrollmentsByFranchise($franchiseId)
    {
        return $this->enrollments()->where('franchise_id', $franchiseId);
    }

    // =============================================================================
    // 🔧 EXISTING PERMISSION METHODS (Keep These)
    // =============================================================================

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin()
    {
        return $this->hasRole('super_admin');
    }

    /**
     * Check if user is franchise owner
     */
    public function isFranchiseOwner()
    {
        return $this->hasRole('franchise');
    }

    /**
     * Check if user is student
     */
    public function isStudent()
    {
        return $this->hasRole('student');
    }

    // =============================================================================
    // 🔧 UPDATED ACCESS CONTROL METHODS
    // =============================================================================

    /**
     * Get accessible students based on role
     */
    public function accessibleStudents()
    {
        if ($this->isSuperAdmin()) {
            return User::where('role', 'student'); // All students
        }

        if ($this->isFranchiseOwner() && $this->franchise_id) {
            // Get students enrolled by this franchise
            return User::where('role', 'student')
                      ->whereHas('enrollments', function($q) {
                          $q->where('franchise_id', $this->franchise_id);
                      });
        }

        return User::whereRaw('1 = 0'); // No students
    }

    /**
     * Get accessible courses based on role
     */
    public function accessibleCourses()
    {
        if ($this->isSuperAdmin()) {
            return Course::query(); // All courses
        }

        if ($this->isFranchiseOwner()) {
            return Course::where('status', 'active'); // Only active courses
        }

        if ($this->isStudent()) {
            // Only courses the student is enrolled in
            return $this->courses();
        }

        return Course::whereRaw('1 = 0'); // No courses
    }

    // =============================================================================
    // 🔧 NEW UTILITY METHODS FOR COURSE MANAGEMENT
    // =============================================================================

    /**
     * Check if student is enrolled in a specific course
     */
    public function isEnrolledIn($courseId, $franchiseId = null)
    {
        $query = $this->enrollments()->where('course_id', $courseId);

        if ($franchiseId) {
            $query->where('franchise_id', $franchiseId);
        }

        return $query->exists();
    }

    /**
     * Get enrollment for a specific course and franchise
     */
    public function getEnrollment($courseId, $franchiseId = null)
    {
        $query = $this->enrollments()->where('course_id', $courseId);

        if ($franchiseId) {
            $query->where('franchise_id', $franchiseId);
        }

        return $query->first();
    }

    /**
     * Get total revenue generated by this student
     */
    public function getTotalRevenue()
    {
        return $this->enrollments()
                   ->where('payment_status', 'paid')
                   ->sum('amount_paid');
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

    // =============================================================================
    // 🔧 SCOPES FOR EASIER QUERYING
    // =============================================================================

    /**
     * Scope for students only
     */
    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    /**
     * Scope for franchise users only
     */
    public function scopeFranchises($query)
    {
        return $query->where('role', 'franchise');
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for users by franchise
     */
    public function scopeByFranchise($query, $franchiseId)
    {
        return $query->where('franchise_id', $franchiseId);
    }

    /**
     * Scope for students enrolled by a specific franchise
     */
    public function scopeEnrolledByFranchise($query, $franchiseId)
    {
        return $query->whereHas('enrollments', function($q) use ($franchiseId) {
            $q->where('franchise_id', $franchiseId);
        });
    }

    // =============================================================================
    // 🔧 ATTRIBUTE ACCESSORS
    // =============================================================================

    /**
     * Get user's display name
     */
    public function getDisplayNameAttribute()
    {
        return $this->name;
    }

    /**
     * Get user's avatar URL
     */
    public function getAvatarAttribute()
    {
        // Generate avatar using UI Avatars service
        return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&background=007bff&color=fff";
    }

    /**
     * Get user's status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status ?? 'active') {
            'active' => 'success',
            'inactive' => 'secondary',
            'suspended' => 'danger',
            default => 'primary'
        };
    }
}
