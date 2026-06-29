<?php

namespace App\Filament\Resources\Store\Policies\Pages;

use App\Filament\Resources\Store\Policies\PolicyResource;
use Filament\Resources\Pages\ListRecords;

class ListPolicies extends ListRecords
{
    protected static string $resource = PolicyResource::class;
}
