<?php

namespace App\Http\Middleware;

use App\Models\BrandPartnerProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
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
                'error' => fn() => $request->session()->get('error'),
                'info' => fn() => $request->session()->get('info'),
                'warning' => fn() => $request->session()->get('warning'),
            ],
            'pendingProductsCount' => fn() => ($bp = Auth::guard('brand_partner')->user())
                ? BrandPartnerProduct::where('brand_partner_id', $bp->id)
                    ->where('approval_status', 'pending')
                    ->count()
                : 0,
        ];
    }
}
