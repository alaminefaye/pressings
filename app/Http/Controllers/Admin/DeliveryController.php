<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\User;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $query = Delivery::with(['order.client', 'driver']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        $deliveries = $query->latest()->paginate(20);
        $drivers = User::drivers()->active()->get();

        return view('admin.deliveries.index', compact('deliveries', 'drivers'));
    }

    public function show($id)
    {
        $delivery = Delivery::with([
            'order.client',
            'order.items',
            'driver',
            'order.pickupAddress',
            'order.deliveryAddress',
        ])->findOrFail($id);

        $drivers = User::drivers()->active()->get();

        return view('admin.deliveries.show', compact('delivery', 'drivers'));
    }

    public function assignDriver(Request $request, $id)
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id',
        ]);

        $delivery = Delivery::findOrFail($id);
        $delivery->driver_id = $request->driver_id;
        $delivery->status = 'assigned';
        $delivery->save();

        return redirect()->back()->with('success', 'Livreur assigné avec succès');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,assigned,picked_up,in_transit,delivered,failed',
        ]);

        $delivery = Delivery::findOrFail($id);
        $delivery->status = $request->status;
        
        if ($request->status === 'delivered') {
            $delivery->delivered_at = now();
            $delivery->order->status = 'delivered';
            $delivery->order->save();
        }
        
        $delivery->save();

        return redirect()->back()->with('success', 'Statut mis à jour avec succès');
    }
}


