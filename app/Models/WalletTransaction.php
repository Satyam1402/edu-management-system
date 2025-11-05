<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'franchise_wallet_id',
        'type',
        'amount',
        'description',
        'status',
        'balance_after',
        'transaction_id',
        'payment_method',
        'metadata'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the wallet that owns this transaction
     */
    public function wallet()
    {
        return $this->belongsTo(FranchiseWallet::class, 'franchise_wallet_id');
    }

    /**
     * Scope for credit transactions
     */
    public function scopeCredits($query)
    {
        return $query->where('type', 'credit');
    }

    /**
     * Scope for debit transactions
     */
    public function scopeDebits($query)
    {
        return $query->where('type', 'debit');
    }

    /**
     * Scope for completed transactions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Check if transaction is a credit
     */
    public function isCredit()
    {
        return $this->type === 'credit';
    }

    /**
     * Check if transaction is a debit
     */
    public function isDebit()
    {
        return $this->type === 'debit';
    }

    /**
     * Get formatted amount with symbol
     */
    public function getFormattedAmountAttribute()
    {
        $symbol = $this->isCredit() ? '+' : '-';
        return $symbol . '₹' . number_format($this->amount, 2);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'completed' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            'refunded' => 'info',
            default => 'secondary'
        };
    }
}
