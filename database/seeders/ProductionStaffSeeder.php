<?php

namespace Database\Seeders;

use App\Enums\UserStatus;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class ProductionStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create production staff accounts with proper roles
        $staff = [
            [
                'email' => 'artist@tpinklab.com',
                'first_name' => 'Artist',
                'last_name' => 'User',
                'role' => 'Artist',
            ],
            [
                'email' => 'printer@tpinklab.com',
                'first_name' => 'Printer',
                'last_name' => 'Operator',
                'role' => 'Printer Operator',
            ],
            [
                'email' => 'qa@tpinklab.com',
                'first_name' => 'Quality',
                'last_name' => 'Assurance',
                'role' => 'Quality Assurance',
            ],
        ];

        foreach ($staff as $data) {
            $staff = Staff::createOrFirst([
                'email' => $data['email'],
            ], [
                'admin' => false,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email_verified_at' => now(),
                'password' => bcrypt('tpinklab123'), // Default password for production
                'status' => UserStatus::ACTIVE,
            ]);

            $staff->assignRole($data['role']);
        }
    }
}
