<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'course_id',
        'franchise_id',
        'enrollment_date',
        'payment_method',
        'amount_paid',
        'payment_status',
        'status',
        'notes',
        'completion_date',
        'grade',
        'certificate_issued'
    ];

    protected $casts = [
        'enrollment_date' => 'datetime',
        'completion_date' => 'datetime',
        'amount_paid' => 'decimal:2',
        'certificate_issued' => 'boolean',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function franchise()
    {
        return $this->belongsTo(Franchise::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeByFranchise($query, $franchiseId)
    {
        return $query->where('franchise_id', $franchiseId);
    }

    public function certificateRequest()
    {
        return $this->hasOne(CertificateRequest::class, 'enrollment_id');
    }

}
