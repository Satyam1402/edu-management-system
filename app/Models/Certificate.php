<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'certificate_request_id', // ← ADDED: Link to certificate request
        'number', // ← Your existing certificate number field
        'status',
        'issued_at', // ← Your existing issued_at field
        'issued_by', // ← ADDED: Who issued the certificate
        'valid_until' // ← ADDED: Certificate validity
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'valid_until' => 'datetime'
    ];

    // ===== RELATIONSHIPS =====

    /**
     * Get the student that owns the certificate
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the course that this certificate is for
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the certificate request that generated this certificate
     */
    public function certificateRequest()
    {
        return $this->belongsTo(CertificateRequest::class);
    }

    /**
     * Get the user who issued this certificate
     */
    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    // ===== SCOPES =====

    public function scopeRequested($query)
    {
        return $query->where('status', 'requested');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeIssued($query)
    {
        return $query->where('status', 'issued');
    }

    /**
     * Scope for active certificates only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'issued')
                    ->where(function($q) {
                        $q->whereNull('valid_until')
                          ->orWhere('valid_until', '>', now());
                    });
    }

    /**
     * Scope for expired certificates
     */
    public function scopeExpired($query)
    {
        return $query->where('valid_until', '<=', now());
    }

    // ===== ACCESSORS =====

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'requested' => '<span class="badge badge-warning">Requested</span>',
            'approved' => '<span class="badge badge-success">Approved</span>',
            'issued' => '<span class="badge badge-primary">Issued</span>',
            'active' => '<span class="badge badge-success">Active</span>', // ← ADDED
            'expired' => '<span class="badge badge-danger">Expired</span>', // ← ADDED
            'revoked' => '<span class="badge badge-dark">Revoked</span>', // ← ADDED
            default => '<span class="badge badge-light">Unknown</span>'
        };
    }

    public function getFormattedIssuedDateAttribute(): string
    {
        return $this->issued_at ? $this->issued_at->format('M d, Y') : 'Not issued';
    }

    /**
     * Get formatted certificate number
     */
    public function getFormattedNumberAttribute(): string
    {
        return 'CERT-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get certificate validity status
     */
    public function getValidityStatusAttribute(): string
    {
        if (!$this->valid_until) {
            return 'Lifetime Valid';
        }

        if ($this->valid_until->isFuture()) {
            return 'Valid until ' . $this->valid_until->format('M d, Y');
        }

        return 'Expired on ' . $this->valid_until->format('M d, Y');
    }

    // ===== METHODS =====

    public function canBeModified(): bool
    {
        return $this->status !== 'issued' && $this->status !== 'revoked';
    }

    public function isIssued(): bool
    {
        return $this->status === 'issued' || $this->status === 'active';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRequested(): bool
    {
        return $this->status === 'requested';
    }

    /**
     * Check if certificate is currently valid
     */
    public function isValid(): bool
    {
        if ($this->status !== 'issued' && $this->status !== 'active') {
            return false;
        }

        if (!$this->valid_until) {
            return true; // Lifetime valid
        }

        return $this->valid_until->isFuture();
    }

    /**
     * Check if certificate is expired
     */
    public function isExpired(): bool
    {
        if (!$this->valid_until) {
            return false; // Lifetime valid
        }

        return $this->valid_until->isPast();
    }

    /**
     * Generate unique certificate number
     */
    public static function generateCertificateNumber($studentId = null): string
    {
        $prefix = 'CERT';
        $timestamp = time();
        $random = rand(100, 999);
        $studentPart = $studentId ? str_pad($studentId, 4, '0', STR_PAD_LEFT) : rand(1000, 9999);

        return "{$prefix}-{$timestamp}-{$studentPart}-{$random}";
    }

    /**
     * Issue the certificate
     */
    public function issue($issuedBy = null): bool
    {
        if ($this->isIssued()) {
            return false; // Already issued
        }

        $this->update([
            'status' => 'issued',
            'issued_at' => now(),
            'issued_by' => $issuedBy ?? auth()->id()
        ]);

        return true;
    }

    /**
     * Revoke the certificate
     */
    public function revoke($reason = null): bool
    {
        if (!$this->isIssued()) {
            return false; // Can only revoke issued certificates
        }

        $this->update([
            'status' => 'revoked',
            'revocation_reason' => $reason,
            'revoked_at' => now(),
            'revoked_by' => auth()->id()
        ]);

        return true;
    }

    /**
     * Set certificate validity period
     */
    public function setValidity($years = null): void
    {
        if ($years) {
            $this->update([
                'valid_until' => now()->addYears($years)
            ]);
        } else {
            $this->update([
                'valid_until' => null // Lifetime valid
            ]);
        }
    }
}
