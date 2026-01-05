<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        // Statistiques globales
        $stats = [
            'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_revenue' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('total'),
            'total_paid' => Payment::where('status', 'completed')
                ->whereBetween('paid_at', [$startDate, $endDate])
                ->sum('amount'),
            'new_customers' => User::clients()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
        ];

        // Commandes par statut
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->get();

        // Revenus par jour
        $revenueByDay = Order::selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top services
        $topServices = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('services', 'order_items.service_id', '=', 'services.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select('services.name', DB::raw('SUM(order_items.quantity) as total_quantity'), DB::raw('SUM(order_items.subtotal) as total_revenue'))
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // Top clients
        $topCustomers = User::clients()
            ->select('users.*', DB::raw('COUNT(orders.id) as order_count'), DB::raw('SUM(orders.total) as total_spent'))
            ->join('orders', 'users.id', '=', 'orders.client_id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('users.id')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        return view('admin.reports.index', compact(
            'stats',
            'ordersByStatus',
            'revenueByDay',
            'topServices',
            'topCustomers',
            'startDate',
            'endDate'
        ));
    }
}


