<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AccessControl extends Controller
{
    public function index()
    {
        return Inertia::render('admin/staff/access-control/index', [
            'roles' => fn() => Role::with('permissions')
                ->where('guard_name', 'staff')
                ->where('name', '!=', 'Administrator')
                ->get()
                ->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'permissions' => $role->permissions->pluck('name'),
                        'is_used' => $role->users()->exists(),
                    ];
                }),
            'permissions' => fn() => Permission::where('guard_name', 'staff')
                ->get()
                ->map(function ($permission) {
                    return [
                        'name' => $permission->name,
                        'title' => __('permissions.' . $permission->name . '.title'),
                        'description' => __('permissions.' . $permission->name . '.description'),
                        'group' => explode(':', $permission->name)[0],
                    ];
                })->groupBy('group'),
        ]);
    }

    public function togglePermission(Request $request)
    {
        $request->validate([
            'group' => ['required', 'array'],
            'group.*.role_id' => ['required', 'integer', 'exists:roles,id'],
            'group.*.group' => ['required', 'string', 'exists:permissions,name'],
            'group.*.permission' => ['required', 'string', 'exists:permissions,name'],
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->input('group', []) as $item) {

                $role = Role::findById($item['role_id'], 'staff');

                if ($item['group'] === $item['permission']) {
                    if ($role->hasPermissionTo($item['permission'], 'staff')) {
                        Permission::where('name', 'like', $item['group'] . ':%')
                            ->get()
                            ->each(function ($permission) use ($role) {
                                $role->revokePermissionTo($permission->name);
                            });
                        $role->revokePermissionTo($item['permission']);
                    } else {
                        $role->givePermissionTo($item['permission']);
                    }
                } else {
                    if ($role->hasPermissionTo($item['permission'], 'staff')) {
                        $role->revokePermissionTo($item['permission']);
                    } elseif ($role->hasPermissionTo($item['group'], 'staff')) {
                        $role->givePermissionTo($item['permission']);
                    }
                }
            }
        });

        return response()->json([
            'message' => 'Permissions updated successfully.',
        ]);
    }
}
