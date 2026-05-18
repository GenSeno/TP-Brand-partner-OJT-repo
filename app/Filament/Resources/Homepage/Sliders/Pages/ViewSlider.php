<?php

namespace App\Filament\Resources\Homepage\Sliders\Pages;

use App\Filament\Resources\Homepage\SliderResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSlider extends ViewRecord
{
    protected static string $resource = SliderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
