<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClothingType;
use Illuminate\Http\Request;

class ClothingTypeController extends Controller
{
    public function index()
    {
        $clothingTypes = ClothingType::withCount('prices')->get();
        return view('admin.clothing-types.index', compact('clothingTypes'));
    }

    public function create()
    {
        return view('admin.clothing-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:clothing_types,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['slug'] = \Str::slug($request->name);

        ClothingType::create($data);

        return redirect()->route('admin.clothing-types.index')->with('success', 'Type de vêtement créé avec succès');
    }

    public function edit($id)
    {
        $clothingType = ClothingType::findOrFail($id);
        return view('admin.clothing-types.edit', compact('clothingType'));
    }

    public function update(Request $request, $id)
    {
        $clothingType = ClothingType::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:clothing_types,name,' . $id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['slug'] = \Str::slug($request->name);

        $clothingType->update($data);

        return redirect()->route('admin.clothing-types.index')->with('success', 'Type de vêtement mis à jour avec succès');
    }

    public function destroy($id)
    {
        $clothingType = ClothingType::findOrFail($id);
        $clothingType->delete();

        return redirect()->route('admin.clothing-types.index')->with('success', 'Type de vêtement supprimé avec succès');
    }
}

