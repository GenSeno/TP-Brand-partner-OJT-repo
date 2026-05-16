<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class UpcomingEventsWidget extends BaseWidget
{
    protected static ?string $heading = 'Upcoming & Recent Events';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Event::query()
                    ->orderBy('event_date', 'asc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('event_date')
                    ->date('F d, Y')
                    ->sortable()
                    ->description(fn (Event $record) => Carbon::parse($record->event_date)->isToday()
                        ? '📅 Today!'
                        : (Carbon::parse($record->event_date)->isFuture()
                            ? '⏳ In ' . Carbon::parse($record->event_date)->diffForHumans()
                            : '✅ ' . Carbon::parse($record->event_date)->diffForHumans())),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean()
                    ->label('Published'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Created')
                    ->sortable(),
            ]);
    }
}
