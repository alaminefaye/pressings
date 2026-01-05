<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['client', 'employee', 'items.service', 'items.clothingType', 'payment']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%')
                ->orWhereHas('client', function ($q) use ($request) {
                    $q->where('first_name', 'like', '%' . $request->search . '%')
                      ->orWhere('last_name', 'like', '%' . $request->search . '%')
                      ->orWhere('phone', 'like', '%' . $request->search . '%');
                });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $orders = $query->latest()->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with([
            'client',
            'employee',
            'items.service',
            'items.clothingType',
            'payment',
            'delivery.driver',
            'pickupAddress',
            'deliveryAddress',
            'statusHistory.user',
        ])->findOrFail($id);

        $employees = User::employees()->active()->get();
        $drivers = User::drivers()->active()->get();

        return view('admin.orders.show', compact('order', 'employees', 'drivers'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,received,washing,ironing,ready,in_delivery,delivered,cancelled',
            'comment' => 'nullable|string',
        ]);

        $order = Order::findOrFail($id);
        $order->updateStatus($request->status, auth()->id(), $request->comment);

        return redirect()->back()->with('success', 'Statut mis à jour avec succès');
    }

    public function assignEmployee(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
        ]);

        $order = Order::findOrFail($id);
        $order->employee_id = $request->employee_id;
        $order->save();

        return redirect()->back()->with('success', 'Employé assigné avec succès');
    }
}

