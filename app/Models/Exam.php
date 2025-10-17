<?php
// app/Models/Exam.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'exam_code', 'description', 'course_id',
        'exam_date', 'duration', 'total_marks', 'passing_marks',
        'status', 'instructions'
    ];

    protected $casts = [
        'exam_date' => 'datetime',
        'instructions' => 'array'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function students()
    {
        return $this->hasManyThrough(Student::class, ExamResult::class, 'exam_id', 'id', 'id', 'student_id');
    }
}
