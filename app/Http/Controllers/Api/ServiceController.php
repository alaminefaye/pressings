<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of services
     */
    public function index(Request $request)
    {
        $query = Service::query();

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

        $services = $query->with('prices.clothingType')->get();

        return response()->json([
            'success' => true,
            'data' => $services,
        ]);
    }

    /**
     * Store a newly created service
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:services,name',
            'description' => 'nullable|string',
            'duration_hours' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $service = Service::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Service créé avec succès',
            'data' => $service,
        ], 201);
    }

    /**
     * Display the specified service
     */
    public function show($id)
    {
        $service = Service::with(['prices.clothingType'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $service,
        ]);
    }

    /**
     * Update the specified service
     */
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255|unique:services,name,' . $id,
            'description' => 'nullable|string',
            'duration_hours' => 'sometimes|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $service->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Service mis à jour avec succès',
            'data' => $service,
        ]);
    }

    /**
     * Remove the specified service
     */
    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service supprimé avec succès',
        ]);
    }

    /**
     * Get prices for a specific service
     */
    public function prices($id)
    {
        $service = Service::with(['prices.clothingType'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'service' => $service,
                'prices' => $service->prices,
            ],
        ]);
    }
}


