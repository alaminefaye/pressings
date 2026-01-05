<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Price;
use App\Models\Promotion;
use App\Models\PromotionUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Order::with(['client', 'items.service', 'items.clothingType', 'payment', 'delivery']);

        // Filter by role
        if ($user->isClient()) {
            $query->where('client_id', $user->id);
        } elseif ($user->isEmployee()) {
            $query->where('employee_id', $user->id);
        } elseif ($user->isDriver()) {
            $query->whereHas('delivery', function ($q) use ($user) {
                $q->where('driver_id', $user->id);
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by order number
        if ($request->has('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        // Date range filter
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $orders = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        $request->validate([
            'delivery_type' => 'required|in:pickup,home_delivery',
            'pickup_address_id' => 'nullable|exists:addresses,id',
            'delivery_address_id' => 'nullable|exists:addresses,id',
            'pickup_date' => 'nullable|date|after:now',
            'delivery_date' => 'nullable|date|after:pickup_date',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'required|exists:services,id',
            'items.*.clothing_type_id' => 'required|exists:clothing_types,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
            'special_instructions' => 'nullable|string',
            'promotion_code' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $user = $request->user();
            $subtotal = 0;
            $itemsData = [];

            // Calculate prices for each item
            foreach ($request->items as $item) {
                $price = Price::where('service_id', $item['service_id'])
                    ->where('clothing_type_id', $item['clothing_type_id'])
                    ->where('is_active', true)
                    ->firstOrFail();

                $itemSubtotal = $price->price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $itemsData[] = [
                    'service_id' => $item['service_id'],
                    'clothing_type_id' => $item['clothing_type_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $price->price,
                    'subtotal' => $itemSubtotal,
                    'notes' => $item['notes'] ?? null,
                ];
            }

            // Apply promotion if provided
            $discount = 0;
            $promotion = null;
            if ($request->has('promotion_code')) {
                $promotion = Promotion::where('code', $request->promotion_code)->first();
                
                if ($promotion && $promotion->isValid()) {
                    $discount = $promotion->calculateDiscount($subtotal);
                }
            }

            // Calculate delivery fee
            $deliveryFee = 0;
            if ($request->delivery_type === 'home_delivery') {
                $deliveryFee = config('app.delivery_fee', 1000);
                
                // Free delivery threshold
                $freeDeliveryThreshold = config('app.free_delivery_threshold', 10000);
                if ($subtotal >= $freeDeliveryThreshold) {
                    $deliveryFee = 0;
                }
            }

            // Calculate total
            $total = $subtotal - $discount + $deliveryFee;

            // Create order
            $order = Order::create([
                'client_id' => $user->id,
                'delivery_type' => $request->delivery_type,
                'pickup_address_id' => $request->pickup_address_id,
                'delivery_address_id' => $request->delivery_address_id,
                'pickup_date' => $request->pickup_date,
                'delivery_date' => $request->delivery_date,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'special_instructions' => $request->special_instructions,
            ]);

            // Create order items
            foreach ($itemsData as $itemData) {
                $order->items()->create($itemData);
            }

            // Record promotion usage
            if ($promotion && $discount > 0) {
                PromotionUsage::create([
                    'promotion_id' => $promotion->id,
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'discount_amount' => $discount,
                ]);
                
                $promotion->incrementUsage();
            }

            // Create payment record
            $order->payment()->create([
                'payment_method' => 'cash',
                'amount' => $total,
                'status' => 'pending',
            ]);

            DB::commit();

            // Load relationships
            $order->load(['items.service', 'items.clothingType', 'payment']);

            return response()->json([
                'success' => true,
                'message' => 'Commande créée avec succès',
                'data' => $order,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la commande: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified order
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $query = Order::with([
            'client',
            'employee',
            'items.service',
            'items.clothingType',
            'payment',
            'delivery.driver',
            'delivery.address',
            'pickupAddress',
            'deliveryAddress',
            'statusHistory.user',
        ]);

        // Apply role-based access control
        if ($user->isClient()) {
            $query->where('client_id', $user->id);
        } elseif ($user->isEmployee()) {
            // Employees can see all orders or only assigned ones
            // Adjust based on your business logic
        } elseif ($user->isDriver()) {
            $query->whereHas('delivery', function ($q) use ($user) {
                $q->where('driver_id', $user->id);
            });
        }

        $order = $query->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,received,washing,ironing,ready,in_delivery,delivered,cancelled',
            'comment' => 'nullable|string',
        ]);

        $order = Order::findOrFail($id);
        
        $order->updateStatus($request->status, $request->user()->id, $request->comment);

        return response()->json([
            'success' => true,
            'message' => 'Statut de la commande mis à jour',
            'data' => $order->load('statusHistory.user'),
        ]);
    }

    /**
     * Assign employee to order
     */
    public function assignEmployee(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
        ]);

        $order = Order::findOrFail($id);
        $order->employee_id = $request->employee_id;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Employé assigné à la commande',
            'data' => $order->load('employee'),
        ]);
    }

    /**
     * Cancel order
     */
    public function cancel(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $user = $request->user();

        // Check if user can cancel
        if ($user->isClient() && $order->client_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas annuler cette commande',
            ], 403);
        }

        // Check if order can be cancelled
        if (in_array($order->status, ['delivered', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cette commande ne peut pas être annulée',
            ], 422);
        }

        $order->updateStatus('cancelled', $user->id, $request->reason);

        return response()->json([
            'success' => true,
            'message' => 'Commande annulée avec succès',
            'data' => $order,
        ]);
    }

    /**
     * Get order statistics
     */
    public function statistics(Request $request)
    {
        $user = $request->user();
        $query = Order::query();

        // Apply role-based filter
        if ($user->isClient()) {
            $query->where('client_id', $user->id);
        }

        $stats = [
            'total_orders' => $query->count(),
            'pending_orders' => (clone $query)->where('status', 'pending')->count(),
            'in_progress_orders' => (clone $query)->whereIn('status', ['washing', 'ironing'])->count(),
            'ready_orders' => (clone $query)->where('status', 'ready')->count(),
            'completed_orders' => (clone $query)->where('status', 'delivered')->count(),
            'cancelled_orders' => (clone $query)->where('status', 'cancelled')->count(),
            'total_amount' => (clone $query)->sum('total'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}


