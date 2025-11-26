<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletAuditLog extends Model
{
    const UPDATED_AT = null; // Audit logs don't update

    protected $fillable = [
        'franchise_id',
        'action',
        'old_balance',
        'new_balance',
        'amount_changed',
        'performed_by',
        'ip_address',
        'user_agent',
        'details'
    ];

    protected $casts = [
        'old_balance' => 'decimal:2',
        'new_balance' => 'decimal:2',
        'amount_changed' => 'decimal:2',
        'details' => 'array',
        'created_at' => 'datetime'
    ];

    // Relationships
    public function franchise()
    {
        return $this->belongsTo(Franchise::class);
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    // Scopes
    public function scopeForFranchise($query, $franchiseId)
    {
        return $query->where('franchise_id', $franchiseId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('performed_by', $userId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }
}
