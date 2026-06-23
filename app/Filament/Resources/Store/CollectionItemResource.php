<?php

namespace App\Filament\Resources\Store;

use App\Filament\Resources\Store\CollectionItems\Pages\CreateCollectionItem;
use App\Filament\Resources\Store\CollectionItems\Pages\EditCollectionItem;
use App\Filament\Resources\Store\CollectionItems\Pages\ListCollectionItems;
use App\Filament\Resources\Store\CollectionItems\Pages\ViewCollectionItem;
use App\Models\StoreCollectionItem;
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

class CollectionItemResource extends Resource
{
    protected static ?string $model = StoreCollectionItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    protected static \UnitEnum|string|null $navigationGroup = 'Collections Page';

    protected static ?string $navigationLabel = 'Collection Items';

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
                Forms\Components\Textarea::make('description')
                    ->nullable(),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->nullable(),
                Forms\Components\TextInput::make('link_url')
                    ->label('Link URL')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('link_text')
                    ->label('Link Text')
                    ->default('VIEW COLLECTION')
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
            'index' => ListCollectionItems::route('/'),
            'create' => CreateCollectionItem::route('/create'),
            'view' => ViewCollectionItem::route('/{record}'),
            'edit' => EditCollectionItem::route('/{record}/edit'),
        ];
    }
}
