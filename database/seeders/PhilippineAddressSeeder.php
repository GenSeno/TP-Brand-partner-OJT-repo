<?php

namespace Database\Seeders;

use App\Models\Barangay;
use App\Models\City;
use App\Models\Province;
use App\Models\Region;

class PhilippineAddressSeeder extends AbstractSeeder
{
    /**
     * Run the database seeds.
     *
     * Data source: https://psgc.cloud (Philippine Standard Geographic Code API)
     */
    public function run(): void
    {
        $this->command->info('Importing Philippine Address Data');

        try {
            // First, seed with basic data to get the system working
            $this->seedBasicData();

            $this->command->info('Philippine Address Data imported successfully');

        } catch (\Exception $e) {
            $this->command->error('Error importing Philippine Address Data: '.$e->getMessage());
        }
    }

    private function seedBasicData(): void
    {
        // Clear existing data in correct order due to foreign keys
        Barangay::query()->delete();
        City::query()->delete();
        Province::query()->delete();
        Region::query()->delete();

        // Seed regions
        $regions = [
            ['psgc_code' => '0100000000', 'region_name' => 'Region I (Ilocos Region)', 'region_code' => '01'],
            ['psgc_code' => '0200000000', 'region_name' => 'Region II (Cagayan Valley)', 'region_code' => '02'],
            ['psgc_code' => '0300000000', 'region_name' => 'Region III (Central Luzon)', 'region_code' => '03'],
            ['psgc_code' => '0400000000', 'region_name' => 'Region IV-A (CALABARZON)', 'region_code' => '04'],
            ['psgc_code' => '1700000000', 'region_name' => 'MIMAROPA Region', 'region_code' => '17'],
            ['psgc_code' => '0500000000', 'region_name' => 'Region V (Bicol Region)', 'region_code' => '05'],
            ['psgc_code' => '0600000000', 'region_name' => 'Region VI (Western Visayas)', 'region_code' => '06'],
            ['psgc_code' => '0700000000', 'region_name' => 'Region VII (Central Visayas)', 'region_code' => '07'],
            ['psgc_code' => '0800000000', 'region_name' => 'Region VIII (Eastern Visayas)', 'region_code' => '08'],
            ['psgc_code' => '0900000000', 'region_name' => 'Region IX (Zamboanga Peninsula)', 'region_code' => '09'],
            ['psgc_code' => '1000000000', 'region_name' => 'Region X (Northern Mindanao)', 'region_code' => '10'],
            ['psgc_code' => '1100000000', 'region_name' => 'Region XI (Davao Region)', 'region_code' => '11'],
            ['psgc_code' => '1200000000', 'region_name' => 'Region XII (SOCCSKSARGEN)', 'region_code' => '12'],
            ['psgc_code' => '1300000000', 'region_name' => 'National Capital Region (NCR)', 'region_code' => '13'],
            ['psgc_code' => '1400000000', 'region_name' => 'Cordillera Administrative Region (CAR)', 'region_code' => '14'],
            ['psgc_code' => '1600000000', 'region_name' => 'Region XIII (Caraga)', 'region_code' => '16'],
            ['psgc_code' => '1900000000', 'region_name' => 'Bangsamoro Autonomous Region In Muslim Mindanao (BARMM)', 'region_code' => '19'],
        ];

        $regionMap = [];
        foreach ($regions as $index => $region) {
            $regionModel = Region::create($region);
            $regionMap[$index + 1] = $regionModel->id; // Map array index to DB ID
        }

        // Seed major provinces for demo
        $provinces = [
            ['psgc_code' => '0102800000', 'province_name' => 'Ilocos Norte', 'province_code' => '0280', 'region_id' => $regionMap[1]],
            ['psgc_code' => '0102900000', 'province_name' => 'Ilocos Sur', 'province_code' => '0290', 'region_id' => $regionMap[1]],
            ['psgc_code' => '0103300000', 'province_name' => 'La Union', 'province_code' => '0330', 'region_id' => $regionMap[1]],
            ['psgc_code' => '0105500000', 'province_name' => 'Pangasinan', 'province_code' => '0550', 'region_id' => $regionMap[1]],

            ['psgc_code' => '0202100000', 'province_name' => 'Batanes', 'province_code' => '0210', 'region_id' => $regionMap[2]],
            ['psgc_code' => '0202200000', 'province_name' => 'Cagayan', 'province_code' => '0220', 'region_id' => $regionMap[2]],
            ['psgc_code' => '0202300000', 'province_name' => 'Isabela', 'province_code' => '0230', 'region_id' => $regionMap[2]],
            ['psgc_code' => '0202400000', 'province_name' => 'Nueva Vizcaya', 'province_code' => '0240', 'region_id' => $regionMap[2]],
            ['psgc_code' => '0202500000', 'province_name' => 'Quirino', 'province_code' => '0250', 'region_id' => $regionMap[2]],

            ['psgc_code' => '0306900000', 'province_name' => 'Bataan', 'province_code' => '0690', 'region_id' => $regionMap[3]],
            ['psgc_code' => '0307100000', 'province_name' => 'Bulacan', 'province_code' => '0710', 'region_id' => $regionMap[3]],
            ['psgc_code' => '0307200000', 'province_name' => 'Nueva Ecija', 'province_code' => '0720', 'region_id' => $regionMap[3]],
            ['psgc_code' => '0307300000', 'province_name' => 'Pampanga', 'province_code' => '0730', 'region_id' => $regionMap[3]],
            ['psgc_code' => '0307400000', 'province_name' => 'Tarlac', 'province_code' => '0740', 'region_id' => $regionMap[3]],
            ['psgc_code' => '0307500000', 'province_name' => 'Zambales', 'province_code' => '0750', 'region_id' => $regionMap[3]],
            ['psgc_code' => '0371000000', 'province_name' => 'Aurora', 'province_code' => '7100', 'region_id' => $regionMap[3]],
        ];

        $provinceMap = [];
        foreach ($provinces as $index => $province) {
            $provinceModel = Province::create($province);
            $provinceMap[$index] = $provinceModel->id; // Map array index to DB ID
        }

        // First create a dummy NCR province since cities need a province_id
        $ncrProvince = Province::create([
            'psgc_code' => '1300000001',
            'province_name' => 'National Capital Region',
            'province_code' => '0001',
            'region_id' => $regionMap[13],
        ]);

        // Seed major cities for demo
        $cities = [
            // NCR Cities
            ['psgc_code' => '1375060000', 'city_name' => 'Manila', 'city_code' => '75060', 'city_type' => 'city', 'province_id' => $ncrProvince->id, 'region_id' => $regionMap[13]],
            ['psgc_code' => '1375070000', 'city_name' => 'Quezon City', 'city_code' => '75070', 'city_type' => 'city', 'province_id' => $ncrProvince->id, 'region_id' => $regionMap[13]],
            ['psgc_code' => '1375010000', 'city_name' => 'Caloocan', 'city_code' => '75010', 'city_type' => 'city', 'province_id' => $ncrProvince->id, 'region_id' => $regionMap[13]],
            ['psgc_code' => '1375020000', 'city_name' => 'Las Piñas', 'city_code' => '75020', 'city_type' => 'city', 'province_id' => $ncrProvince->id, 'region_id' => $regionMap[13]],
            ['psgc_code' => '1375030000', 'city_name' => 'Makati', 'city_code' => '75030', 'city_type' => 'city', 'province_id' => $ncrProvince->id, 'region_id' => $regionMap[13]],
            ['psgc_code' => '1375040000', 'city_name' => 'Malabon', 'city_code' => '75040', 'city_type' => 'city', 'province_id' => $ncrProvince->id, 'region_id' => $regionMap[13]],
            ['psgc_code' => '1375050000', 'city_name' => 'Mandaluyong', 'city_code' => '75050', 'city_type' => 'city', 'province_id' => $ncrProvince->id, 'region_id' => $regionMap[13]],
            ['psgc_code' => '1375080000', 'city_name' => 'Marikina', 'city_code' => '75080', 'city_type' => 'city', 'province_id' => $ncrProvince->id, 'region_id' => $regionMap[13]],
            ['psgc_code' => '1375090000', 'city_name' => 'Muntinlupa', 'city_code' => '75090', 'city_type' => 'city', 'province_id' => $ncrProvince->id, 'region_id' => $regionMap[13]],
            ['psgc_code' => '1375100000', 'city_name' => 'Navotas', 'city_code' => '75100', 'city_type' => 'city', 'province_id' => $ncrProvince->id, 'region_id' => $regionMap[13]],
            ['psgc_code' => '1375110000', 'city_name' => 'Parañaque', 'city_code' => '75110', 'city_type' => 'city', 'province_id' => $ncrProvince->id, 'region_id' => $regionMap[13]],
            ['psgc_code' => '1375120000', 'city_name' => 'Pasay', 'city_code' => '75120', 'city_type' => 'city', 'province_id' => $ncrProvince->id, 'region_id' => $regionMap[13]],
            ['psgc_code' => '1375130000', 'city_name' => 'Pasig', 'city_code' => '75130', 'city_type' => 'city', 'province_id' => $ncrProvince->id, 'region_id' => $regionMap[13]],
            ['psgc_code' => '1375140000', 'city_name' => 'San Juan', 'city_code' => '75140', 'city_type' => 'city', 'province_id' => $ncrProvince->id, 'region_id' => $regionMap[13]],
            ['psgc_code' => '1375150000', 'city_name' => 'Taguig', 'city_code' => '75150', 'city_type' => 'city', 'province_id' => $ncrProvince->id, 'region_id' => $regionMap[13]],
            ['psgc_code' => '1375160000', 'city_name' => 'Valenzuela', 'city_code' => '75160', 'city_type' => 'city', 'province_id' => $ncrProvince->id, 'region_id' => $regionMap[13]],

            // Major cities in other regions
            ['psgc_code' => '0349060000', 'city_name' => 'Angeles', 'city_code' => '49060', 'city_type' => 'city', 'province_id' => $provinceMap[0], 'region_id' => $regionMap[3]],
            ['psgc_code' => '0349150000', 'city_name' => 'San Fernando', 'city_code' => '49150', 'city_type' => 'city', 'province_id' => $provinceMap[0], 'region_id' => $regionMap[3]],
        ];

        $cityMap = [];
        foreach ($cities as $index => $city) {
            $cityModel = City::create($city);
            $cityMap[$index] = $cityModel->id; // Map array index to DB ID
        }

        // Seed sample barangays for demo
        $barangays = [
            ['psgc_code' => '13750604001', 'barangay_name' => 'Barangay 1', 'city_id' => $cityMap[0], 'province_id' => $ncrProvince->id, 'region_id' => $regionMap[13]],
            ['psgc_code' => '13750604002', 'barangay_name' => 'Barangay 2', 'city_id' => $cityMap[0], 'province_id' => $ncrProvince->id, 'region_id' => $regionMap[13]],
            ['psgc_code' => '13750604003', 'barangay_name' => 'Barangay 3', 'city_id' => $cityMap[0], 'province_id' => $ncrProvince->id, 'region_id' => $regionMap[13]],
            ['psgc_code' => '13750604004', 'barangay_name' => 'Barangay 4', 'city_id' => $cityMap[0], 'province_id' => $ncrProvince->id, 'region_id' => $regionMap[13]],
            ['psgc_code' => '13750604005', 'barangay_name' => 'Barangay 5', 'city_id' => $cityMap[0], 'province_id' => $ncrProvince->id, 'region_id' => $regionMap[13]],

            ['psgc_code' => '13750701001', 'barangay_name' => 'Barangay 1', 'city_id' => $cityMap[1], 'province_id' => $ncrProvince->id, 'region_id' => $regionMap[13]],
            ['psgc_code' => '13750701002', 'barangay_name' => 'Barangay 2', 'city_id' => $cityMap[1], 'province_id' => $ncrProvince->id, 'region_id' => $regionMap[13]],
            ['psgc_code' => '13750701003', 'barangay_name' => 'Barangay 3', 'city_id' => $cityMap[1], 'province_id' => $ncrProvince->id, 'region_id' => $regionMap[13]],
        ];

        foreach ($barangays as $barangay) {
            Barangay::create($barangay);
        }
    }
}
