<?php

namespace App\Filament\Resources\Homepage;

use App\Filament\Resources\Homepage\Sliders\Pages\CreateSlider;
use App\Filament\Resources\Homepage\Sliders\Pages\EditSlider;
use App\Filament\Resources\Homepage\Sliders\Pages\ListSliders;
use App\Filament\Resources\Homepage\Sliders\Pages\ViewSlider;
use App\Models\HomepageSlider;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class SliderResource extends Resource
{
    protected static ?string $model = HomepageSlider::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $recordTitleAttribute = 'title';

    protected static \UnitEnum|string|null $navigationGroup = 'Homepage';

    protected static ?string $navigationLabel = 'Carousel Slides';

    public static function form(Schema $form): Schema
    {
        return $form
            ->columns(1)
            ->components([
                Forms\Components\Select::make('brand_partner_id')
                    ->relationship('brandPartner', 'name')
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('subtitle')
                    ->nullable(),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->label('Background Image')
                    ->nullable(),
                Forms\Components\TextInput::make('link_url')
                    ->label('Button Link URL')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('link_text')
                    ->label('Button Text')
                    ->default('VIEW ALL PRODUCTS')
                    ->maxLength(255),
                Forms\Components\Select::make('overlay_type')
                    ->options([
                        'dark' => 'Dark Gradient',
                        'orange' => 'Orange Slant',
                        'none' => 'No Overlay',
                    ])
                    ->default('dark'),
                Forms\Components\Select::make('content_position')
                    ->options([
                        'left' => 'Left',
                        'center' => 'Center',
                    ])
                    ->default('left'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('brandPartner.name')->label('Brand'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->label('Order')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('sort_order')
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
            'index' => ListSliders::route('/'),
            'create' => CreateSlider::route('/create'),
            'view' => ViewSlider::route('/{record}'),
            'edit' => EditSlider::route('/{record}/edit'),
        ];
    }
}
