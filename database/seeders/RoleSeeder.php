<?php

namespace Database\Seeders;

use App\Constants\StaffPermission;
use DB;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'Administrator' => ['*'],

            'Artist' => [
                StaffPermission::JOB_ORDERS,
                StaffPermission::MANAGE_JO_NOTES,
                StaffPermission::MANAGE_JO_ARTIST_TASKS,
            ],

            'Printer Operator' => [
                StaffPermission::JOB_ORDERS,
                StaffPermission::MANAGE_JO_NOTES,
                StaffPermission::MANAGE_JO_PRINTING_TASKS,
            ],

            'Quality Assurance' => [
                StaffPermission::JOB_ORDERS,
                StaffPermission::MANAGE_JO_NOTES,
                StaffPermission::MANAGE_JO_HEAT_PRESS_TASKS,
                StaffPermission::MANAGE_JO_SEWING_TASKS,
                StaffPermission::MANAGE_JO_PACKING_TASKS,
                StaffPermission::MANAGE_JO_COMPLETED_TASKS,
            ],

            'Sewer' => [
                StaffPermission::JOB_ORDERS,
                StaffPermission::MANAGE_JO_NOTES,
                StaffPermission::MANAGE_JO_SEWING_TASKS,
            ],

            'Dispatching' => [
                StaffPermission::JOB_ORDERS,
                StaffPermission::MANAGE_JO_NOTES,
                StaffPermission::MANAGE_JO_DISPATCHING_TASKS,
            ],
        ];

        DB::transaction(function () use ($roles) {
            $AllPermissions = Permission::where('guard_name', 'staff')
                ->pluck('name')
                ->toArray();

            foreach ($roles as $role => $permissions) {
                $role = Role::firstOrCreate([
                    'guard_name' => 'staff',
                    'name' => $role,
                ]);

                if (in_array('*', $permissions)) {
                    $role->syncPermissions($AllPermissions);
                    continue;
                }
                $role->syncPermissions($permissions);
            }
        });
    }
}
