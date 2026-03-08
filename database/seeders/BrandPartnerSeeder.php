<?php

namespace Database\Seeders;

use App\Enums\BrandPartnerStatus;
use App\Models\BrandPartner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BrandPartnerSeeder extends Seeder
{
    public function run(): void
    {
        BrandPartner::firstOrCreate(
            ['slug' => 'pakaras'],
            [
                'name'           => 'Tribu Pakaras Outdoors',
                'email'          => 'pakaras@tpinklab.com',
                'password'       => Hash::make('password'),
                'contact_person' => null,
                'phone'          => null,
                'address'        => null,
                'description'    => null,
                'status'         => BrandPartnerStatus::ACTIVE,
            ]
        );
    }
}
