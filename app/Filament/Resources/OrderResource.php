<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $modelLabel = 'Ordine Fornitore';
    protected static ?string $pluralModelLabel = 'Ordini Fornitori';
    protected static ?string $navigationLabel = 'Ordini';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->required()
                    ->label('Fornitore'),
                Forms\Components\Select::make('estimate_id')
                    ->relationship('estimate', 'number')
                    ->label('Rif. Preventivo')
                    ->searchable(),
                Forms\Components\TextInput::make('number')
                    ->label('Numero Ordine')
                    ->default('ORD-' . date('Y') . '-')
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->label('Data')
                    ->default(now())
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Bozza',
                        'sent' => 'Inviato',
                        'completed' => 'Completato',
                    ])
                    ->default('draft')
                    ->required(),
                Forms\Components\TextInput::make('total_amount')
                    ->label('Totale')
                    ->prefix('€ ')
                    ->numeric()
                    ->default(0),
                Forms\Components\Textarea::make('notes')->columnSpanFull(),
                Forms\Components\FileUpload::make('attachments')
                    ->label('Allegati (Disegni, Foto)')
                    ->multiple()
                    ->directory('order-attachments')
                    ->columnSpanFull()
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->maxFiles(5),
                Forms\Components\Section::make('Articoli Ordine')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\Grid::make(4)
                                    ->schema([
                                        Forms\Components\Select::make('material_id')
                                            ->label('Materiale')
                                            ->relationship('material', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->reactive()
                                            ->afterStateUpdated(fn($state, Forms\Set $set) => $set('unit_price', \App\Models\Material::find($state)?->cost_price ?? 0))
                                            ->columnSpan(2)
                                            ->required(),
                                        Forms\Components\TextInput::make('quantity')
                                            ->label('Qta')
                                            ->numeric()
                                            ->default(1)
                                            ->reactive()
                                            ->afterStateUpdated(fn($state, Forms\Get $get, Forms\Set $set) => $set('total_price', $state * $get('unit_price')))
                                            ->required(),
                                        Forms\Components\TextInput::make('unit_price')
                                            ->label('Prezzo Unit.')
                                            ->numeric()
                                            ->prefix('€')
                                            ->reactive()
                                            ->afterStateUpdated(fn($state, Forms\Get $get, Forms\Set $set) => $set('total_price', $state * $get('quantity')))
                                            ->required(),
                                        Forms\Components\TextInput::make('total_price')
                                            ->label('Totale Riga')
                                            ->numeric()
                                            ->prefix('€')
                                            ->readOnly()
                                            ->required(),
                                    ]),
                                Forms\Components\TextInput::make('name')
                                    ->label('Descrizione Manuale (opzionale)')
                                    ->placeholder('Se vuoto, usa nome materiale'),
                            ])
                            ->columns(1)
                            ->collapsed(false)
                            ->itemLabel(fn(array $state): ?string => $state['name'] ?? null),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')->searchable()->label('#'),
                Tables\Columns\TextColumn::make('supplier.name')->label('Fornitore')->sortable(),
                Tables\Columns\TextColumn::make('date')->date()->label('Data'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('total_amount')->prefix('€ ')->label('Totale'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (Order $record) {
                        $company = \App\Models\Company::first();
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.order', ['order' => $record, 'company' => $company]);
                        return response()->streamDownload(fn() => print ($pdf->output()), "Ordine_{$record->number}.pdf");
                    }),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
