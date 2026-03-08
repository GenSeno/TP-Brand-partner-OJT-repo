<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Province;
use App\Models\Region;
use Illuminate\Support\Facades\Http;

class PhilippineGeoSeeder extends AbstractSeeder
{
    private const PSGC_API = 'https://psgc.gitlab.io/api';

    /**
     * Run the database seeds.
     *
     * Data source: https://psgc.gitlab.io/api
     * License: Philippine Standard Geographic Code (PSGC) — Philippine Statistics Authority (PSA)
     */
    public function run(): void
    {
        $this->command->info('Importing Philippine Regions, Provinces, and Cities...');

        // 1. Regions
        $this->command->info('Fetching regions...');
        $regionsData = $this->fetch('/regions/');
        if ($regionsData === null) return;

        foreach ($regionsData->chunk(50) as $chunk) {
            Region::upsert(
                $chunk->map(fn ($r) => [
                    'psgc_code'   => $r['code'],
                    'region_name' => $r['name'],
                    'region_code' => $r['regionName'] ?? $r['code'],
                ])->toArray(),
                ['psgc_code'],
                ['region_name', 'region_code']
            );
        }

        $regionMap = Region::pluck('id', 'psgc_code')->toArray();
        $this->command->info("  ✓ {$regionsData->count()} regions imported.");

        // 2. Provinces
        $this->command->info('Fetching provinces...');
        $provincesData = $this->fetch('/provinces/');
        if ($provincesData === null) return;

        $provinceRows = $provincesData
            ->filter(fn ($p) => isset($regionMap[$p['regionCode']]))
            ->map(fn ($p) => [
                'psgc_code'     => $p['code'],
                'province_name' => $p['name'],
                'province_code' => $p['code'],
                'region_id'     => $regionMap[$p['regionCode']],
            ])
            ->values();

        foreach ($provinceRows->chunk(100) as $chunk) {
            Province::upsert(
                $chunk->toArray(),
                ['psgc_code'],
                ['province_name', 'province_code', 'region_id']
            );
        }

        // 3. NCR Districts — treated as provinces so NCR cities can satisfy the province_id FK
        $this->command->info('Fetching NCR districts...');
        $districtsData = $this->fetch('/districts/');

        if ($districtsData !== null) {
            $districtRows = $districtsData
                ->filter(fn ($d) => isset($regionMap[$d['regionCode']]))
                ->map(fn ($d) => [
                    'psgc_code'     => $d['code'],
                    'province_name' => $d['name'],
                    'province_code' => $d['code'],
                    'region_id'     => $regionMap[$d['regionCode']],
                ])
                ->values();

            foreach ($districtRows->chunk(100) as $chunk) {
                Province::upsert(
                    $chunk->toArray(),
                    ['psgc_code'],
                    ['province_name', 'province_code', 'region_id']
                );
            }
        }

        $provinceMap = Province::pluck('id', 'psgc_code')->toArray();
        $this->command->info('  ✓ ' . count($provinceMap) . ' provinces/districts imported.');

        // 4. Cities & Municipalities
        $this->command->info('Fetching cities and municipalities (this may take a moment)...');
        $citiesData = $this->fetch('/cities-municipalities/');
        if ($citiesData === null) return;

        $cityRows = $citiesData
            ->map(function ($c) use ($regionMap, $provinceMap) {
                // NCR cities have provinceCode=false and use districtCode instead
                $provinceCode = is_string($c['provinceCode'] ?? null) ? $c['provinceCode'] : null;
                $districtCode = is_string($c['districtCode'] ?? null) ? $c['districtCode'] : null;
                $lookupCode   = $provinceCode ?? $districtCode;

                $provinceId = $lookupCode ? ($provinceMap[$lookupCode] ?? null) : null;
                $regionId   = isset($c['regionCode']) ? ($regionMap[$c['regionCode']] ?? null) : null;

                if (!$provinceId || !$regionId) {
                    return null;
                }

                return [
                    'psgc_code'   => $c['code'],
                    'city_name'   => $c['name'],
                    'city_code'   => $c['code'],
                    'city_type'   => ($c['isCity'] ?? false) ? 'city' : 'municipality',
                    'province_id' => $provinceId,
                    'region_id'   => $regionId,
                ];
            })
            ->filter()
            ->values();

        foreach ($cityRows->chunk(200) as $chunk) {
            City::upsert(
                $chunk->toArray(),
                ['psgc_code'],
                ['city_name', 'city_code', 'city_type', 'province_id', 'region_id']
            );
        }

        $this->command->info("  ✓ {$cityRows->count()} cities and municipalities imported.");
        $this->command->info('Philippine geography data imported successfully.');
    }

    private function fetch(string $endpoint): ?\Illuminate\Support\Collection
    {
        $response = Http::timeout(60)->get(self::PSGC_API . $endpoint);

        if (!$response->successful()) {
            $this->command->error('Failed to fetch data from: ' . self::PSGC_API . $endpoint);
            return null;
        }

        return collect($response->json());
    }
}
