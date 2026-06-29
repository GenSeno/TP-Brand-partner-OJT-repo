<?php

namespace App\Filament\Resources\Store\Policies\Pages;

use App\Filament\Resources\Store\Policies\PolicyResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPolicy extends ViewRecord
{
    protected static string $resource = PolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
