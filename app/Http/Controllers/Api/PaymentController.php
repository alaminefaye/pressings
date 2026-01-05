<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\LoyaltyPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Process payment for an order
     */
    public function process(Request $request, $orderId)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,mobile_money,wallet',
            'payment_provider' => 'nullable|string',
        ]);

        $order = Order::with('payment')->findOrFail($orderId);
        $user = $request->user();

        // Check authorization
        if ($order->client_id !== $user->id && !$user->isAdmin() && !$user->isEmployee()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 403);
        }

        // Check if already paid
        if ($order->payment->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Cette commande a déjà été payée',
            ], 422);
        }

        DB::beginTransaction();

        try {
            $payment = $order->payment;
            $payment->payment_method = $request->payment_method;
            $payment->payment_provider = $request->payment_provider;

            switch ($request->payment_method) {
                case 'cash':
                    // Cash payment will be confirmed on delivery
                    $payment->status = 'pending';
                    break;

                case 'mobile_money':
                    // Integrate with Mobile Money API
                    $result = $this->processMobileMoney($order, $request->payment_provider);
                    if ($result['success']) {
                        $payment->status = 'completed';
                        $payment->transaction_id = $result['transaction_id'];
                        $payment->paid_at = now();
                    } else {
                        throw new \Exception($result['message']);
                    }
                    break;

                case 'wallet':
                    // Process wallet payment
                    $wallet = $user->wallet;
                    if (!$wallet || !$wallet->hasBalance($order->total)) {
                        throw new \Exception('Solde insuffisant dans le portefeuille');
                    }

                    $wallet->debit(
                        $order->total,
                        "Paiement commande {$order->order_number}",
                        $order->id
                    );

                    $payment->status = 'completed';
                    $payment->paid_at = now();
                    break;
            }

            $payment->save();

            // Award loyalty points if payment is completed
            if ($payment->status === 'completed') {
                $this->awardLoyaltyPoints($user, $order);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Paiement traité avec succès',
                'data' => [
                    'payment' => $payment,
                    'order' => $order->fresh(),
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du paiement: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Confirm cash payment (admin/employee only)
     */
    public function confirmCash(Request $request, $orderId)
    {
        $order = Order::with('payment')->findOrFail($orderId);

        if ($order->payment->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Ce paiement a déjà été confirmé',
            ], 422);
        }

        DB::beginTransaction();

        try {
            $payment = $order->payment;
            $payment->markAsCompleted();

            // Award loyalty points
            $this->awardLoyaltyPoints($order->client, $order);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Paiement confirmé',
                'data' => $payment,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get payment history
     */
    public function history(Request $request)
    {
        $user = $request->user();
        $query = Payment::with('order');

        if ($user->isClient()) {
            $query->whereHas('order', function ($q) use ($user) {
                $q->where('client_id', $user->id);
            });
        }

        $payments = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $payments,
        ]);
    }

    /**
     * Request refund
     */
    public function refund(Request $request, $orderId)
    {
        $request->validate([
            'reason' => 'required|string',
        ]);

        $order = Order::with('payment')->findOrFail($orderId);

        if ($order->payment->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Aucun paiement à rembourser',
            ], 422);
        }

        DB::beginTransaction();

        try {
            $payment = $order->payment;

            // Process refund based on payment method
            if ($payment->payment_method === 'wallet') {
                $wallet = $order->client->wallet;
                $wallet->credit(
                    $payment->amount,
                    "Remboursement commande {$order->order_number}",
                    $order->id
                );
            }

            $payment->status = 'refunded';
            $payment->save();

            // Deduct loyalty points if any were awarded
            $loyaltyPoints = $order->client->loyaltyPoints;
            if ($loyaltyPoints) {
                $pointsToDeduct = floor($order->total / 100); // 1 point per 100 FCFA
                if ($loyaltyPoints->points >= $pointsToDeduct) {
                    $loyaltyPoints->expire($pointsToDeduct, "Remboursement commande {$order->order_number}");
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Remboursement effectué',
                'data' => $payment,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du remboursement: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process Mobile Money payment (placeholder)
     */
    protected function processMobileMoney($order, $provider)
    {
        // TODO: Integrate with actual Mobile Money APIs
        // - Orange Money API
        // - MTN Mobile Money API
        // - Wave API
        // etc.

        // For now, simulate successful payment
        return [
            'success' => true,
            'transaction_id' => 'MM-' . strtoupper(uniqid()),
            'message' => 'Paiement Mobile Money simulé',
        ];

        /* Example integration structure:
        
        switch ($provider) {
            case 'orange':
                return $this->processOrangeMoney($order);
            case 'mtn':
                return $this->processMTNMoney($order);
            case 'wave':
                return $this->processWave($order);
            default:
                return ['success' => false, 'message' => 'Provider not supported'];
        }
        */
    }

    /**
     * Award loyalty points for completed order
     */
    protected function awardLoyaltyPoints($user, $order)
    {
        $loyaltyEnabled = config('app.loyalty_enabled', true);
        if (!$loyaltyEnabled) {
            return;
        }

        $pointsPerAmount = config('app.loyalty_points_per_amount', 100);
        $points = floor($order->total / $pointsPerAmount);

        if ($points > 0) {
            $loyaltyPoints = LoyaltyPoint::firstOrCreate(
                ['user_id' => $user->id],
                ['points' => 0, 'total_earned' => 0, 'total_spent' => 0]
            );

            $loyaltyPoints->earn($points, $order->id, "Points gagnés pour la commande {$order->order_number}");
        }
    }
}


