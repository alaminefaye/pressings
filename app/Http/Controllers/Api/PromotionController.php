<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    /**
     * Display a listing of promotions
     */
    public function index(Request $request)
    {
        $query = Promotion::query();

        // Filter by active status
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        // Filter by available promotions only
        if ($request->boolean('available')) {
            $query->available();
        }

        $promotions = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $promotions,
        ]);
    }

    /**
     * Store a newly created promotion
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'nullable|string|unique:promotions,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'required|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        $promotion = Promotion::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Promotion créée avec succès',
            'data' => $promotion,
        ], 201);
    }

    /**
     * Display the specified promotion
     */
    public function show($id)
    {
        $promotion = Promotion::with('usages')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $promotion,
        ]);
    }

    /**
     * Update the specified promotion
     */
    public function update(Request $request, $id)
    {
        $promotion = Promotion::findOrFail($id);

        $request->validate([
            'code' => 'sometimes|string|unique:promotions,code,' . $id,
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:percentage,fixed_amount',
            'value' => 'sometimes|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'sometimes|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        $promotion->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Promotion mise à jour avec succès',
            'data' => $promotion,
        ]);
    }

    /**
     * Remove the specified promotion
     */
    public function destroy($id)
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Promotion supprimée avec succès',
        ]);
    }

    /**
     * Validate and apply a promotion code
     */
    public function validateCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'order_amount' => 'required|numeric|min:0',
        ]);

        $promotion = Promotion::where('code', $request->code)->first();

        if (!$promotion) {
            return response()->json([
                'success' => false,
                'message' => 'Code promo invalide',
            ], 404);
        }

        if (!$promotion->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Ce code promo n\'est plus valide',
            ], 422);
        }

        $discount = $promotion->calculateDiscount($request->order_amount);

        if ($discount == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Montant minimum requis: ' . $promotion->min_order_amount,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Code promo valide',
            'data' => [
                'promotion' => $promotion,
                'discount_amount' => $discount,
                'final_amount' => max(0, $request->order_amount - $discount),
            ],
        ]);
    }

    /**
     * Get promotion statistics
     */
    public function stats($id)
    {
        $promotion = Promotion::with('usages')->findOrFail($id);

        $stats = [
            'total_usages' => $promotion->usage_count,
            'total_discount_given' => $promotion->usages->sum('discount_amount'),
            'remaining_usages' => $promotion->usage_limit ? ($promotion->usage_limit - $promotion->usage_count) : null,
            'is_valid' => $promotion->isValid(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'promotion' => $promotion,
                'stats' => $stats,
            ],
        ]);
    }
}

