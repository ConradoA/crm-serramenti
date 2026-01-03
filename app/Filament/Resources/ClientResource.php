<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $modelLabel = 'Cliente';
    protected static ?string $pluralModelLabel = 'Clienti';
    protected static ?string $navigationLabel = 'Clienti';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->label('Nome Referente'),
                Forms\Components\TextInput::make('company_name')->label('Nome Azienda (Opzionale)'),
                Forms\Components\TextInput::make('email')->email(),
                Forms\Components\TextInput::make('phone')->tel()->label('Telefono'),
                Forms\Components\TextInput::make('vat_number')->label('P.IVA'),
                Forms\Components\TextInput::make('fiscal_code')->label('Codice Fiscale'),
                Forms\Components\TextInput::make('address')->label('Indirizzo'),
                Forms\Components\TextInput::make('city')->label('Città'),
                Forms\Components\TextInput::make('cap')->label('CAP'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->label('Referente'),
                Tables\Columns\TextColumn::make('company_name')->searchable()->sortable()->label('Azienda'),
                Tables\Columns\TextColumn::make('email')->icon('heroicon-m-envelope'),
                Tables\Columns\TextColumn::make('phone')->label('Telefono'),
                Tables\Columns\TextColumn::make('city')->label('Città'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($record, $action) {
                        if ($record->estimates()->exists()) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Impossibile eliminare')
                                ->body('Il cliente ha preventivi associati. Elimina prima i preventivi.')
                                ->send();
                            $action->cancel();
                        }
                        // Check for work orders (assuming relationship exists)
                        if (method_exists($record, 'workOrders') && $record->workOrders()->exists()) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Impossibile eliminare')
                                ->body('Il cliente ha commesse associate. Elimina prima le commesse.')
                                ->send();
                            $action->cancel();
                        }
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
