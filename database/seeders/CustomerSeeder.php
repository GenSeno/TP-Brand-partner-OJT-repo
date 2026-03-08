<?php

namespace Database\Seeders;

use App\Enums\AddressType;
use App\Models\Address;
use App\Models\Customer;
use DB;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment('local', 'testing')) {
            DB::transaction(function () {
                $customers = Customer::factory()->count(50)->create();

                $customers->each(function (Customer $customer) {
                    $customer->addresses()->createMany(
                        Address::factory()
                            ->count(2)
                            ->sequence(
                                ['type' => AddressType::BILLING, 'default' => true],
                                ['type' => AddressType::SHIPPING, 'default' => false],
                            )
                            ->make()
                            ->toArray()
                    );

                });
            });
        }
    }
}
