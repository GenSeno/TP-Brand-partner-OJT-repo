<?php

use App\Models\Address;
use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // Update phonecode to prepend '+' if not already present
        $countriesId = cache()->get('countries_with_plus_phonecode');
        if (empty($countriesId)) {
            $countriesId = Country::query()
                ->whereRaw("phonecode LIKE '+%'")
                ->pluck('id')
                ->toArray();

            cache()->forever('countries_with_plus_phonecode', $countriesId);

            Country::query()
                ->whereNotIn('id', $countriesId)
                ->update([
                    'phonecode' => DB::raw("CONCAT('+', phonecode)")
                ]);
        }

        // Set default address to 1 where addressable has no default billing address
        Address::isDefault()->update(['default' => false]);

        Address::billing()
            ->whereIn(
                'id',
                Address::billing()
                    ->distinct('id', 'addressable_type', 'addressable_id')
                    ->pluck('id')
                    ->toArray()
            )
            ->update(['default' => true]);

        // Update addresses with invalid city/province to a valid one from states table
        DB::table('addresses')
            ->whereNotIn(
                'addresses.city',
                DB::table('states')
                    ->select('name')
                    ->where('states.country_id', 'addresses.country_id')
            )
            ->orderBy('id')
            ->each(function ($address) {
                $state = State::where('country_id', $address->country_id)
                    ->inRandomOrder()
                    ->value('name');
                if ($state) {
                    DB::table('addresses')
                        ->where('id', $address->id)
                        ->update([
                            'city' => $state,
                            'province' => $state,
                        ]);
                }
            });

        DB::table('order_addresses')
            ->whereNotIn(
                'order_addresses.city',
                DB::table('states')
                    ->select('name')
                    ->where('states.country_id', 'order_addresses.country_id')
            )
            ->orderBy('id')
            ->each(function ($address) {
                $state = State::where('country_id', $address->country_id)
                    ->inRandomOrder()
                    ->value('name');
                if ($state) {
                    DB::table('order_addresses')
                        ->where('id', $address->id)
                        ->update([
                            'city' => $state,
                            'province' => $state,
                        ]);
                }
            });

        DB::table('quote_addresses')
            ->whereNotIn(
                'quote_addresses.city',
                DB::table('states')
                    ->select('name')
                    ->where('states.country_id', 'quote_addresses.country_id')
            )
            ->orderBy('id')
            ->each(function ($address) {
                $state = State::where('country_id', $address->country_id)
                    ->inRandomOrder()
                    ->value('name');
                if ($state) {
                    DB::table('quote_addresses')
                        ->where('id', $address->id)
                        ->update([
                            'city' => $state,
                            'province' => $state,
                        ]);
                }
            });
    }

    public function down()
    {
        // Remove '+' if present at the start
        $countriesId = cache()->get('countries_with_plus_phonecode');
        if (!empty($countriesId)) {
            Country::query()
                ->whereNotIn('id', $countriesId)
                ->update([
                    'phonecode' => DB::raw("TRIM(LEADING '+' FROM phonecode)")
                ]);

            cache()->forget('countries_with_plus_phonecode');
        }

        // No action for default address on rollback
    }
};
