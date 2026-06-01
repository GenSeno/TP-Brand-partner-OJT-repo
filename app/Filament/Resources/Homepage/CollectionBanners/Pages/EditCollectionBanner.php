<?php

namespace App\Filament\Resources\Homepage\CollectionBanners\Pages;

use App\Filament\Resources\Homepage\CollectionBannerResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCollectionBanner extends EditRecord
{
    protected static string $resource = CollectionBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
