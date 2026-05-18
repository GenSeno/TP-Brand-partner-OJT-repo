<?php

namespace App\Filament\Resources\Homepage;

use App\Filament\Resources\Homepage\CollectionBanners\Pages\CreateCollectionBanner;
use App\Filament\Resources\Homepage\CollectionBanners\Pages\EditCollectionBanner;
use App\Filament\Resources\Homepage\CollectionBanners\Pages\ListCollectionBanners;
use App\Filament\Resources\Homepage\CollectionBanners\Pages\ViewCollectionBanner;
use App\Models\HomepageCollectionBanner;
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

class CollectionBannerResource extends Resource
{
    protected static ?string $model = HomepageCollectionBanner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static ?string $recordTitleAttribute = 'title';

    protected static \UnitEnum|string|null $navigationGroup = 'Homepage';

    protected static ?string $navigationLabel = 'Collection Banners';

    public static function form(Schema $form): Schema
    {
        return $form
            ->columns(1)
            ->components([
                Forms\Components\Select::make('brand_partner_id')
                    ->relationship('brandPartner', 'name')
                    ->required(),
                Forms\Components\Select::make('section_type')
                    ->options([
                        'banner' => 'Hero Banner (large top banner)',
                        'panel' => 'Panel (grid card)',
                    ])
                    ->default('panel')
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('badge_text')
                    ->label('Badge Text (e.g. "THE")')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
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
                    ->default('VIEW COLLECTION')
                    ->maxLength(255),
                Forms\Components\TextInput::make('overlay_class')
                    ->label('CSS Overlay Class')
                    ->nullable()
                    ->maxLength(255),
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
                Tables\Columns\TextColumn::make('section_type')->badge(),
                Tables\Columns\TextColumn::make('brandPartner.name')->label('Brand'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->label('Order')->sortable(),
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
            'index' => ListCollectionBanners::route('/'),
            'create' => CreateCollectionBanner::route('/create'),
            'view' => ViewCollectionBanner::route('/{record}'),
            'edit' => EditCollectionBanner::route('/{record}/edit'),
        ];
    }
}
