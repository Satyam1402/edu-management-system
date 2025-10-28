<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'franchise_id',
        'student_id',
        'course_id',
        'payment_id',
        'status',
        'note',
        'requested_at',
        // New admin approval fields
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'admin_notes'
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // ========================================
    // EXISTING RELATIONSHIPS (Keep as they are)
    // ========================================

    public function franchise()
    {
        return $this->belongsTo(User::class, 'franchise_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    // ========================================
    // NEW ADMIN APPROVAL RELATIONSHIPS
    // ========================================

    /**
     * Get the admin who approved this request
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the admin who rejected this request
     */
    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Get the certificate created from this request (if approved)
     */
    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }

    // ========================================
    // EXISTING SCOPES (Keep as they are)
    // ========================================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // ========================================
    // NEW HELPER METHODS
    // ========================================

    /**
     * Check if request can be approved
     */
    public function canBeApproved()
    {
        return $this->status === 'pending' &&
               $this->payment &&
               $this->payment->status === 'completed';
    }

    /**
     * Check if request can be rejected
     */
    public function canBeRejected()
    {
        return $this->status === 'pending';
    }

    /**
     * Get status color for badges
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'issued' => 'info'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Get formatted status text
     */
    public function getStatusTextAttribute()
    {
        return ucfirst($this->status);
    }

    /**
     * Check if payment is completed
     */
    public function hasCompletedPayment()
    {
        return $this->payment && $this->payment->status === 'completed';
    }
}
