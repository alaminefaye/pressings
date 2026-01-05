<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Price;
use App\Models\Service;
use App\Models\ClothingType;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    public function index()
    {
        $services = Service::active()->get();
        $clothingTypes = ClothingType::active()->get();
        
        $prices = Price::with(['service', 'clothingType'])
            ->get()
            ->groupBy('service_id');

        return view('admin.prices.index', compact('prices', 'services', 'clothingTypes'));
    }

    public function update(Request $request)
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

        return redirect()->route('admin.prices.index')->with('success', 'Prix mis à jour avec succès');
    }
}

