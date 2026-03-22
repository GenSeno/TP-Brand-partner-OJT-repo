<?php

namespace Database\Seeders;

use App\Enums\UserStatus;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        Staff::createOrFirst([
            'email' => 'root@example.com',
        ], [
            'admin' => true,
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'status' => UserStatus::ACTIVE,
        ]);

        Staff::createOrFirst([
            'email' => 'pakaras@tpinklab.com',
        ], [
            'admin' => true,
            'first_name' => 'Pakaras',
            'last_name' => 'Admin',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'status' => UserStatus::ACTIVE,
        ]);

        // Create sample staff for local/testing environments
        if (app()->environment('local', 'testing')) {
            $staff = [
                [
                    'email' => 'admin@example.com',
                    'first_name' => 'Admin',
                    'last_name' => 'Staff',
                    'role' => 'Administrator',
                ],
                [
                    'email' => 'artist@example.com',
                    'first_name' => 'Artist',
                    'last_name' => 'Staff',
                    'role' => 'Artist',
                ],
                [
                    'email' => 'printer@example.com',
                    'first_name' => 'Printer',
                    'last_name' => 'Operator',
                    'role' => 'Printer Operator',
                ],
                [
                    'email' => 'qa@example.com',
                    'first_name' => 'QA',
                    'last_name' => 'Tester',
                    'role' => 'Quality Assurance',
                ],
                [
                    'email' => 'sewer@example.com',
                    'first_name' => 'Sewer',
                    'last_name' => 'Staff',
                    'role' => 'Sewer',
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
                    'password' => bcrypt('password'),
                    'status' => UserStatus::ACTIVE,
                ]);

                $staff->assignRole($data['role']);
            }
        }
    }
}
