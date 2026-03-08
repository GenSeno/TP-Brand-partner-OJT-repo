<?php

namespace Database\Seeders;

use App\Constants\StaffPermission;
use App\States\JobOrderState\JobOrderState;
use DB;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            StaffPermission::values()->each(function ($permission) {
                Permission::createOrFirst([
                    'guard_name' => 'staff',
                    'name' => $permission,
                ]);
            });
        });
    }
}
