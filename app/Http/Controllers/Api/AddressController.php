<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Display a listing of user's addresses
     */
    public function index(Request $request)
    {
        $addresses = $request->user()->addresses()->get();

        return response()->json([
            'success' => true,
            'data' => $addresses,
        ]);
    }

    /**
     * Store a newly created address
     */
    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'address_line' => 'required|string',
            'city' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset other default addresses
        if ($request->boolean('is_default')) {
            Address::where('user_id', $request->user()->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $address = $request->user()->addresses()->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Adresse créée avec succès',
            'data' => $address,
        ], 201);
    }

    /**
     * Display the specified address
     */
    public function show(Request $request, $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $address,
        ]);
    }

    /**
     * Update the specified address
     */
    public function update(Request $request, $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);

        $request->validate([
            'label' => 'sometimes|string|max:255',
            'address_line' => 'sometimes|string',
            'city' => 'sometimes|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset other default addresses
        if ($request->boolean('is_default')) {
            Address::where('user_id', $request->user()->id)
                ->where('id', '!=', $id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $address->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Adresse mise à jour avec succès',
            'data' => $address,
        ]);
    }

    /**
     * Remove the specified address
     */
    public function destroy(Request $request, $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);
        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Adresse supprimée avec succès',
        ]);
    }

    /**
     * Set address as default
     */
    public function setDefault(Request $request, $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);

        // Unset all other default addresses
        Address::where('user_id', $request->user()->id)
            ->where('id', '!=', $id)
            ->update(['is_default' => false]);

        $address->is_default = true;
        $address->save();

        return response()->json([
            'success' => true,
            'message' => 'Adresse définie comme adresse par défaut',
            'data' => $address,
        ]);
    }
}


