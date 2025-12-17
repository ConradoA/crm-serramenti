<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialResource\Pages;
use App\Filament\Resources\MaterialResource\RelationManagers;
use App\Models\Material;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $modelLabel = 'Materiale';
    protected static ?string $pluralModelLabel = 'Materiali';
    protected static ?string $navigationLabel = 'Materiali';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->label('Nome Materiale'),
                Forms\Components\Select::make('category')
                    ->options([
                        'profile' => 'Profilo / Barra',
                        'glass' => 'Vetro',
                        'mechanism' => 'Meccanismo / Motore',
                        'accessory' => 'Accessorio',
                        'labor' => 'Manodopera',
                    ])
                    ->required(),
                Forms\Components\Select::make('unit')
                    ->options([
                        'm' => 'Metri Lineari',
                        'mq' => 'Metri Quadri',
                        'pz' => 'Pezzi',
                        'h' => 'Ore',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('cost_price')
                    ->numeric()
                    ->prefix('€')
                    ->label('Prezzo di Costo'),
                Forms\Components\Select::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('category')->badge(),
                Tables\Columns\TextColumn::make('unit'),
                Tables\Columns\TextColumn::make('cost_price')
                    ->prefix('€ ')
                    ->numeric(decimalPlaces: 2),
                Tables\Columns\TextColumn::make('supplier.name')->label('Fornitore'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaterials::route('/'),
            'create' => Pages\CreateMaterial::route('/create'),
            'edit' => Pages\EditMaterial::route('/{record}/edit'),
        ];
    }
}
