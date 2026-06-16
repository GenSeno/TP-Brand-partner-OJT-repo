<?php

namespace App\Filament\Resources\Store\CollectionItems\Pages;

use App\Filament\Resources\Store\CollectionItemResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCollectionItem extends EditRecord
{
    protected static string $resource = CollectionItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
