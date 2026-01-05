<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::clients()->withCount('orders');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $customers = $query->latest()->paginate(20);

        return view('admin.customers.index', compact('customers'));
    }

    public function show($id)
    {
        $customer = User::clients()
            ->with(['orders.items', 'addresses', 'wallet', 'loyaltyPoints'])
            ->findOrFail($id);

        $recentOrders = $customer->orders()->latest()->limit(10)->get();

        return view('admin.customers.show', compact('customer', 'recentOrders'));
    }
}

