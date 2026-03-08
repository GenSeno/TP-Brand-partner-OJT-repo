<?php

namespace Database\Seeders;

use App\Models\UnitMeasure;
use DB;

class UnitMeasureSeeder extends AbstractSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unitMeasures = $this->getSeedData('unit-of-measure');

        DB::transaction(function () use ($unitMeasures) {
            foreach ($unitMeasures as $unitMeasure) {
                UnitMeasure::firstOrCreate([
                    'name' => $unitMeasure->name,
                    'code' => $unitMeasure->code,
                ], [
                    'type' => $unitMeasure->type,
                    'enabled' => $unitMeasure->enabled,
                ]);
            }
        });
    }
}
