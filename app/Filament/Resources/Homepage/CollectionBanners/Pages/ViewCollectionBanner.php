<?php

namespace App\Filament\Resources\Homepage\CollectionBanners\Pages;

use App\Filament\Resources\Homepage\CollectionBannerResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCollectionBanner extends ViewRecord
{
    protected static string $resource = CollectionBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
