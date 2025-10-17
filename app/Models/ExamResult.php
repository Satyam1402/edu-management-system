<?php
// app/Models/ExamResult.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id', 'student_id', 'marks_obtained', 'percentage',
        'result', 'exam_start_time', 'exam_end_time', 'answers', 'remarks'
    ];

    protected $casts = [
        'exam_start_time' => 'datetime',
        'exam_end_time' => 'datetime',
        'answers' => 'array'
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
