<?php

namespace App\Filament\Resources\Store\Policies\Pages;

use App\Filament\Resources\Store\Policies\PolicyResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePolicy extends CreateRecord
{
    protected static string $resource = PolicyResource::class;
}
