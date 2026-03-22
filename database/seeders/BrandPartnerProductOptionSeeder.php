<?php

namespace Database\Seeders;

use App\Models\BrandPartner;
use App\Models\BrandPartnerProductOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandPartnerProductOptionSeeder extends Seeder
{
    protected array $options = [
        [
            'name'     => 'Collection',
            'position' => 1,
            'values'   => [],
        ],
        [
            'name'     => 'Category',
            'position' => 2,
            'values'   => [],
        ],
        [
            'name'     => 'Sizes',
            'position' => 3,
            'values'   => [
                ['label' => 'XXS',    'value' => 'XXS'],
                ['label' => 'XS',     'value' => 'XS'],
                ['label' => 'S',      'value' => 'S'],
                ['label' => 'M',      'value' => 'M'],
                ['label' => 'L',      'value' => 'L'],
                ['label' => 'XL',     'value' => 'XL'],
                ['label' => 'XXL',    'value' => 'XXL'],
                ['label' => 'XXXL',   'value' => 'XXXL'],
                ['label' => '4XL',    'value' => '4XL'],
                ['label' => '5XL',    'value' => '5XL'],
                ['label' => 'Others', 'value' => 'OTHERS'],
                ['label' => 'Custom', 'value' => 'Custom'],
            ],
        ],
    ];

    public function run(): void
    {
        DB::transaction(function () {
            BrandPartner::each(function (BrandPartner $brandPartner) {
                foreach ($this->options as $i => $optionData) {
                    $option = BrandPartnerProductOption::firstOrCreate(
                        [
                            'brand_partner_id' => $brandPartner->id,
                            'name'             => $optionData['name'],
                        ],
                        [
                            'position' => $optionData['position'],
                        ]
                    );

                    foreach ($optionData['values'] as $j => $valueData) {
                        $option->values()->firstOrCreate(
                            ['label' => $valueData['label']],
                            ['value' => $valueData['value'], 'position' => $j + 1]
                        );
                    }
                }
            });
        });
    }
}
