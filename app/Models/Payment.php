<?php
// app/Models/Payment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id', 'student_id', 'course_id', 'amount',
        'payment_type', 'payment_method', 'transaction_id',
        'payment_date', 'status', 'notes', 'payment_details'
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'payment_details' => 'array'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
