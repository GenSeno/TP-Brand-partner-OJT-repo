<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class EventStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Events', Event::count())
                ->description('All events in the system')
                ->icon('heroicon-o-calendar'),

            Stat::make('Published Events', Event::where('is_published', true)->count())
                ->description('Visible on the website')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Unpublished Events', Event::where('is_published', false)->count())
                ->description('Not yet visible')
                ->color('warning')
                ->icon('heroicon-o-eye-slash'),

            Stat::make('Upcoming Events', Event::where('event_date', '>=', Carbon::today())->count())
                ->description('From today onwards')
                ->color('info')
                ->icon('heroicon-o-clock'),
        ];
    }
}