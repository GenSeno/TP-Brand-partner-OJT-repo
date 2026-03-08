<?php

namespace Database\Seeders;

use App\Models\Currency;
use DB;

class CurrencySeeder extends AbstractSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = $this->getSeedData('currencies');

        DB::transaction(function () use ($currencies) {
            foreach ($currencies as $currency) {
                Currency::firstOrCreate([
                    'code' => $currency->code,
                ], [
                    'name' => $currency->name,
                    'symbol' => $currency->symbol,
                    'exchange_rate' => $currency->exchange_rate,
                    'decimal_places' => $currency->decimal_places,
                    'default' => $currency->default,
                    'enabled' => true,
                ]);
            }
        });
    }
}
