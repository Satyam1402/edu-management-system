<?php
// app/Models/Course.php - COMPLETE UPDATED VERSION FOR ENROLLMENT SYSTEM
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'fee',
        'discount_fee',
        'is_free',
        'franchise_fee',
        'fee_notes',
        'duration_months',
        'curriculum',
        'prerequisites',
        'level',
        'learning_outcomes',
        'certificate_template',
        'category',
        'max_students',
        'passing_percentage',
        'instructor_name',
        'instructor_email',
        'course_image',
        'tags',
        'is_featured',
        'status'
    ];

    protected $casts = [
        'learning_outcomes' => 'array',
        'tags' => 'array',
        'is_featured' => 'boolean',
        'fee' => 'decimal:2',
        'discount_fee' => 'decimal:2',
        'franchise_fee' => 'decimal:2',
        'is_free' => 'boolean',
        'passing_percentage' => 'decimal:2'
    ];

    protected $dates = [
        'deleted_at'
    ];

    // Boot method to auto-generate course code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($course) {
            if (!$course->code) {
                $course->code = static::generateCourseCode($course->name);
            }

            if ($course->passing_percentage === null) {
                $course->passing_percentage = 60.00; // Default passing percentage
            }
        });
    }

    // =============================================================================
    // 🔧 UPDATED: ENROLLMENT RELATIONSHIPS (CRITICAL FOR FRANCHISE SYSTEM)
    // =============================================================================

    /**
     * Get all enrollments for this course
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get all students enrolled in this course (using the enrollments pivot table)
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'student_id')
                    ->withPivot(['franchise_id', 'enrollment_date', 'payment_status', 'amount_paid', 'status'])
                    ->withTimestamps();
    }

    /**
     * Get students enrolled by a specific franchise
     */
    public function studentsByFranchise($franchiseId)
    {
        return $this->students()->wherePivot('franchise_id', $franchiseId);
    }

    /**
     * Get enrollments by a specific franchise
     */
    public function enrollmentsByFranchise($franchiseId)
    {
        return $this->enrollments()->where('franchise_id', $franchiseId);
    }

    // =============================================================================
    // 🔧 EXISTING RELATIONSHIPS (Keep These - But Updated for Consistency)
    // =============================================================================

    /**
     * Direct relationship to Student model (if you're using a separate Student model)
     * Note: This might conflict with enrollments - use one or the other
     */
    public function directStudents()
    {
        return $this->hasMany(Student::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function examResults()
    {
        return $this->hasManyThrough(ExamResult::class, Exam::class);
    }

    // =============================================================================
    // 🔧 UPDATED PRICING ACCESSORS (Keep Your Excellent Logic)
    // =============================================================================

    /**
     * Get the effective fee (considering discounts and franchise pricing)
     */
    public function getEffectiveFeeAttribute()
    {
        if ($this->is_free) return 0;

        // If there's a franchise fee, use it for franchise users
        if ($this->franchise_fee !== null) {
            return $this->franchise_fee;
        }

        // If there's a discount fee, use it
        if ($this->discount_fee !== null) {
            return $this->discount_fee;
        }

        // Otherwise use regular fee
        return $this->fee;
    }

    /**
     * Get formatted price for display (replaces existing getFormattedFeeAttribute)
     */
    public function getFormattedPriceAttribute()
    {
        if ($this->is_free) return 'Free';

        $effectiveFee = $this->effective_fee;
        return '₹' . number_format($effectiveFee, 2);
    }

    /**
     * Check if course has discount
     */
    public function getHasDiscountAttribute()
    {
        return $this->discount_fee !== null && $this->discount_fee < $this->fee;
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentageAttribute()
    {
        if (!$this->has_discount) return 0;

        return round((($this->fee - $this->discount_fee) / $this->fee) * 100);
    }

    /**
     * Get price for franchise users specifically
     */
    public function getFranchisePriceAttribute()
    {
        if ($this->is_free) return 'Free';

        $price = $this->franchise_fee ?? $this->effective_fee;
        return '₹' . number_format($price, 2);
    }

    // =============================================================================
    // 🔧 UPDATED ENROLLMENT-SPECIFIC METHODS
    // =============================================================================

    /**
     * Get total revenue from enrollments (updated to use enrollment table)
     */
    public function getTotalRevenue()
    {
        return $this->enrollments()->where('payment_status', 'paid')->sum('amount_paid');
    }

    /**
     * Get active students count (updated to use enrollment table)
     */
    public function getActiveStudentsCount()
    {
        return $this->enrollments()->where('status', 'active')->count();
    }

    /**
     * Get completed students count (updated to use enrollment table)
     */
    public function getCompletedStudentsCount()
    {
        return $this->enrollments()->where('status', 'completed')->count();
    }

    /**
     * Get total enrolled students count
     */
    public function getTotalEnrolledCount()
    {
        return $this->enrollments()->count();
    }

    /**
     * Get completion rate based on enrollments
     */
    public function getCompletionRate()
    {
        $total = $this->getTotalEnrolledCount();
        $completed = $this->getCompletedStudentsCount();

        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }

    /**
     * Check if course has available seats (updated)
     */
    public function hasAvailableSeats()
    {
        if (!$this->max_students) {
            return true;
        }

        return $this->getTotalEnrolledCount() < $this->max_students;
    }

    /**
     * Get remaining seats (updated)
     */
    public function getRemainingSeats()
    {
        if (!$this->max_students) {
            return 'Unlimited';
        }

        $remaining = $this->max_students - $this->getTotalEnrolledCount();
        return max(0, $remaining);
    }

    /**
     * Get revenue by franchise
     */
    public function getRevenueByFranchise($franchiseId)
    {
        return $this->enrollments()
                   ->where('franchise_id', $franchiseId)
                   ->where('payment_status', 'paid')
                   ->sum('amount_paid');
    }

    /**
     * Get enrollment count by franchise
     */
    public function getEnrollmentCountByFranchise($franchiseId)
    {
        return $this->enrollments()->where('franchise_id', $franchiseId)->count();
    }

    // =============================================================================
    // 🔧 KEEP ALL YOUR EXISTING ACCESSORS & METHODS
    // =============================================================================

    public function getFormattedFeeAttribute()
    {
        return $this->fee ? '₹' . number_format($this->fee, 2) : 'Free';
    }

    public function getDurationTextAttribute()
    {
        if (!$this->duration_months) {
            return 'Duration not set';
        }

        if ($this->duration_months == 1) {
            return '1 month';
        } elseif ($this->duration_months < 12) {
            return $this->duration_months . ' months';
        } else {
            $years = floor($this->duration_months / 12);
            $months = $this->duration_months % 12;
            $duration = $years . ' year' . ($years > 1 ? 's' : '');
            if ($months > 0) {
                $duration .= ' ' . $months . ' month' . ($months > 1 ? 's' : '');
            }
            return $duration;
        }
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => 'success',
            'inactive' => 'secondary',
            'draft' => 'warning',
            'archived' => 'danger'
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    public function getLevelBadgeAttribute()
    {
        $badges = [
            'beginner' => 'success',
            'intermediate' => 'warning',
            'advanced' => 'danger'
        ];

        return $badges[$this->level] ?? 'info';
    }

    public function getCourseImageUrlAttribute()
    {
        return $this->course_image
            ? asset('storage/' . $this->course_image)
            : asset('images/default-course.jpg');
    }

    public function getEnrollmentStatusAttribute()
    {
        $enrolled = $this->getTotalEnrolledCount();
        $max = $this->max_students;

        if (!$max) {
            return 'Open enrollment';
        }

        if ($enrolled >= $max) {
            return 'Full';
        } elseif ($enrolled >= ($max * 0.8)) {
            return 'Almost full';
        } else {
            return 'Available';
        }
    }

    // =============================================================================
    // 🔧 KEEP ALL YOUR EXISTING SCOPES
    // =============================================================================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeWithinPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('fee', [$minPrice, $maxPrice]);
    }

    public function scopePopular($query)
    {
        return $query->withCount(['enrollments'])
                    ->orderBy('enrollments_count', 'desc');
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                  ->orWhere('code', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%")
                  ->orWhere('category', 'like', "%{$term}%");
        });
    }

    // =============================================================================
    // 🔧 KEEP ALL YOUR EXISTING STATIC METHODS
    // =============================================================================

    public function getAverageExamScore()
    {
        $results = $this->examResults()->where('result', 'pass')->get();
        return $results->count() > 0 ? round($results->avg('percentage'), 2) : 0;
    }

    public function isEligibleForEnrollment()
    {
        return $this->status === 'active' && $this->hasAvailableSeats();
    }

    public function getUpcomingExams()
    {
        return $this->exams()
            ->where('status', 'scheduled')
            ->where('exam_date', '>', now())
            ->orderBy('exam_date')
            ->get();
    }

    public function getRecentResults($limit = 10)
    {
        return $this->examResults()
            ->with(['student', 'exam'])
            ->latest()
            ->take($limit)
            ->get();
    }

    public function canBeDeleted()
    {
        return $this->enrollments()->count() === 0 &&
               $this->exams()->count() === 0 &&
               $this->payments()->count() === 0;
    }

    public function getPrerequisitesList()
    {
        return $this->prerequisites ? explode(',', $this->prerequisites) : [];
    }

    // Static methods
    public static function generateCourseCode($name)
    {
        $code = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 4));
        $year = date('y');
        $count = static::where('code', 'like', $code . $year . '%')->count() + 1;

        return $code . $year . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public static function getStatusOptions()
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'draft' => 'Draft',
            'archived' => 'Archived'
        ];
    }

    public static function getLevelOptions()
    {
        return [
            'beginner' => 'Beginner',
            'intermediate' => 'Intermediate',
            'advanced' => 'Advanced'
        ];
    }

    public static function getCategoryOptions()
    {
        return [
            'technology' => 'Technology',
            'business' => 'Business',
            'design' => 'Design',
            'marketing' => 'Marketing',
            'health' => 'Health & Wellness',
            'education' => 'Education',
            'arts' => 'Arts & Crafts',
            'language' => 'Language',
            'other' => 'Other'
        ];
    }

    public static function getFeaturedCourses($limit = 6)
    {
        return static::active()
            ->featured()
            ->withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->take($limit)
            ->get();
    }

    public static function getPopularCourses($limit = 10)
    {
        return static::active()
            ->popular()
            ->take($limit)
            ->get();
    }

    public static function getCoursesWithHighCompletionRate($limit = 5)
    {
        return static::active()
            ->get()
            ->sortByDesc(function ($course) {
                return $course->getCompletionRate();
            })
            ->take($limit);
    }
}
