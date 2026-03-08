<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barangay;
use App\Models\City;
use App\Models\Country;
use App\Models\Province;
use App\Models\Region;
use App\Models\State;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function states(Request $request)
    {
        $country_id = $request->input('country_id');
        if (empty(trim($country_id))) {
            return [];
        }

        return State::where('country_id', $country_id)->get();
    }

    public function regions(Request $request)
    {
        return Region::orderBy('region_name')->get();
    }

    public function provinces(Request $request)
    {
        $query = Province::orderBy('province_name');

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->input('region_id'));
        }

        return $query->get();
    }

    public function cities(Request $request)
    {
        $province_id = $request->input('province_id');
        if (empty(trim($province_id))) {
            return [];
        }

        return City::where('province_id', $province_id)
            ->orderBy('city_name')
            ->get();
    }

    public function barangays(Request $request)
    {
        $city_id = $request->input('city_id');
        if (empty(trim($city_id))) {
            return [];
        }

        return Barangay::where('city_id', $city_id)
            ->orderBy('barangay_name')
            ->get();
    }

    public function getPhilippines()
    {
        return Country::where('iso2', 'PH')->first();
    }
}
