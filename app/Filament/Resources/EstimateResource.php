<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstimateResource\Pages;
use App\Filament\Resources\EstimateResource\RelationManagers;
use App\Filament\Resources\WorkOrderResource;
use App\Models\Estimate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EstimateResource extends Resource
{
    protected static ?string $model = Estimate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $modelLabel = 'Preventivo';
    protected static ?string $pluralModelLabel = 'Preventivi';
    protected static ?string $navigationLabel = 'Preventivi';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Dati Generali')
                        ->schema([
                            Forms\Components\Select::make('client_id')
                                ->label('Cliente')
                                ->relationship('client', 'name')
                                ->searchable()
                                ->required()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')->required()->label('Nome'),
                                    Forms\Components\TextInput::make('email')->email(),
                                    Forms\Components\TextInput::make('phone')->tel(),
                                ]),
                            Forms\Components\DatePicker::make('date')
                                ->label('Data Preventivo')
                                ->default(now())
                                ->required(),
                            Forms\Components\DatePicker::make('valid_until')
                                ->label('Valido fino al')
                                ->default(now()->addDays(30)),
                            Forms\Components\Select::make('status')
                                ->label('Stato')
                                ->options([
                                    'draft' => 'Bozza',
                                    'waiting' => 'In Attesa',
                                    'sent' => 'Inviato',
                                    'approved' => 'Approvato',
                                    'rejected' => 'Rifiutato',
                                ])
                                ->default('draft')
                                ->required(),
                            Forms\Components\TextInput::make('number')
                                ->label('Numero Preventivo')
                                ->placeholder('Auto-generato al salvataggio')
                                ->disabled(),
                        ]),
                    Forms\Components\Wizard\Step::make('Prodotti e Misure')
                        ->schema([
                            Forms\Components\Repeater::make('items')
                                ->relationship()
                                ->schema([
                                    Forms\Components\Grid::make(3)
                                        ->schema([
                                            Forms\Components\Select::make('product_type')
                                                ->label('Tipo Prodotto')
                                                ->options(fn() => \App\Models\Product::where('is_active', true)->pluck('name', 'name'))
                                                ->required()
                                                ->reactive(),
                                            Forms\Components\TextInput::make('name')
                                                ->label('Riferimento (es. Cucina)')
                                                ->required()
                                                ->columnSpan(2),
                                        ]),
                                    Forms\Components\Grid::make(4)
                                        ->schema([
                                            Forms\Components\TextInput::make('width')
                                                ->label('Larghezza (mm)')
                                                ->numeric()
                                                ->suffix('mm')
                                                ->required(),
                                            Forms\Components\TextInput::make('height')
                                                ->label('Altezza (mm)')
                                                ->numeric()
                                                ->suffix('mm')
                                                ->required(),
                                            Forms\Components\TextInput::make('quantity')
                                                ->label('Quantità')
                                                ->numeric()
                                                ->default(1)
                                                ->required(),
                                            Forms\Components\TextInput::make('unit_price')
                                                ->label('Prezzo Unitario')
                                                ->numeric()
                                                ->prefix('€')
                                                ->required(),
                                        ]),
                                    Forms\Components\KeyValue::make('attributes')
                                        ->label('Caratteristiche Extra')
                                        ->keyLabel('Caratteristica (es. Colore)')
                                        ->valueLabel('Valore (es. Bianco)'),
                                    Forms\Components\FileUpload::make('photos')
                                        ->label('Foto e Disegni')
                                        ->multiple()
                                        ->image()
                                        ->directory('estimate-photos')
                                        ->columnSpanFull(),
                                ])
                                ->columns(1)
                                ->label('Lista Articoli'),
                        ]),
                    Forms\Components\Wizard\Step::make('Note e Totale')
                        ->schema([
                            Forms\Components\Textarea::make('public_notes')
                                ->label('Note per il Cliente (visibili in PDF)')
                                ->placeholder('Esempio: Serie PIATTAFORMA 70 profilo arrotondato - Telaio Z40...')
                                ->rows(5),
                            Forms\Components\Textarea::make('internal_notes')
                                ->label('Note Interne (Nascoste)')
                                ->rows(3),
                            Forms\Components\Section::make('Totali')
                                ->schema([
                                    Forms\Components\TextInput::make('installation_amount')
                                        ->label('Costo Posa (Manodopera)')
                                        ->prefix('€')
                                        ->numeric()
                                        ->default(0),
                                    Forms\Components\Placeholder::make('total_preview')
                                        ->label('Totale Stimato')
                                        ->content('Il totale verrà calcolato al salvataggio.'),
                                ]),
                        ]),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('client.name')->label('Cliente')->searchable(),
                Tables\Columns\TextColumn::make('date')->date()->label('Data'),
                Tables\Columns\TextColumn::make('total')
                    ->prefix('€ ')
                    ->numeric(decimalPlaces: 2)
                    ->label('Totale'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'waiting' => 'info',
                        'sent' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Estimate $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.estimate', ['record' => $record])->output();
                        }, "Preventivo-{$record->number}.pdf");
                    }),
                Tables\Actions\Action::make('approve')
                    ->label('Approva & Genera Commessa')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approvare Preventivo?')
                    ->modalDescription('Questo approverà il preventivo e genererà automaticamente una Commessa di lavorazione.')
                    ->action(function (Estimate $record) {
                        // 1. Update Estimate Status
                        $record->update(['status' => 'approved']);

                        // 2. Generate Work Order Description from Items
                        $description = "Rif. Preventivo: {$record->number}\n\n";
                        foreach ($record->items as $item) {
                            $description .= "- {$item->quantity}x {$item->name} ({$item->width}x{$item->height})\n";
                        }

                        // 3. Create Work Order
                        $workOrder = \App\Models\WorkOrder::create([
                            'estimate_id' => $record->id,
                            'client_id' => $record->client_id,
                            'number' => 'COM-' . date('Y') . '-' . str_pad($record->id, 4, '0', STR_PAD_LEFT),
                            'status' => 'pending',
                            'description' => $description,
                            'priority' => 'normal',
                            'start_date' => now(),
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Preventivo Approvato & Commessa Creata')
                            ->success()
                            ->send();

                        return redirect()->to(WorkOrderResource::getUrl('edit', ['record' => $workOrder]));
                    })
                    ->visible(fn(Estimate $record) => $record->status !== 'approved'),
                Tables\Actions\Action::make('email')
                    ->label('Invia Email')
                    ->icon('heroicon-o-envelope')
                    ->requiresConfirmation()
                    ->action(function (Estimate $record) {
                        \Illuminate\Support\Facades\Mail::to($record->client->email)
                            ->send(new \App\Mail\EstimateMail($record));

                        \Filament\Notifications\Notification::make()
                            ->title('Email inviata con successo')
                            ->success()
                            ->send();

                        $record->update(['status' => 'sent']);
                    })
                    ->visible(fn(Estimate $record) => $record->client && $record->client->email),
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
            RelationManagers\InteractionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEstimates::route('/'),
            'create' => Pages\CreateEstimate::route('/create'),
            'edit' => Pages\EditEstimate::route('/{record}/edit'),
        ];
    }
}
