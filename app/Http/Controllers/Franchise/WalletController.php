<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\FranchiseWallet;
use App\Models\FranchiseWalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    // Show wallet balance and transactions
    public function index()
{
    $franchiseId = auth()->user()->franchise_id;

    // All wallet data for the franchise
    $wallet = FranchiseWallet::firstOrCreate(['franchise_id' => $franchiseId], ['balance' => 0]);

    // All transactions for this franchise
    $transactions = FranchiseWalletTransaction::where('franchise_id', $franchiseId)
        ->orderBy('created_at', 'desc')
        ->paginate(20);

    // Calculate stats
    $thisMonthWallet = FranchiseWalletTransaction::where('franchise_id', $franchiseId)
        ->whereYear('created_at', now()->year)
        ->whereMonth('created_at', now()->month)
        ->sum('amount');

    $totalCredits = FranchiseWalletTransaction::where('franchise_id', $franchiseId)
        ->where('type', 'credit')
        ->sum('amount');

    $totalDebits = FranchiseWalletTransaction::where('franchise_id', $franchiseId)
        ->where('type', 'debit')
        ->sum('amount');

    return view('franchise.wallet.index', compact(
        'wallet', 
        'transactions', 
        'thisMonthWallet', 
        'totalCredits', 
        'totalDebits'
    ));
}



    // Credit wallet after successful payment
    public function creditFromPayment($franchiseId, $amount, $paymentId = null)
    {
        DB::transaction(function () use ($franchiseId, $amount, $paymentId) {
            $wallet = FranchiseWallet::firstOrCreate(
                ['franchise_id' => $franchiseId],
                ['balance' => 0]
            );

            $wallet->increment('balance', $amount);

            FranchiseWalletTransaction::create([
                'franchise_id' => $franchiseId,
                'type' => 'credit',
                'amount' => $amount,
                'source' => 'payment',
                'reference_id' => $paymentId
            ]);
        });
    }


    // Deduct from wallet for batch certificate request
    public function deductForCertificateBatch($franchiseId, $amount, $batchMeta = [])
    {
        $wallet = FranchiseWallet::where('franchise_id', $franchiseId)->first();

        if (!$wallet || $wallet->balance < $amount) {
            return false; // or throw exception for insufficient balance
        }

        DB::transaction(function () use ($wallet, $amount, $batchMeta) {
            $wallet->decrement('balance', $amount);

            FranchiseWalletTransaction::create([
                'franchise_id' => $wallet->franchise_id,
                'type' => 'debit',
                'amount' => $amount,
                'source' => 'certificate_batch',
                'meta' => $batchMeta
            ]);
        });

        return true;
    }

    public function getWalletByFranchise($franchiseId)
    {
        return \App\Models\FranchiseWallet::where('franchise_id', $franchiseId)->first();
    }

}
