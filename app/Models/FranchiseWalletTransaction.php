<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FranchiseWalletTransaction extends Model
{
    use HasFactory;

    protected $table = 'franchise_wallet_transactions';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'franchise_id',
        'type',                 // credit or debit
        'amount',
        'source',               // wallet_topup, certificate_batch, etc.
        'payment_method',       // razorpay, upi, bank_transfer
        'status',               // pending, completed, failed
        'reference_id',         // payment gateway transaction ID
        'meta',                 // additional JSON data
        'completed_at'          // timestamp when completed
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'meta' => 'array',              // Automatically converts JSON to array
        'amount' => 'decimal:2',        // Format amount to 2 decimal places
        'completed_at' => 'datetime',   // Cast to Carbon datetime instance
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [];

    /**
     * Get the franchise that owns the transaction.
     */
    public function franchise()
    {
        return $this->belongsTo(Franchise::class, 'franchise_id');
    }

    /**
     * Scope to get only credit transactions.
     */
    public function scopeCredits($query)
    {
        return $query->where('type', 'credit');
    }

    /**
     * Scope to get only debit transactions.
     */
    public function scopeDebits($query)
    {
        return $query->where('type', 'debit');
    }

    /**
     * Scope to get only completed transactions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get only pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get formatted amount with currency symbol.
     */
    public function getFormattedAmountAttribute()
    {
        return '₹' . number_format($this->amount, 2);
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'completed' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Check if transaction is completed.
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if transaction is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if transaction is failed.
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }
}
