<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    /**
     * Get wallet balance and recent transactions
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $wallet = $user->wallet()->with(['transactions' => function ($query) {
            $query->latest()->limit(10);
        }])->first();

        if (!$wallet) {
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $wallet,
        ]);
    }

    /**
     * Get wallet transaction history
     */
    public function transactions(Request $request)
    {
        $user = $request->user();
        $wallet = $user->wallet;

        if (!$wallet) {
            return response()->json([
                'success' => true,
                'data' => [
                    'transactions' => [],
                    'balance' => 0,
                ],
            ]);
        }

        $query = $wallet->transactions();

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => [
                'transactions' => $transactions,
                'balance' => $wallet->balance,
            ],
        ]);
    }

    /**
     * Add funds to wallet
     */
    public function addFunds(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
            'payment_method' => 'required|in:mobile_money,card',
            'payment_provider' => 'nullable|string',
        ]);

        $user = $request->user();
        $wallet = $user->wallet;

        if (!$wallet) {
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
            ]);
        }

        DB::beginTransaction();

        try {
            // TODO: Process payment with payment gateway
            // For now, simulate successful payment
            $transactionId = 'TOPUP-' . strtoupper(uniqid());

            $wallet->credit(
                $request->amount,
                'Rechargement du portefeuille',
                $transactionId
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Portefeuille rechargé avec succès',
                'data' => [
                    'wallet' => $wallet->fresh(),
                    'transaction_id' => $transactionId,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du rechargement: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get wallet statistics
     */
    public function statistics(Request $request)
    {
        $user = $request->user();
        $wallet = $user->wallet;

        if (!$wallet) {
            return response()->json([
                'success' => true,
                'data' => [
                    'balance' => 0,
                    'total_credits' => 0,
                    'total_debits' => 0,
                    'transaction_count' => 0,
                ],
            ]);
        }

        $credits = $wallet->transactions()->where('type', 'credit')->sum('amount');
        $debits = $wallet->transactions()->where('type', 'debit')->sum('amount');
        $count = $wallet->transactions()->count();

        return response()->json([
            'success' => true,
            'data' => [
                'balance' => $wallet->balance,
                'total_credits' => $credits,
                'total_debits' => $debits,
                'transaction_count' => $count,
            ],
        ]);
    }
}


