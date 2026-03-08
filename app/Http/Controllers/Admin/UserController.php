<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    protected $defaultPerPage = 10;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = QueryBuilder::for(User::class)
            ->allowedSorts(['name', 'email', 'created_at'])
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::scope('search'),
            ])
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        return Inertia::render('admin/users/index', [
            'users' => $users,
            'statuses' => fn() => UserStatus::getOptions(),
            'filter' => $request->input('filter', []) + [
                'default_per_page' => $this->defaultPerPage,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::modal('admin/users/create', [
            'statuses' => fn() => UserStatus::getOptions(),
        ])->baseRoute('admin.users.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $user = DB::transaction(function () use ($request) {
            $user = User::create($request->only([
                'name',
                'email',
                'status'
            ]) + [
                'password' => bcrypt($request->input('password')),
            ]);

            if ($request->boolean('verified')) {
                $user->markEmailAsVerified();
            }

            if ($request->hasFile('avatar')) {
                $user
                    ->addMediaFromRequest('avatar')
                    ->toMediaCollection('avatar');
            }

            return $user;
        });

        return response()->json([
            'user' => $user,
            'message' => __('crud.created', ['record' => 'User'])
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return Inertia::modal('admin/users/edit', [
            'user' => $user,
            'statuses' => fn() => UserStatus::getOptions(),
        ])->baseRoute('admin.users.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        DB::transaction(function () use ($request, $user) {
            $data = $request->only([
                'name',
                'email',
                'status'
            ]);
            if ($request->filled('password')) {
                $data['password'] = bcrypt($request->input('password'));
            }
            $user->update($data);

            if ($request->input('verified')) {
                $user->markEmailAsVerified();
            } else {
                $user->email_verified_at = null;
                $user->save();
            }

            if ($request->hasFile('avatar')) {
                $user
                    ->addMediaFromRequest('avatar')
                    ->toMediaCollection('avatar');
            } elseif ($request->input('avatar_removed')) {
                $user
                    ->clearMediaCollection('avatar');
            }
        });

        return response()->json([
            'user' => $user->refresh(),
            'message' => __('crud.updated', ['record' => 'User'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json([
            'deleted' => [$user->id],
            'message' => __('crud.deleted', ['record' => 'User']),
        ]);
    }
}
