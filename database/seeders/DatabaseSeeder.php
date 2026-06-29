<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            // CountriesSeeder::class, --- REPLACED ---
            CountryStateSeeder::class,
            PhilippineGeoSeeder::class,
            CurrencySeeder::class,
            ProductOptionSeeder::class,
            UnitMeasureSeeder::class,
            InventorySeeder::class,

            PermissionSeeder::class,
            RoleSeeder::class,
            StaffSeeder::class,
            BrandPartnerSeeder::class,
            BrandPartnerProductOptionSeeder::class,

            // Test Data Seeders
            CustomerSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
            JobOrderSeeder::class,

            // CMS Seeders
            PolicySeeder::class,
            FaqSeeder::class,
        ]);
    }
}
