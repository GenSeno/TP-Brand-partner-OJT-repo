<?php
// database/seeders/CountriesSeeder.php

namespace Database\Seeders;

use App\Models\Country;

class CountriesSeeder extends AbstractSeeder
{
    public function run()
    {
        return $this->command->info('CountriesSeeder is deprecated. Please use CountryStateSeeder instead.');

        $countries = $this->getSeedData('countries');

        $countries->chunkWhile(function ($chunk) {
            Country::upsert((array) $chunk, ['iso3'], [
                'name',
                'iso2',
                'phonecode',
                'capital',
                'currency',
                'native',
                'emoji',
                'emojiU',
            ]);
        });
    }
}
