<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'franchise_id',
        'student_id',
        'course_id',
        'amount',
        'certificate_type',
        'status',
        'notes',
        'requested_at',
        // Admin approval fields
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'admin_notes',
        // Processing fields
        'processed_by',
        'processed_at',
        'certificate_number',
        'issued_date'
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'processed_at' => 'datetime',
        'issued_date' => 'datetime',
        'amount' => 'decimal:2'
    ];

    protected $dates = ['deleted_at'];

    // ========================================
    // RELATIONSHIPS
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

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }

    public function walletTransaction()
    {
        return $this->hasOne(FranchiseWalletTransaction::class, 'reference_id', 'id')
                    ->where('source', 'certificate_request');
    }

    // ========================================
    // SCOPES
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

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForFranchise($query, $franchiseId)
    {
        return $query->where('franchise_id', $franchiseId);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    public function canBeApproved()
    {
        return $this->status === 'pending';
    }

    public function canBeRejected()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function canBeProcessed()
    {
        return $this->status === 'approved';
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'processing' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            'completed' => 'primary',
            default => 'secondary'
        };
    }

    public function getStatusTextAttribute()
    {
        return ucfirst($this->status);
    }

    public function getFormattedAmountAttribute()
    {
        return '₹' . number_format($this->amount, 2);
    }

    public function markAsApproved($adminId, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $adminId,
            'approved_at' => now(),
            'admin_notes' => $notes
        ]);
    }

    public function markAsRejected($adminId, $reason, $notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'rejected_by' => $adminId,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
            'admin_notes' => $notes
        ]);
    }

    public function markAsCompleted($adminId, $certificateNumber, $issuedDate = null)
    {
        $this->update([
            'status' => 'completed',
            'processed_by' => $adminId,
            'processed_at' => now(),
            'certificate_number' => $certificateNumber,
            'issued_date' => $issuedDate ?: now()
        ]);
    }

    // ========================================
    // STATIC METHODS
    // ========================================

    public static function getStatsByFranchise($franchiseId)
    {
        return self::where('franchise_id', $franchiseId)
            ->selectRaw('
                status,
                COUNT(*) as count,
                SUM(amount) as total_amount
            ')
            ->groupBy('status')
            ->get()
            ->keyBy('status');
    }
}
