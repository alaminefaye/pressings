<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Statistics
        $stats = [
            'orders' => [
                'today' => Order::whereDate('created_at', today())->count(),
                'pending' => Order::where('status', 'pending')->count(),
                'in_progress' => Order::whereIn('status', ['washing', 'ironing'])->count(),
                'ready' => Order::where('status', 'ready')->count(),
            ],
            'revenue' => [
                'today' => Order::whereDate('created_at', today())->sum('total'),
                'month' => Order::whereMonth('created_at', now()->month)->sum('total'),
                'paid' => Payment::where('status', 'completed')->sum('amount'),
            ],
            'customers' => [
                'total' => User::clients()->count(),
                'new_today' => User::clients()->whereDate('created_at', today())->count(),
            ],
            'deliveries' => [
                'pending' => Delivery::where('status', 'pending')->count(),
                'in_progress' => Delivery::where('status', 'in_progress')->count(),
            ],
        ];

        // Recent orders
        $recentOrders = Order::with(['client', 'items'])
            ->latest()
            ->limit(10)
            ->get();

        // Orders by status (for chart)
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Revenue last 7 days
        $revenueChart = Order::selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('dashboard', compact('stats', 'recentOrders', 'ordersByStatus', 'revenueChart'));
    }
}

