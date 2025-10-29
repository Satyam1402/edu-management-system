<?php
// app/Models/CourseEnrollment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'franchise_id',
        'status',
        'enrollment_date',
        'notes'
    ];

    protected $casts = [
        'enrollment_date' => 'date'
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function franchise()
    {
        return $this->belongsTo(User::class, 'franchise_id');
    }

    // Status badge for display
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'enrolled' => 'primary',
            'completed' => 'success',
            'dropped' => 'danger'
        ];
        return $badges[$this->status] ?? 'secondary';
    }
}
