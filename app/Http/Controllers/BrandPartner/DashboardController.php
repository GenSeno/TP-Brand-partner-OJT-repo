<?php

namespace App\Http\Controllers\BrandPartner;

use App\Enums\BrandPartnerOrderStatus;
use App\Enums\BrandPartnerProductStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the brand partner dashboard.
     */
    public function index(Request $request)
    {
        $brandPartner = Auth::guard('brand_partner')->user();

        $stats = [
            'total_products' => $brandPartner->products()->count(),
            'published_products' => $brandPartner->products()->where('status', BrandPartnerProductStatus::PUBLISHED)->count(),
            'total_orders' => $brandPartner->orders()->count(),
            'pending_orders' => $brandPartner->orders()->where('status', BrandPartnerOrderStatus::PENDING)->count(),
            'total_revenue' => $brandPartner->orders()
                ->whereIn('status', [BrandPartnerOrderStatus::CONFIRMED, BrandPartnerOrderStatus::COMPLETED])
                ->sum('total'),
            'total_categories' => $brandPartner->categories()->count(),
            'total_events' => $brandPartner->events()->count(),
        ];

        $recentOrders = $brandPartner->orders()
            ->with('lines')
            ->latest()
            ->limit(5)
            ->get();

        return Inertia::render('dashboard/index', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }
}
