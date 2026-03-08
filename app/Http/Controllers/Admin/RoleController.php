<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::modal('admin/staff/access-control/role/create')
            ->baseRoute('admin.access-control.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
        ]);

        $role = Role::create($request->only('name'));

        return response()->json([
            'role' => $role,
            'message' => __('crud.created', ['record' => 'Role'])
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        return Inertia::modal('admin/staff/access-control/role/edit', [
            'role' => $role,
        ])->baseRoute('admin.access-control.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($role->id)
            ],
        ]);

        $role->update($request->only('name'));

        return response()->json([
            'role' => $role,
            'message' => __('crud.updated', ['record' => 'Role'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if ($role->users()->exists()) {
            return response()->json([
                'message' => 'Cannot delete role that has assigned users.',
            ], 422);
        }

        $role->delete();

        return response()->json([
            'deleted' => [$role->id],
            'message' => __('crud.deleted', ['record' => 'Role']),
        ]);
    }
}
