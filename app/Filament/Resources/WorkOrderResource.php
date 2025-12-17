<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkOrderResource\Pages;
use App\Filament\Resources\WorkOrderResource\RelationManagers;
use App\Models\WorkOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkOrderResource extends Resource
{
    protected static ?string $model = WorkOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $modelLabel = 'Commessa';
    protected static ?string $pluralModelLabel = 'Commesse';
    protected static ?string $navigationLabel = 'Commesse';
    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Dettagli Commessa')
                            ->schema([
                                Forms\Components\TextInput::make('number')
                                    ->label('Numero Commessa')
                                    ->default('COM-' . date('Y') . '-')
                                    ->required(),
                                Forms\Components\Select::make('client_id')
                                    ->relationship('client', 'name')
                                    ->label('Cliente')
                                    ->required()
                                    ->searchable(),
                                Forms\Components\Select::make('estimate_id')
                                    ->relationship('estimate', 'number')
                                    ->label('Rif. Preventivo')
                                    ->searchable(),
                                Forms\Components\Select::make('priority')
                                    ->label('Priorità')
                                    ->options([
                                        'low' => 'Bassa',
                                        'normal' => 'Normale',
                                        'high' => 'Alta',
                                        'urgent' => 'Urgente',
                                    ])
                                    ->default('normal')
                                    ->required(),
                                Forms\Components\Select::make('status')
                                    ->label('Stato')
                                    ->options([
                                        'pending' => 'In Attesa',
                                        'working' => 'In Lavorazione',
                                        'completed' => 'Completata',
                                        'cancelled' => 'Annullata',
                                    ])
                                    ->default('pending')
                                    ->required(),
                            ])->columns(2),
                    ])->columnSpan(['lg' => 2]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Date')
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('Data Inizio Lavori'),
                                Forms\Components\DatePicker::make('due_date')
                                    ->label('Data Consegna Prevista'),
                            ]),
                    ])->columnSpan(['lg' => 1]),
                Forms\Components\Section::make('Descrizione Lavori')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Descrizione da Preventivo')
                            ->rows(5),
                        Forms\Components\Textarea::make('notes')
                            ->label('Note Interne')
                            ->rows(3),
                    ])->columnSpanFull(),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')->label('#')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('client.name')->label('Cliente')->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'working' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Priorità')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'low' => 'gray',
                        'normal' => 'info',
                        'high' => 'warning',
                        'urgent' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('due_date')->date()->label('Consegna'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'In Attesa',
                        'working' => 'In Lavorazione',
                        'completed' => 'Completata',
                        'cancelled' => 'Annullata',
                    ]),
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
            'index' => Pages\ListWorkOrders::route('/'),
            'create' => Pages\CreateWorkOrder::route('/create'),
            'edit' => Pages\EditWorkOrder::route('/{record}/edit'),
        ];
    }
}
