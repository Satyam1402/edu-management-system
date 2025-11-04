<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\FranchiseWallet;
use App\Models\FranchiseWalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class WalletController extends Controller
{
    public function index(Request $request)
{
    $franchiseId = auth()->user()->franchise_id;

    // === AJAX REQUEST === Return JSON for DataTables
    if ($request->ajax()) {
        try {
            $transactions = FranchiseWalletTransaction::where('franchise_id', $franchiseId)
                ->orderBy('created_at', 'desc');

            return DataTables::of($transactions)
                ->addIndexColumn()
                ->editColumn('type', function($row) {
                    $badge = $row->type === 'credit' ? 'success' : 'danger';
                    return '<span class="badge badge-'.$badge.'">'.ucfirst($row->type).'</span>';
                })
                ->editColumn('formatted_amount', function($row) {
                    $color = $row->type === 'credit' ? 'success' : 'danger';
                    $sign = $row->type === 'credit' ? '+' : '-';
                    return '<strong class="text-'.$color.'">'.$sign.' ₹'.number_format($row->amount, 2).'</strong>';
                })
                ->editColumn('payment_method', function($row) {
                    return '<span class="badge badge-secondary">'.ucfirst($row->source ?? 'N/A').'</span>';
                })
                ->editColumn('status_badge', function($row) {
                    $statusClass = $row->status === 'completed' ? 'success' : ($row->status === 'pending' ? 'warning' : 'danger');
                    return '<span class="badge badge-'.$statusClass.'">'.ucfirst($row->status ?? 'Completed').'</span>';
                })
                ->editColumn('formatted_date', function($row) {
                    return '<div><strong>'.$row->created_at->format('M d, Y').'</strong><br><small class="text-muted">'.$row->created_at->format('h:i A').'</small></div>';
                })
                ->addColumn('action', function($row) {
                    // THIS IS THE FIX - Link to show route
                    $showUrl = route('franchise.wallet.show', $row->id);
                    return '<a href="'.$showUrl.'" class="btn btn-info btn-sm" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>';
                })
                ->rawColumns(['type', 'formatted_amount', 'payment_method', 'status_badge', 'formatted_date', 'action'])
                ->make(true);

        } catch (\Exception $e) {
            \Log::error('Wallet DataTable Error: ' . $e->getMessage());
            return response()->json([
                'draw' => intval($request->get('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Unable to load transactions'
            ], 500);
        }
    }

    // === NON-AJAX REQUEST === Return Blade View
    $wallet = FranchiseWallet::firstOrCreate(['franchise_id' => $franchiseId], ['balance' => 0]);

    $thisMonthWallet = FranchiseWalletTransaction::where('franchise_id', $franchiseId)
        ->whereYear('created_at', now()->year)
        ->whereMonth('created_at', now()->month)
        ->where('type', 'credit')
        ->sum('amount');

    $totalCredits = FranchiseWalletTransaction::where('franchise_id', $franchiseId)
        ->where('type', 'credit')
        ->sum('amount');

    $totalDebits = FranchiseWalletTransaction::where('franchise_id', $franchiseId)
        ->where('type', 'debit')
        ->sum('amount');

    return view('franchise.wallet.index', compact(
        'wallet', 
        'thisMonthWallet', 
        'totalCredits', 
        'totalDebits'
    ));
}


     public function create()
    {
        $franchiseId = auth()->user()->franchise_id;
        $wallet = FranchiseWallet::firstOrCreate(
            ['franchise_id' => $franchiseId],
            ['balance' => 0]
        );

        return view('franchise.wallet.create', compact('wallet'));
    }

    /**
     * Process the wallet top-up (store funds)
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100|max:100000',
            'payment_method' => 'required|in:razorpay,upi,bank_transfer'
        ]);

        $franchiseId = auth()->user()->franchise_id;
        $amount = $request->amount;

        try {
            DB::beginTransaction();

            // Create a pending wallet transaction
            $transaction = FranchiseWalletTransaction::create([
                'franchise_id' => $franchiseId,
                'type' => 'credit',
                'amount' => $amount,
                'source' => 'wallet_topup',
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'meta' => json_encode([
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'initiated_at' => now()
                ])
            ]);

            DB::commit();

            // Redirect to payment gateway based on method
            if ($request->payment_method === 'razorpay') {
                return $this->initiateRazorpayPayment($transaction);
            } elseif ($request->payment_method === 'upi') {
                return $this->initiateUpiPayment($transaction);
            } else {
                return redirect()
                    ->route('franchise.wallet.index')
                    ->with('info', 'Bank transfer details have been sent to your email.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Wallet Top-up Error: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Failed to initiate payment. Please try again.');
        }
    }

    /**
 * Display transaction details
 */
public function show($id)
{
    $franchiseId = auth()->user()->franchise_id;
    
    $transaction = FranchiseWalletTransaction::where('franchise_id', $franchiseId)
        ->where('id', $id)
        ->firstOrFail();
    
    return view('franchise.wallet.show', compact('transaction'));
}

/**
 * Download transaction receipt (optional)
 */
public function downloadReceipt($id)
{
    $franchiseId = auth()->user()->franchise_id;
    
    $transaction = FranchiseWalletTransaction::where('franchise_id', $franchiseId)
        ->where('id', $id)
        ->where('status', 'completed')
        ->firstOrFail();
    
    // You can generate PDF here using dompdf or similar
    // For now, redirect to show page
    return redirect()->route('franchise.wallet.show', $transaction->id);
}


    /**
     * Initiate Razorpay payment for wallet top-up
     */
    private function initiateRazorpayPayment($transaction)
    {
        // Razorpay integration logic here
        $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        $order = $api->order->create([
            'amount' => $transaction->amount * 100, // Amount in paise
            'currency' => 'INR',
            'receipt' => 'wallet_' . $transaction->id,
            'notes' => [
                'franchise_id' => $transaction->franchise_id,
                'type' => 'wallet_topup'
            ]
        ]);

        return view('franchise.wallet.razorpay', compact('transaction', 'order'));
    }

    /**
     * Initiate UPI payment for wallet top-up
     */
    private function initiateUpiPayment($transaction)
    {
        // Generate UPI payment link or QR code
        $upiId = config('services.upi.id'); // Your UPI ID
        $upiString = "upi://pay?pa={$upiId}&pn=YourCompany&am={$transaction->amount}&cu=INR&tn=Wallet-TopUp-{$transaction->id}";

        return view('franchise.wallet.upi', compact('transaction', 'upiString'));
    }

    /**
     * Handle Razorpay payment callback
     */
    public function verifyRazorpay(Request $request)
    {
        // Verify Razorpay signature and update transaction
        try {
            $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            $api->utility->verifyPaymentSignature($attributes);

            // Payment verified - credit wallet
            $transactionId = $request->transaction_id;
            $transaction = FranchiseWalletTransaction::findOrFail($transactionId);

            DB::transaction(function() use ($transaction) {
                $wallet = FranchiseWallet::where('franchise_id', $transaction->franchise_id)->first();
                $wallet->increment('balance', $transaction->amount);
                
                $transaction->update([
                    'status' => 'completed',
                    'completed_at' => now()
                ]);
            });

            return redirect()
                ->route('franchise.wallet.index')
                ->with('success', 'Wallet credited successfully! ₹' . number_format($transaction->amount, 2));

        } catch (\Exception $e) {
            \Log::error('Razorpay Verification Failed: ' . $e->getMessage());
            
            return redirect()
                ->route('franchise.wallet.index')
                ->with('error', 'Payment verification failed.');
        }
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
