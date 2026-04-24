<?php

namespace App\Http\Middleware;

use App\Models\BrandPartnerProduct;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                'roles' => fn() => $request->user('staff')?->roles()->pluck('name') ?? [],
                'permissions' => fn() => $request->user('staff')?->getAllPermissions()->pluck('name') ?? [],
            ],
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error'   => fn() => $request->session()->get('error'),
                'info'    => fn() => $request->session()->get('info'),
                'warning' => fn() => $request->session()->get('warning'),
            ],
            'pendingProductsCount' => fn() => ($bp = Auth::guard('brand_partner')->user())
                ? BrandPartnerProduct::where('brand_partner_id', $bp->id)
                    ->where('approval_status', 'pending')
                    ->count()
                : 0,
            'cartCount' => function () use ($request) {
                if (Auth::check()) {
                    // Logged in: count from DB
                    return CartItem::where('user_id', Auth::id())->sum('quantity');
                }

                // Guest: count from session
                $slug = config('store.brand_partner_slug');
                $cart = $request->session()->get("bp_cart_{$slug}", []);
                return array_sum(array_column($cart, 'quantity'));
            },
        ];
    }
}