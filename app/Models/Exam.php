<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'title', 'description', 'mode', 'exam_date', 
        'start_time', 'duration_minutes', 'total_marks', 'status'
    ];

    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime:H:i'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function exam_attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    // Helper methods
    public function getDurationTextAttribute()
    {
        $hours = intval($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        
        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        return $minutes . ' minutes';
    }

    public function getFormattedDateAttribute()
    {
        return $this->exam_date ? $this->exam_date->format('M d, Y') : 'Not scheduled';
    }
}
