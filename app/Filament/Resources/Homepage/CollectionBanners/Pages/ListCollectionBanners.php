<?php

namespace App\Filament\Resources\Homepage\CollectionBanners\Pages;

use App\Filament\Resources\Homepage\CollectionBannerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCollectionBanners extends ListRecords
{
    protected static string $resource = CollectionBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
