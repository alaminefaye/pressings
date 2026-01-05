<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Price;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    /**
     * Display a listing of prices
     */
    public function index(Request $request)
    {
        $query = Price::with(['service', 'clothingType']);

        // Filter by service
        if ($request->has('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        // Filter by clothing type
        if ($request->has('clothing_type_id')) {
            $query->where('clothing_type_id', $request->clothing_type_id);
        }

        // Filter by active status
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        $prices = $query->get();

        return response()->json([
            'success' => true,
            'data' => $prices,
        ]);
    }

    /**
     * Get price matrix (all services x all clothing types)
     */
    public function matrix()
    {
        $prices = Price::with(['service', 'clothingType'])
            ->where('is_active', true)
            ->get()
            ->groupBy('service_id');

        return response()->json([
            'success' => true,
            'data' => $prices,
        ]);
    }

    /**
     * Store a newly created price
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'clothing_type_id' => 'required|exists:clothing_types,id',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // Check if price already exists
        $existingPrice = Price::where('service_id', $request->service_id)
            ->where('clothing_type_id', $request->clothing_type_id)
            ->first();

        if ($existingPrice) {
            return response()->json([
                'success' => false,
                'message' => 'Un prix existe déjà pour cette combinaison service/vêtement',
            ], 422);
        }

        $price = Price::create($request->all());
        $price->load(['service', 'clothingType']);

        return response()->json([
            'success' => true,
            'message' => 'Prix créé avec succès',
            'data' => $price,
        ], 201);
    }

    /**
     * Display the specified price
     */
    public function show($id)
    {
        $price = Price::with(['service', 'clothingType'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $price,
        ]);
    }

    /**
     * Update the specified price
     */
    public function update(Request $request, $id)
    {
        $price = Price::findOrFail($id);

        $request->validate([
            'price' => 'sometimes|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $price->update($request->all());
        $price->load(['service', 'clothingType']);

        return response()->json([
            'success' => true,
            'message' => 'Prix mis à jour avec succès',
            'data' => $price,
        ]);
    }

    /**
     * Remove the specified price
     */
    public function destroy($id)
    {
        $price = Price::findOrFail($id);
        $price->delete();

        return response()->json([
            'success' => true,
            'message' => 'Prix supprimé avec succès',
        ]);
    }

    /**
     * Bulk update prices
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'prices' => 'required|array',
            'prices.*.id' => 'required|exists:prices,id',
            'prices.*.price' => 'required|numeric|min:0',
        ]);

        foreach ($request->prices as $priceData) {
            Price::where('id', $priceData['id'])->update([
                'price' => $priceData['price'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Prix mis à jour avec succès',
        ]);
    }

    /**
     * Calculate price for order items
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.service_id' => 'required|exists:services,id',
            'items.*.clothing_type_id' => 'required|exists:clothing_types,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $totalAmount = 0;
        $itemsWithPrices = [];

        foreach ($request->items as $item) {
            $price = Price::where('service_id', $item['service_id'])
                ->where('clothing_type_id', $item['clothing_type_id'])
                ->where('is_active', true)
                ->first();

            if (!$price) {
                return response()->json([
                    'success' => false,
                    'message' => 'Prix non trouvé pour un des articles',
                ], 404);
            }

            $subtotal = $price->price * $item['quantity'];
            $totalAmount += $subtotal;

            $itemsWithPrices[] = [
                'service_id' => $item['service_id'],
                'clothing_type_id' => $item['clothing_type_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $price->price,
                'subtotal' => $subtotal,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $itemsWithPrices,
                'subtotal' => $totalAmount,
            ],
        ]);
    }
}


