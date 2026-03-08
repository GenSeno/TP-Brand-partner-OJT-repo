<?php

namespace App\Http\Controllers\Admin;

use App\Enums\JobOrderUrgency;
use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\JobOrder;
use App\Models\Order;
use App\Models\Product;
use App\States\JobOrderState\Completed;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $stats = [
            'orders' => $this->getOrderStats(),
            'customers' => $this->getCustomerStats(),
            'products' => $this->getProductStats(),
            'job_orders' => $this->getJobOrderStats(),
        ];

        $recentOrders = $this->getRecentOrders();
        $urgentJobOrders = $this->getUrgentJobOrders();

        return Inertia::render('admin/dashboard/index', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'urgentJobOrders' => $urgentJobOrders,
        ]);
    }

    protected function getOrderStats(): array
    {
        $totalOrders = Order::placed()->count();
        $pendingOrders = Order::placed()
            ->whereIn('status', [OrderStatus::PENDING, OrderStatus::UNPAID])
            ->count();
        $newOrdersToday = Order::whereDate('placed_at', now())->count();
        $newOrdersLast30Days = Order::placed()
            ->whereDate('placed_at', now()->subDays(30))
            ->count();

        $percentage = $newOrdersLast30Days > 0
            ? round(($newOrdersToday / $newOrdersLast30Days) * 100, 2)
            : 100;

        return [
            'total' => $totalOrders,
            'pending' => $pendingOrders,
            'new_today' => $newOrdersToday,
            'percentage' => $percentage,
        ];
    }

    protected function getCustomerStats(): array
    {
        $totalCustomers = Customer::enabled()->count();
        $newCustomersToday = Customer::enabled()->whereDate('created_at', now())->count();

        return [
            'total' => $totalCustomers,
            'new_today' => $newCustomersToday,
        ];
    }

    protected function getProductStats(): array
    {
        $totalProducts = Product::whereStatus(ProductStatus::PUBLISHED)->count();
        $newProductsToday = Product::whereStatus(ProductStatus::PUBLISHED)
            ->whereDate('created_at', now())
            ->count();

        return [
            'total' => $totalProducts,
            'new_today' => $newProductsToday,
        ];
    }

    protected function getJobOrderStats(): array
    {
        $totalJobOrders = JobOrder::whereNotIn('current_state', [Completed::$name])->count();
        $newJobOrdersToday = JobOrder::whereDate('created_at', now())->count();

        return [
            'total' => $totalJobOrders,
            'new_today' => $newJobOrdersToday,
        ];
    }

    protected function getUrgentJobOrders()
    {
        return JobOrder::with(['order.orderable'])
            ->whereIn('urgency_flag', [JobOrderUrgency::RUSH, JobOrderUrgency::PRIORITY])
            ->whereNotIn('current_state', [Completed::$name])
            ->urgentFirst()
            ->limit(10)
            ->get();
    }

    protected function getRecentOrders()
    {
        return Order::with(['orderable', 'jobOrder'])
            ->placed()
            ->latest('placed_at')
            ->limit(10)
            ->get();
    }

    protected function getSalesStats()
    {
        // Placeholder for future sales statistics implementation
        return [];
    }
}
