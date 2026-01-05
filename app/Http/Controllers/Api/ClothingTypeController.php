<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClothingType;
use Illuminate\Http\Request;

class ClothingTypeController extends Controller
{
    /**
     * Display a listing of clothing types
     */
    public function index(Request $request)
    {
        $query = ClothingType::query();

        // Filter by active status
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        // Search
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $clothingTypes = $query->with('prices.service')->get();

        return response()->json([
            'success' => true,
            'data' => $clothingTypes,
        ]);
    }

    /**
     * Store a newly created clothing type
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:clothing_types,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $clothingType = ClothingType::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Type de vêtement créé avec succès',
            'data' => $clothingType,
        ], 201);
    }

    /**
     * Display the specified clothing type
     */
    public function show($id)
    {
        $clothingType = ClothingType::with(['prices.service'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $clothingType,
        ]);
    }

    /**
     * Update the specified clothing type
     */
    public function update(Request $request, $id)
    {
        $clothingType = ClothingType::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255|unique:clothing_types,name,' . $id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $clothingType->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Type de vêtement mis à jour avec succès',
            'data' => $clothingType,
        ]);
    }

    /**
     * Remove the specified clothing type
     */
    public function destroy($id)
    {
        $clothingType = ClothingType::findOrFail($id);
        $clothingType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Type de vêtement supprimé avec succès',
        ]);
    }

    /**
     * Get prices for a specific clothing type
     */
    public function prices($id)
    {
        $clothingType = ClothingType::with(['prices.service'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'clothing_type' => $clothingType,
                'prices' => $clothingType->prices,
            ],
        ]);
    }
}


