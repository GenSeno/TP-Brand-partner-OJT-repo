<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Support\Facades\Http;

class CountryStateSeeder extends AbstractSeeder
{
    /**
     * Run the database seeds.
     *
     * Data source: https://github.com/dr5hn/countries-states-cities-database
     * License: Open Database License (ODbL)
     */
    public function run(): void
    {
        $this->command->info('Importing Countries and States');

        /**
         * Here we are using Http over Https due to some environments not having
         * the latest CA Authorities installed, causing an SSL exception to be thrown.
         */
        $response = Http::timeout(60)->get('http://data.lunarphp.io/countries+states.json');

        if (!$response->successful()) {
            $this->command->error('Failed to fetch data from remote source.');
            return;
        }

        $countries = collect($response->object());
        $this->command->info("Processing {$countries->count()} countries...");

        $countries->each(function ($country) {
            $countryModel = Country::firstOrCreate(
                [
                    'iso3' => $country->iso3
                ],
                [
                    'name' => $country->name,
                    'iso2' => $country->iso2,
                    'phonecode' => $country->phone_code,
                    'capital' => $country->capital,
                    'currency' => $country->currency,
                    'native' => $country->native,
                    'emoji' => $country->emoji,
                    'emojiU' => $country->emojiU,
                ]
            );

            // Delete existing states for this country to avoid duplicates
            $countryModel->states()->delete();

            // Transform states data correctly
            if (!empty($country->states)) {
                $states = collect($country->states)->map(function ($state) {
                    return [
                        'name' => $state->name,
                        'code' => $state->state_code,
                    ];
                });

                $countryModel->states()->createMany($states->toArray());
            }
        });

        $this->command->info('Countries and States imported successfully');
    }
}
