<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StaffRequest;
use App\Models\Staff;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class StaffController extends Controller
{
    protected $defaultPerPage = 10;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $staff_list = QueryBuilder::for(Staff::class)
            ->where('admin', false)
            ->with('roles')
            ->allowedSorts(['first_name', 'email', 'created_at'])
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::scope('search'),
            ])
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        return Inertia::render('admin/staff/index', [
            'staffList' => $staff_list,
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
        return Inertia::modal('admin/staff/create', [
            'statuses' => fn() => UserStatus::getOptions(),
            'roles' => fn() => Role::where('guard_name', 'staff')->get(),
        ])->baseRoute('admin.staff.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StaffRequest $request)
    {
        $staff = DB::transaction(function () use ($request) {
            $staff = Staff::create($request->only([
                'first_name',
                'last_name',
                'email',
                'status'
            ]) + [
                'password' => bcrypt($request->input('password')),
            ]);

            $staff->markEmailAsVerified();

            $staff->assignRole($request->input('role'));

            if ($request->hasFile('avatar')) {
                $staff
                    ->addMediaFromRequest('avatar')
                    ->toMediaCollection('avatar');
            }

            return $staff;
        });

        return response()->json([
            'staff' => $staff,
            'message' => __('crud.created', ['record' => 'Staff'])
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Staff $staff)
    {
        return Inertia::modal('admin/staff/edit', [
            'staff' => $staff->load('roles'),
            'statuses' => fn() => UserStatus::getOptions(),
            'roles' => fn() => Role::where('guard_name', 'staff')->get(),
        ])->baseRoute('admin.staff.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StaffRequest $request, Staff $staff)
    {
        DB::transaction(function () use ($request, $staff) {
            $data = $request->only([
                'first_name',
                'last_name',
                'email',
                'status'
            ]);
            if ($request->filled('password')) {
                $data['password'] = bcrypt($request->input('password'));
            }
            $staff->update($data);

            $staff->syncRoles([$request->input('role')]);

            if ($request->hasFile('avatar')) {
                $staff
                    ->addMediaFromRequest('avatar')
                    ->toMediaCollection('avatar');
            } elseif ($request->input('avatar_removed')) {
                $staff
                    ->clearMediaCollection('avatar');
            }
        });

        return response()->json([
            'staff' => $staff->refresh(),
            'message' => __('crud.updated', ['record' => 'Staff'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Staff $staff)
    {
        $staff->delete();
        return response()->json([
            'deleted' => [$staff->id],
            'message' => __('crud.deleted', ['record' => 'Staff']),
        ]);
    }
}
