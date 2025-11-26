<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Franchise;
use App\Models\FranchiseWallet;
use App\Models\WalletTransaction;
use App\Models\WalletRechargeRequest;
use App\Models\WalletAuditLog;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Admin Wallet Dashboard
     */
    public function index()
    {
        $stats = [
            'total_balance' => FranchiseWallet::sum('balance'),
            'total_franchises' => FranchiseWallet::count(),
            'pending_recharges' => WalletRechargeRequest::where('status', 'pending')->count(),
            'today_transactions' => WalletTransaction::whereDate('created_at', today())->count(),
            'total_credits' => WalletTransaction::where('type', 'credit')
                ->where('status', 'completed')
                ->sum('amount'),
            'total_debits' => WalletTransaction::where('type', 'debit')
                ->where('status', 'completed')
                ->sum('amount'),
        ];

        // Recent transactions
        $recentTransactions = WalletTransaction::with(['wallet.franchise'])
            ->latest()
            ->limit(10)
            ->get();

        // Top franchises by wallet balance
        $topFranchises = FranchiseWallet::with('franchise')
            ->orderBy('balance', 'desc')
            ->limit(5)
            ->get();

        return view('admin.wallet.index', compact('stats', 'recentTransactions', 'topFranchises'));
    }

    /**
     * All Transactions (DataTables)
     */
    public function transactions(Request $request)
    {
        if ($request->ajax()) {
            $query = WalletTransaction::with(['wallet.franchise'])
                ->latest();

            // Apply filters
            if ($request->filled('franchise_id')) {
                $query->whereHas('wallet', function($q) use ($request) {
                    $q->where('franchise_id', $request->franchise_id);
                });
            }

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            return DataTables::of($query)
                ->addColumn('franchise_name', function ($transaction) {
                    return $transaction->wallet->franchise->name ?? 'N/A';
                })
                ->addColumn('type_badge', function ($transaction) {
                    $color = $transaction->type === 'credit' ? 'success' : 'danger';
                    $icon = $transaction->type === 'credit' ? 'arrow-up' : 'arrow-down';
                    return '<span class="badge badge-' . $color . '">
                                <i class="fas fa-' . $icon . '"></i> ' . ucfirst($transaction->type) . '
                            </span>';
                })
                ->addColumn('amount_formatted', function ($transaction) {
                    $symbol = $transaction->type === 'credit' ? '+' : '-';
                    $color = $transaction->type === 'credit' ? 'text-success' : 'text-danger';
                    return '<strong class="' . $color . '">' . $symbol . '₹' . number_format($transaction->amount, 2) . '</strong>';
                })
                ->addColumn('balance_after_formatted', function ($transaction) {
                    return '₹' . number_format($transaction->balance_after, 2);
                })
                ->addColumn('status_badge', function ($transaction) {
                    $colors = [
                        'completed' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'refunded' => 'info'
                    ];
                    $color = $colors[$transaction->status] ?? 'secondary';
                    return '<span class="badge badge-' . $color . '">' . ucfirst($transaction->status) . '</span>';
                })
                ->addColumn('date_formatted', function ($transaction) {
                    return $transaction->created_at->format('M d, Y g:i A');
                })
                ->addColumn('actions', function ($transaction) {
                    return '<button class="btn btn-sm btn-info" onclick="viewTransaction(' . $transaction->id . ')">
                                <i class="fas fa-eye"></i> View
                            </button>';
                })
                ->rawColumns(['type_badge', 'amount_formatted', 'status_badge', 'actions'])
                ->make(true);
        }

        $franchises = Franchise::orderBy('name')->get();
        return view('admin.wallet.transactions', compact('franchises'));
    }

    /**
     * Wallet Recharge Requests
     */
    public function rechargeRequests(Request $request)
    {
        if ($request->ajax()) {
            $query = WalletRechargeRequest::with(['franchise', 'verifiedBy'])
                ->latest('requested_at');

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->addColumn('franchise_name', function ($req) {
                    return $req->franchise->name;
                })
                ->addColumn('amount_formatted', function ($req) {
                    return '₹' . number_format($req->amount, 2);
                })
                ->addColumn('payment_info', function ($req) {
                    $html = '<div>';
                    $html .= '<strong>' . ucfirst(str_replace('_', ' ', $req->payment_method)) . '</strong><br>';
                    if ($req->payment_reference) {
                        $html .= '<small class="text-muted">Ref: ' . $req->payment_reference . '</small>';
                    }
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('status_badge', function ($req) {
                    $colors = [
                        'pending' => 'warning',
                        'verified' => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger'
                    ];
                    $color = $colors[$req->status] ?? 'secondary';
                    return '<span class="badge badge-' . $color . '">' . ucfirst($req->status) . '</span>';
                })
                ->addColumn('requested_date', function ($req) {
                    return $req->requested_at->format('M d, Y g:i A');
                })
                ->addColumn('actions', function ($req) {
                    $actions = '<div class="btn-group btn-group-sm">';
                    
                    if ($req->isPending() || $req->isVerified()) {
                        $actions .= '<button class="btn btn-success" onclick="approveRecharge(' . $req->id . ')">
                                        <i class="fas fa-check"></i> Approve
                                    </button>';
                        $actions .= '<button class="btn btn-danger" onclick="rejectRecharge(' . $req->id . ')">
                                        <i class="fas fa-times"></i> Reject
                                    </button>';
                    }
                    
                    $actions .= '<button class="btn btn-info" onclick="viewRecharge(' . $req->id . ')">
                                    <i class="fas fa-eye"></i> View
                                </button>';
                    
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['payment_info', 'status_badge', 'actions'])
                ->make(true);
        }

        $stats = [
            'pending' => WalletRechargeRequest::pending()->count(),
            'approved' => WalletRechargeRequest::approved()->count(),
            'rejected' => WalletRechargeRequest::rejected()->count(),
        ];

        return view('admin.wallet.recharge-requests', compact('stats'));
    }

    /**
     * Approve Recharge Request
     */
    public function approveRecharge(Request $request, $id)
    {
        $request->validate([
            'admin_remarks' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $rechargeRequest = WalletRechargeRequest::findOrFail($id);

            if (!$rechargeRequest->canBeApproved()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This request cannot be approved'
                ], 400);
            }

            $franchise = $rechargeRequest->franchise;

            // Add credit to wallet
            $this->walletService->addCredit(
                $franchise,
                $rechargeRequest->amount,
                'Wallet recharge approved - Ref: ' . $rechargeRequest->payment_reference,
                [
                    'recharge_request_id' => $rechargeRequest->id,
                    'payment_method' => $rechargeRequest->payment_method
                ]
            );

            // Update recharge request status
            $rechargeRequest->update([
                'status' => 'approved',
                'verified_at' => now(),
                'verified_by' => auth()->id(),
                'admin_remarks' => $request->admin_remarks
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Recharge request approved successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject Recharge Request
     */
    public function rejectRecharge(Request $request, $id)
    {
        $request->validate([
            'admin_remarks' => 'required|string|max:500'
        ]);

        $rechargeRequest = WalletRechargeRequest::findOrFail($id);

        if (!$rechargeRequest->canBeRejected()) {
            return response()->json([
                'success' => false,
                'message' => 'This request cannot be rejected'
            ], 400);
        }

        $rechargeRequest->update([
            'status' => 'rejected',
            'verified_at' => now(),
            'verified_by' => auth()->id(),
            'admin_remarks' => $request->admin_remarks
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Recharge request rejected'
        ]);
    }

    /**
     * Manual Credit/Debit Form
     */
    public function manualTransaction()
    {
        $franchises = Franchise::with('wallet')->orderBy('name')->get();
        return view('admin.wallet.manual-transaction', compact('franchises'));
    }

    /**
     * Process Manual Credit/Debit
     */
    public function processManualTransaction(Request $request)
    {
        $request->validate([
            'franchise_id' => 'required|exists:franchises,id',
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255'
        ]);

        try {
            $franchise = Franchise::findOrFail($request->franchise_id);

            if ($request->type === 'credit') {
                $transaction = $this->walletService->addCredit(
                    $franchise,
                    $request->amount,
                    $request->description,
                    ['manual_transaction' => true]
                );
            } else {
                $transaction = $this->walletService->deductAmount(
                    $franchise,
                    $request->amount,
                    $request->description
                );
            }

            return response()->json([
                'success' => true,
                'message' => ucfirst($request->type) . ' processed successfully',
                'transaction' => $transaction
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Franchise Wallet Details
     */
    public function franchiseWallet($franchiseId)
    {
        $franchise = Franchise::with('wallet')->findOrFail($franchiseId);
        
        $transactions = $this->walletService->getTransactionHistory($franchiseId, 100);
        
        $stats = [
            'total_credits' => $transactions->where('type', 'credit')->sum('amount'),
            'total_debits' => $transactions->where('type', 'debit')->sum('amount'),
            'transaction_count' => $transactions->count(),
        ];

        return view('admin.wallet.franchise-details', compact('franchise', 'transactions', 'stats'));
    }

    /**
     * Audit Logs
     */
    public function auditLogs(Request $request)
    {
        if ($request->ajax()) {
            $query = WalletAuditLog::with(['franchise', 'performedBy'])
                ->latest('created_at');

            if ($request->filled('franchise_id')) {
                $query->where('franchise_id', $request->franchise_id);
            }

            if ($request->filled('performed_by')) {
                $query->where('performed_by', $request->performed_by);
            }

            return DataTables::of($query)
                ->addColumn('franchise_name', function ($log) {
                    return $log->franchise->name ?? 'N/A';
                })
                ->addColumn('action_formatted', function ($log) {
                    return ucwords(str_replace('_', ' ', $log->action));
                })
                ->addColumn('balance_change', function ($log) {
                    if ($log->old_balance !== null && $log->new_balance !== null) {
                        $change = $log->new_balance - $log->old_balance;
                        $color = $change >= 0 ? 'success' : 'danger';
                        return '<span class="text-' . $color . '">₹' . number_format($log->old_balance, 2) . 
                               ' → ₹' . number_format($log->new_balance, 2) . '</span>';
                    }
                    return 'N/A';
                })
                ->addColumn('performed_by_name', function ($log) {
                    return $log->performedBy->name ?? 'System';
                })
                ->addColumn('timestamp', function ($log) {
                    return $log->created_at->format('M d, Y g:i A');
                })
                ->rawColumns(['balance_change'])
                ->make(true);
        }

        $franchises = Franchise::orderBy('name')->get();
        return view('admin.wallet.audit-logs', compact('franchises'));
    }
}
