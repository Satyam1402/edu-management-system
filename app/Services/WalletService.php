<?php

namespace App\Services;

use App\Models\Franchise;
use App\Models\FranchiseWallet;
use App\Models\WalletTransaction;
use App\Models\WalletAuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletService
{
    /**
     * Add credit to franchise wallet (Admin action)
     */
    public function addCredit(
        Franchise $franchise,
        float $amount,
        string $description = null,
        array $metadata = []
    ) {
        return DB::transaction(function () use ($franchise, $amount, $description, $metadata) {
            // Lock wallet to prevent race conditions
            $wallet = FranchiseWallet::lockForUpdate()
                ->where('franchise_id', $franchise->id)
                ->first();

            if (!$wallet) {
                throw new \Exception('Wallet not found for this franchise');
            }

            $balanceBefore = $wallet->balance;
            $balanceAfter = $balanceBefore + $amount;

            // Create transaction record
            $transaction = WalletTransaction::create([
                'franchise_wallet_id' => $wallet->id,
                'type' => 'credit',
                'amount' => $amount,
                'description' => $description ?? 'Admin credit added',
                'status' => 'completed',
                'balance_after' => $balanceAfter,
                'metadata' => array_merge($metadata, [
                    'performed_by' => auth()->id(),
                    'ip_address' => request()->ip(),
                    'timestamp' => now()->toDateTimeString()
                ])
            ]);

            // Update wallet balance
            $wallet->update(['balance' => $balanceAfter]);

            // Create audit log
            $this->createAuditLog(
                $franchise->id,
                'credit_added',
                $balanceBefore,
                $balanceAfter,
                $amount
            );

            return $transaction;
        });
    }

    /**
     * Deduct amount from wallet
     */
    public function deductAmount(
        Franchise $franchise,
        float $amount,
        string $description = null
    ) {
        return DB::transaction(function () use ($franchise, $amount, $description) {
            $wallet = FranchiseWallet::lockForUpdate()
                ->where('franchise_id', $franchise->id)
                ->first();

            if (!$wallet) {
                throw new \Exception('Wallet not found');
            }

            // Check sufficient balance
            if ($wallet->balance < $amount) {
                throw new \Exception('Insufficient wallet balance');
            }

            $balanceBefore = $wallet->balance;
            $balanceAfter = $balanceBefore - $amount;

            $transaction = WalletTransaction::create([
                'franchise_wallet_id' => $wallet->id,
                'type' => 'debit',
                'amount' => $amount,
                'description' => $description ?? 'Amount deducted',
                'status' => 'completed',
                'balance_after' => $balanceAfter,
                'metadata' => [
                    'performed_by' => auth()->id(),
                    'ip_address' => request()->ip()
                ]
            ]);

            $wallet->update(['balance' => $balanceAfter]);

            $this->createAuditLog(
                $franchise->id,
                'debit_deducted',
                $balanceBefore,
                $balanceAfter,
                $amount
            );

            return $transaction;
        });
    }

    /**
     * Get transaction history
     */
    public function getTransactionHistory($franchiseId, $limit = 50)
    {
        $wallet = FranchiseWallet::where('franchise_id', $franchiseId)->first();
        
        if (!$wallet) {
            return collect([]);
        }

        return WalletTransaction::where('franchise_wallet_id', $wallet->id)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Create audit log
     */
    protected function createAuditLog($franchiseId, $action, $oldBalance, $newBalance, $amount)
    {
        WalletAuditLog::create([
            'franchise_id' => $franchiseId,
            'action' => $action,
            'old_balance' => $oldBalance,
            'new_balance' => $newBalance,
            'amount_changed' => $amount,
            'performed_by' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'details' => [
                'timestamp' => now()->toDateTimeString(),
                'route' => request()->route()?->getName()
            ]
        ]);
    }
}
