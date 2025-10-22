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
        'number',
        'status',
        'issued_at'
    ];

    protected $casts = [
        'issued_at' => 'datetime'
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

    // Scopes
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

    // Accessors
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'requested' => '<span class="badge badge-warning">Requested</span>',
            'approved' => '<span class="badge badge-success">Approved</span>',
            'issued' => '<span class="badge badge-primary">Issued</span>',
            default => '<span class="badge badge-light">Unknown</span>'
        };
    }

    public function getFormattedIssuedDateAttribute(): string
    {
        return $this->issued_at ? $this->issued_at->format('M d, Y') : 'Not issued';
    }

    // Methods
    public function canBeModified(): bool
    {
        return $this->status !== 'issued';
    }

    public function isIssued(): bool
    {
        return $this->status === 'issued';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRequested(): bool
    {
        return $this->status === 'requested';
    }
}
