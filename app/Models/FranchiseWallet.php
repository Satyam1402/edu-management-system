<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FranchiseWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'franchise_id',
        'balance'
    ];

    protected $casts = [
        'balance' => 'decimal:2'
    ];

    /**
     * Get the franchise that owns the wallet
     */
    public function franchise()
    {
        return $this->belongsTo(Franchise::class);
    }

    /**
     * Get all transactions for this wallet
     */
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class, 'franchise_wallet_id');
    }

    /**
     * Get completed transactions
     */
    public function completedTransactions()
    {
        return $this->transactions()->where('status', 'completed');
    }

    /**
     * Get total credits
     */
    public function getTotalCreditsAttribute()
    {
        return $this->completedTransactions()->where('type', 'credit')->sum('amount');
    }

    /**
     * Get total debits
     */
    public function getTotalDebitsAttribute()
    {
        return $this->completedTransactions()->where('type', 'debit')->sum('amount');
    }

    /**
     * Check if wallet has sufficient balance
     */
    public function hasSufficientBalance($amount)
    {
        return $this->balance >= $amount;
    }

    /**
     * Add funds to wallet
     */
    public function credit($amount, $description = null, $paymentMethod = null)
    {
        $this->balance += $amount;
        $this->save();

        return $this->transactions()->create([
            'type' => 'credit',
            'amount' => $amount,
            'description' => $description ?? 'Funds added to wallet',
            'status' => 'completed',
            'balance_after' => $this->balance,
            'payment_method' => $paymentMethod
        ]);
    }

    /**
     * Deduct funds from wallet
     */
    public function debit($amount, $description = null)
    {
        if (!$this->hasSufficientBalance($amount)) {
            throw new \Exception('Insufficient wallet balance');
        }

        $this->balance -= $amount;
        $this->save();

        return $this->transactions()->create([
            'type' => 'debit',
            'amount' => $amount,
            'description' => $description ?? 'Funds deducted from wallet',
            'status' => 'completed',
            'balance_after' => $this->balance
        ]);
    }
}
