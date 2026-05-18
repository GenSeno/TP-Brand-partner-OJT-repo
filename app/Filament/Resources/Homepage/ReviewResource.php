<?php

namespace App\Filament\Resources\Homepage;

use App\Filament\Resources\Homepage\Reviews\Pages\CreateReview;
use App\Filament\Resources\Homepage\Reviews\Pages\EditReview;
use App\Filament\Resources\Homepage\Reviews\Pages\ListReviews;
use App\Filament\Resources\Homepage\Reviews\Pages\ViewReview;
use App\Models\HomepageReview;
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

class ReviewResource extends Resource
{
    protected static ?string $model = HomepageReview::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static ?string $recordTitleAttribute = 'name';

    protected static \UnitEnum|string|null $navigationGroup = 'Homepage';

    protected static ?string $navigationLabel = 'Reviews';

    public static function form(Schema $form): Schema
    {
        return $form
            ->columns(1)
            ->components([
                Forms\Components\Select::make('brand_partner_id')
                    ->relationship('brandPartner', 'name')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('role')
                    ->label('Role (e.g. "42k Finisher")')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\Textarea::make('comment')
                    ->required(),
                Forms\Components\FileUpload::make('avatar')
                    ->image()
                    ->nullable(),
                Forms\Components\Select::make('rating')
                    ->options([
                        1 => '1 Star',
                        2 => '2 Stars',
                        3 => '3 Stars',
                        4 => '4 Stars',
                        5 => '5 Stars',
                    ])
                    ->default(5),
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
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('role')->label('Role'),
                Tables\Columns\TextColumn::make('comment')->limit(60),
                Tables\Columns\TextColumn::make('rating')->sortable(),
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
            'index' => ListReviews::route('/'),
            'create' => CreateReview::route('/create'),
            'view' => ViewReview::route('/{record}'),
            'edit' => EditReview::route('/{record}/edit'),
        ];
    }
}
