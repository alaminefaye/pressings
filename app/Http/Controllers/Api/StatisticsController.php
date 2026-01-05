<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    /**
     * Get dashboard overview statistics
     */
    public function overview(Request $request)
    {
        $period = $request->get('period', 'today'); // today, week, month, year

        $dateFilter = $this->getDateFilter($period);

        $stats = [
            'orders' => [
                'total' => Order::when($dateFilter, function ($q) use ($dateFilter) {
                    $q->whereBetween('created_at', $dateFilter);
                })->count(),
                'pending' => Order::where('status', 'pending')
                    ->when($dateFilter, function ($q) use ($dateFilter) {
                        $q->whereBetween('created_at', $dateFilter);
                    })->count(),
                'in_progress' => Order::whereIn('status', ['washing', 'ironing'])
                    ->when($dateFilter, function ($q) use ($dateFilter) {
                        $q->whereBetween('created_at', $dateFilter);
                    })->count(),
                'ready' => Order::where('status', 'ready')
                    ->when($dateFilter, function ($q) use ($dateFilter) {
                        $q->whereBetween('created_at', $dateFilter);
                    })->count(),
                'delivered' => Order::where('status', 'delivered')
                    ->when($dateFilter, function ($q) use ($dateFilter) {
                        $q->whereBetween('created_at', $dateFilter);
                    })->count(),
            ],
            'revenue' => [
                'total' => Order::when($dateFilter, function ($q) use ($dateFilter) {
                    $q->whereBetween('created_at', $dateFilter);
                })->sum('total'),
                'paid' => Payment::where('status', 'completed')
                    ->when($dateFilter, function ($q) use ($dateFilter) {
                        $q->whereBetween('paid_at', $dateFilter);
                    })->sum('amount'),
                'pending' => Payment::where('status', 'pending')
                    ->when($dateFilter, function ($q) use ($dateFilter) {
                        $q->whereBetween('created_at', $dateFilter);
                    })->sum('amount'),
            ],
            'customers' => [
                'total' => User::clients()->count(),
                'new' => User::clients()
                    ->when($dateFilter, function ($q) use ($dateFilter) {
                        $q->whereBetween('created_at', $dateFilter);
                    })->count(),
                'active' => User::clients()
                    ->whereHas('orders', function ($q) use ($dateFilter) {
                        if ($dateFilter) {
                            $q->whereBetween('created_at', $dateFilter);
                        }
                    })->count(),
            ],
            'deliveries' => [
                'pending' => Delivery::where('status', 'pending')->count(),
                'in_progress' => Delivery::where('status', 'in_progress')->count(),
                'completed' => Delivery::where('status', 'completed')
                    ->when($dateFilter, function ($q) use ($dateFilter) {
                        $q->whereBetween('completed_at', $dateFilter);
                    })->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'period' => $period,
        ]);
    }

    /**
     * Get revenue statistics
     */
    public function revenue(Request $request)
    {
        $period = $request->get('period', 'month'); // week, month, year
        $groupBy = $this->getGroupByFormat($period);

        $revenue = Order::selectRaw("
                DATE_FORMAT(created_at, '{$groupBy}') as date,
                COUNT(*) as orders_count,
                SUM(total) as total_revenue,
                SUM(subtotal) as subtotal,
                SUM(discount) as total_discount,
                SUM(delivery_fee) as total_delivery_fees
            ")
            ->whereBetween('created_at', $this->getDateFilter($period))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $payments = Payment::selectRaw("
                DATE_FORMAT(paid_at, '{$groupBy}') as date,
                payment_method,
                COUNT(*) as count,
                SUM(amount) as amount
            ")
            ->where('status', 'completed')
            ->whereBetween('paid_at', $this->getDateFilter($period))
            ->groupBy('date', 'payment_method')
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        return response()->json([
            'success' => true,
            'data' => [
                'revenue' => $revenue,
                'payments_by_method' => $payments,
            ],
            'period' => $period,
        ]);
    }

    /**
     * Get orders statistics
     */
    public function orders(Request $request)
    {
        $period = $request->get('period', 'month');
        $groupBy = $this->getGroupByFormat($period);

        // Orders by status over time
        $ordersByStatus = Order::selectRaw("
                DATE_FORMAT(created_at, '{$groupBy}') as date,
                status,
                COUNT(*) as count
            ")
            ->whereBetween('created_at', $this->getDateFilter($period))
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        // Average order value
        $avgOrderValue = Order::whereBetween('created_at', $this->getDateFilter($period))
            ->avg('total');

        // Most popular services
        $popularServices = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('services', 'order_items.service_id', '=', 'services.id')
            ->selectRaw('
                services.name,
                services.id,
                COUNT(*) as orders_count,
                SUM(order_items.quantity) as total_quantity,
                SUM(order_items.subtotal) as total_revenue
            ')
            ->whereBetween('orders.created_at', $this->getDateFilter($period))
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('orders_count')
            ->limit(10)
            ->get();

        // Most popular clothing types
        $popularClothingTypes = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('clothing_types', 'order_items.clothing_type_id', '=', 'clothing_types.id')
            ->selectRaw('
                clothing_types.name,
                clothing_types.id,
                SUM(order_items.quantity) as total_quantity,
                COUNT(DISTINCT orders.id) as orders_count
            ')
            ->whereBetween('orders.created_at', $this->getDateFilter($period))
            ->groupBy('clothing_types.id', 'clothing_types.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'orders_by_status' => $ordersByStatus,
                'average_order_value' => round($avgOrderValue, 2),
                'popular_services' => $popularServices,
                'popular_clothing_types' => $popularClothingTypes,
            ],
            'period' => $period,
        ]);
    }

    /**
     * Get customer statistics
     */
    public function customers(Request $request)
    {
        $period = $request->get('period', 'month');
        $dateFilter = $this->getDateFilter($period);

        // New customers over time
        $groupBy = $this->getGroupByFormat($period);
        $newCustomers = User::clients()
            ->selectRaw("DATE_FORMAT(created_at, '{$groupBy}') as date, COUNT(*) as count")
            ->whereBetween('created_at', $dateFilter)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top customers by order count
        $topCustomersByOrders = User::clients()
            ->withCount(['orders' => function ($q) use ($dateFilter) {
                $q->whereBetween('created_at', $dateFilter);
            }])
            ->having('orders_count', '>', 0)
            ->orderByDesc('orders_count')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'phone' => $user->phone,
                    'orders_count' => $user->orders_count,
                ];
            });

        // Top customers by revenue
        $topCustomersByRevenue = User::clients()
            ->withSum(['orders' => function ($q) use ($dateFilter) {
                $q->whereBetween('created_at', $dateFilter);
            }], 'total')
            ->having('orders_sum_total', '>', 0)
            ->orderByDesc('orders_sum_total')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'phone' => $user->phone,
                    'total_spent' => $user->orders_sum_total,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'new_customers' => $newCustomers,
                'top_by_orders' => $topCustomersByOrders,
                'top_by_revenue' => $topCustomersByRevenue,
            ],
            'period' => $period,
        ]);
    }

    /**
     * Get delivery statistics
     */
    public function deliveries(Request $request)
    {
        $period = $request->get('period', 'month');
        $dateFilter = $this->getDateFilter($period);

        // Deliveries by status
        $deliveriesByStatus = Delivery::selectRaw('status, COUNT(*) as count')
            ->whereBetween('created_at', $dateFilter)
            ->groupBy('status')
            ->get();

        // Average delivery time
        $avgDeliveryTime = Delivery::where('status', 'completed')
            ->whereBetween('completed_at', $dateFilter)
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, started_at, completed_at)) as avg_minutes')
            ->value('avg_minutes');

        // Driver performance
        $driverPerformance = User::drivers()
            ->withCount(['deliveries' => function ($q) use ($dateFilter) {
                $q->where('status', 'completed')
                    ->whereBetween('completed_at', $dateFilter);
            }])
            ->having('deliveries_count', '>', 0)
            ->orderByDesc('deliveries_count')
            ->get()
            ->map(function ($driver) {
                return [
                    'id' => $driver->id,
                    'name' => $driver->full_name,
                    'phone' => $driver->phone,
                    'completed_deliveries' => $driver->deliveries_count,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'by_status' => $deliveriesByStatus,
                'average_delivery_time_minutes' => round($avgDeliveryTime, 2),
                'driver_performance' => $driverPerformance,
            ],
            'period' => $period,
        ]);
    }

    /**
     * Export statistics report
     */
    public function export(Request $request)
    {
        $period = $request->get('period', 'month');
        $format = $request->get('format', 'json'); // json, csv, pdf

        // Gather all statistics
        $stats = [
            'overview' => $this->overview($request)->getData()->data,
            'revenue' => $this->revenue($request)->getData()->data,
            'orders' => $this->orders($request)->getData()->data,
            'customers' => $this->customers($request)->getData()->data,
            'deliveries' => $this->deliveries($request)->getData()->data,
        ];

        // TODO: Implement CSV and PDF export
        if ($format === 'csv') {
            // Implement CSV export
            return response()->json([
                'success' => false,
                'message' => 'CSV export not yet implemented',
            ], 501);
        }

        if ($format === 'pdf') {
            // Implement PDF export using libraries like dompdf or snappy
            return response()->json([
                'success' => false,
                'message' => 'PDF export not yet implemented',
            ], 501);
        }

        return response()->json([
            'success' => true,
            'data' => $stats,
            'period' => $period,
            'generated_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Helper: Get date filter based on period
     */
    protected function getDateFilter($period)
    {
        return match($period) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'yesterday' => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'last_week' => [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'last_month' => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'year' => [now()->startOfYear(), now()->endOfYear()],
            'all' => null,
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }

    /**
     * Helper: Get grouping format based on period
     */
    protected function getGroupByFormat($period)
    {
        return match($period) {
            'today', 'yesterday' => '%Y-%m-%d %H:00',
            'week', 'last_week' => '%Y-%m-%d',
            'month', 'last_month' => '%Y-%m-%d',
            'year' => '%Y-%m',
            default => '%Y-%m-%d',
        };
    }
}


