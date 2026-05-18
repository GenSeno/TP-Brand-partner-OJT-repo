<?php

namespace App\Filament\Resources\Events;

use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Filament\Resources\Events\Pages\EditEvent;
use App\Filament\Resources\Events\Pages\ListEvents;
use App\Filament\Resources\Events\Pages\ViewEvent;
use App\Models\Event;
use BackedEnum;
use Filament\Forms;
use Filament\Schemas\Components\Group;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;


class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                Forms\Components\TextInput::make('title')
                ->required()
                ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->nullable(),

                Forms\Components\TextInput::make('location')
                    ->placeholder('e.g. Gaisano Mall of Gensan, General Santos City')
                    ->nullable(),

                Forms\Components\DatePicker::make('event_date')
                    ->required(),

                Group::make([
                    Forms\Components\CheckBoxList::make('distances')
                        ->options([
                            '5KM'  => '5KM',
                            '21KM' => '21KM',
                            '10KM' => '10KM',
                            '42KM' => '42KM',
                            '15KM' => '15KM',
                            'Other'=> 'Other'
                        ])
                        ->columns(3)
                        ->live(),

                    Forms\Components\Repeater::make('other_distances')
                        ->label('Specify Other Distance')
                        ->schema([
                            Forms\Components\TextInput::make('value')
                                ->placeholder('e.g. Ultra Marathon, 100KM...')
                                ->required(),
                        ])
                        ->addActionLabel('Add another distance')
                        ->visible(fn ($get) => in_array('Other', $get('distances') ?? []))
                ])
                    ->columnSpan(1),
                    

                Forms\Components\FileUpload::make('image')
                    ->disk('public')
                    ->directory('events')
                    ->image()
                    ->nullable(),

                Forms\Components\Toggle::make('is_published')
                    ->label('Published')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('title')->searchable(),
            Tables\Columns\TextColumn::make('event_date')->date()->sortable(),
            Tables\Columns\IconColumn::make('is_published')->boolean(),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
        ])
        ->filters([])
        ->recordActions([       
            ActionGroup::make([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]),
        ])
        ->bulkActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
}

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvents::route('/'),
            'create' => CreateEvent::route('/create'),
            'view' => ViewEvent::route('/{record}'),
            'edit' => EditEvent::route('/{record}/edit'),
        ];
    }
}