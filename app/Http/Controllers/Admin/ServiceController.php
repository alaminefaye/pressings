<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::withCount('prices')->get();
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:services,name',
            'description' => 'nullable|string',
            'duration_hours' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        Service::create($request->all());

        return redirect()->route('admin.services.index')->with('success', 'Service créé avec succès');
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:services,name,' . $id,
            'description' => 'nullable|string',
            'duration_hours' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $service->update($request->all());

        return redirect()->route('admin.services.index')->with('success', 'Service mis à jour avec succès');
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Service supprimé avec succès');
    }
}

