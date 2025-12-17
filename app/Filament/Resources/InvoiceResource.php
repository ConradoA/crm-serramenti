<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-euro';
    protected static ?string $modelLabel = 'Fattura';
    protected static ?string $pluralModelLabel = 'Fatture';
    protected static ?string $navigationLabel = 'Fatture';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->relationship('client', 'name')
                    ->required()
                    ->label('Cliente'),
                Forms\Components\Select::make('estimate_id')
                    ->relationship('estimate', 'number')
                    ->label('Rif. Preventivo')
                    ->searchable(),
                Forms\Components\TextInput::make('number')
                    ->label('Numero Fattura')
                    ->default('FAT-' . date('Y') . '-')
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->label('Data Emissione')
                    ->default(now())
                    ->required(),
                Forms\Components\DatePicker::make('due_date')
                    ->label('Scadenza'),
                Forms\Components\Select::make('type')
                    ->options([
                        'deposit' => 'Acconto',
                        'balance' => 'Saldo',
                        'full' => 'Saldo Completo',
                    ])
                    ->default('deposit')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->label('Importo')
                    ->prefix('€ ')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Bozza',
                        'paid' => 'Pagata',
                        'overdue' => 'Scaduta',
                    ])
                    ->default('draft')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')->searchable()->label('#'),
                Tables\Columns\TextColumn::make('client.name')->label('Cliente')->sortable(),
                Tables\Columns\TextColumn::make('date')->date()->label('Data'),
                Tables\Columns\TextColumn::make('amount')->prefix('€ ')->label('Importo'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'paid' => 'success',
                        'overdue' => 'danger',
                    }),
            ])
            ->filters([
                //
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
            EstimateResource\RelationManagers\InteractionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
