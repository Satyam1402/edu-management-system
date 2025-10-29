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
        'franchise_id', // 🆕 ADDED: Link to franchise
        'certificate_request_id', // Link to certificate request
        'number', // Certificate number field
        'status',
        'issued_at', // When certificate was issued
        'issued_by', // Who issued the certificate
        'valid_until', // Certificate validity
        'revocation_reason', // 🆕 ADDED: Why certificate was revoked
        'revoked_at', // 🆕 ADDED: When certificate was revoked
        'revoked_by' // 🆕 ADDED: Who revoked the certificate
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'valid_until' => 'datetime',
        'revoked_at' => 'datetime'
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
     * 🆕 Get the franchise that issued this certificate
     */
    public function franchise()
    {
        return $this->belongsTo(Franchise::class);
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

    /**
     * 🆕 Get the user who revoked this certificate
     */
    public function revokedBy()
    {
        return $this->belongsTo(User::class, 'revoked_by');
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

    /**
     * 🆕 Scope for certificates by franchise
     */
    public function scopeByFranchise($query, $franchiseId)
    {
        return $query->where('franchise_id', $franchiseId);
    }

    /**
     * 🆕 Scope for revoked certificates
     */
    public function scopeRevoked($query)
    {
        return $query->where('status', 'revoked');
    }

    // ===== ACCESSORS =====

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'requested' => '<span class="badge badge-warning">Requested</span>',
            'approved' => '<span class="badge badge-success">Approved</span>',
            'issued' => '<span class="badge badge-primary">Issued</span>',
            'active' => '<span class="badge badge-success">Active</span>',
            'expired' => '<span class="badge badge-danger">Expired</span>',
            'revoked' => '<span class="badge badge-dark">Revoked</span>',
            default => '<span class="badge badge-light">Unknown</span>'
        };
    }

    public function getFormattedIssuedDateAttribute(): string
    {
        return $this->issued_at ? $this->issued_at->format('M d, Y') : 'Not issued';
    }

    /**
     * 🆕 UPDATED: Get formatted certificate number (using the actual number field)
     */
    public function getFormattedNumberAttribute(): string
    {
        return $this->number ?? 'CERT-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get certificate validity status
     */
    public function getValidityStatusAttribute(): string
    {
        if ($this->status === 'revoked') {
            return 'Certificate Revoked';
        }

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
     * 🆕 Check if certificate is revoked
     */
    public function isRevoked(): bool
    {
        return $this->status === 'revoked';
    }

    /**
     * Check if certificate is currently valid
     */
    public function isValid(): bool
    {
        if ($this->status === 'revoked') {
            return false;
        }

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
     * 🆕 REMOVED: generateCertificateNumber() method (moved to controller)
     * This was causing conflicts - the controller now handles this
     */

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
    public function revoke($reason = null, $revokedBy = null): bool
    {
        if (!$this->isIssued()) {
            return false; // Can only revoke issued certificates
        }

        $this->update([
            'status' => 'revoked',
            'revocation_reason' => $reason,
            'revoked_at' => now(),
            'revoked_by' => $revokedBy ?? auth()->id()
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

    /**
     * 🆕 Get certificate template data for PDF generation
     */
    public function getTemplateData(): array
    {
        return [
            'certificate_number' => $this->number,
            'student_name' => $this->student->name ?? 'Unknown Student',
            'course_name' => $this->course->name ?? 'General Certificate',
            'franchise_name' => $this->franchise->name ?? 'Unknown Franchise',
            'issued_date' => $this->issued_at ? $this->issued_at->format('F d, Y') : now()->format('F d, Y'),
            'validity_status' => $this->validity_status,
            'is_valid' => $this->isValid()
        ];
    }

    /**
     * 🆕 Get download filename
     */
    public function getDownloadFilename(): string
    {
        $studentName = str_replace(' ', '_', $this->student->name ?? 'Certificate');
        $courseCode = $this->course ? str_replace(' ', '_', substr($this->course->name, 0, 10)) : 'General';

        return "Certificate_{$studentName}_{$courseCode}_{$this->number}.pdf";
    }

    /**
     * 🆕 Check if certificate belongs to franchise
     */
    public function belongsToFranchise($franchiseId): bool
    {
        return $this->franchise_id == $franchiseId;
    }
}
