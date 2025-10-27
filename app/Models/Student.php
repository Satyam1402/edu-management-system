<?php
// app/Models/Student.php - COMPLETE UPDATED VERSION
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'state',
        'pincode',
        'guardian_name',
        'guardian_phone',
        'franchise_id',
        'course_id',
        'enrollment_date',
        'batch',
        'status',
        'notes',
        'profile_photo'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'enrollment_date' => 'date'
    ];

    protected $dates = [
        'date_of_birth',
        'enrollment_date',
        'deleted_at'
    ];

    // Boot method to auto-generate student ID
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            if (!$student->student_id) {
                $lastStudent = static::withTrashed()->orderBy('id', 'desc')->first();
                $nextId = $lastStudent ? $lastStudent->id + 1 : 1;
                $student->student_id = 'STU' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            }

            if (!$student->enrollment_date) {
                $student->enrollment_date = now();
            }
        });
    }

    // Relationships
    public function franchise()
    {
        return $this->belongsTo(User::class, 'franchise_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
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
        return $this->hasMany(ExamResult::class);
    }

    // Keep backward compatibility with existing relationship
    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function exams()
    {
        return $this->hasManyThrough(Exam::class, ExamResult::class, 'student_id', 'id', 'id', 'exam_id');
    }

    // Accessors & Mutators
    public function getFullNameAttribute()
    {
        return $this->name;
    }

    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => 'success',
            'inactive' => 'secondary',
            'graduated' => 'primary',
            'dropped' => 'danger',
            'suspended' => 'warning'
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo
            ? asset('storage/' . $this->profile_photo)
            : asset('images/default-avatar.png');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeByFranchise($query, $franchiseId)
    {
        return $query->where('franchise_id', $franchiseId);
    }

    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeEnrolledThisMonth($query)
    {
        return $query->whereMonth('enrollment_date', now()->month)
                    ->whereYear('enrollment_date', now()->year);
    }

    // Methods
    public function getTotalPaidAmount()
    {
        return $this->payments()->where('status', 'completed')->sum('amount');
    }

    public function getPendingPaymentAmount()
    {
        return $this->payments()->where('status', 'pending')->sum('amount');
    }

    public function getExamAverage()
    {
        $results = $this->examResults()->where('result', 'pass')->get();
        return $results->count() > 0 ? $results->avg('percentage') : 0;
    }

    public function hasCompletedCourse()
    {
        return $this->status === 'graduated';
    }

    public function canTakeExam(Exam $exam)
    {
        return $this->status === 'active' &&
               $this->course_id === $exam->course_id &&
               !$this->examResults()->where('exam_id', $exam->id)->exists();
    }

    public function isEligibleForCertificate()
    {
        if (!$this->course) {
            return false;
        }

        // Check if student has passed all required exams for the course
        $courseExams = $this->course->exams()->where('status', 'completed')->count();
        $passedExams = $this->examResults()
            ->whereHas('exam', function($query) {
                $query->where('course_id', $this->course_id)
                      ->where('status', 'completed');
            })
            ->where('result', 'pass')
            ->count();

        return $courseExams > 0 && $passedExams >= $courseExams;
    }

    public function getNextPaymentDue()
    {
        return $this->payments()
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->first();
    }

    // Static methods
    public static function generateStudentId()
    {
        $lastStudent = static::withTrashed()->orderBy('id', 'desc')->first();
        $nextId = $lastStudent ? $lastStudent->id + 1 : 1;
        return 'STU' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }

    public static function getStatusOptions()
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'graduated' => 'Graduated',
            'dropped' => 'Dropped Out',
            'suspended' => 'Suspended'
        ];
    }

    public static function getGenderOptions()
    {
        return [
            'male' => 'Male',
            'female' => 'Female',
            'other' => 'Other'
        ];
    }
}
