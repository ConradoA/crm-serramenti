<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $modelLabel = 'Dati Aziendali';
    protected static ?string $pluralModelLabel = 'Dati Aziendali';
    protected static ?string $navigationLabel = 'Dati Aziendali';
    protected static ?string $navigationGroup = 'Impostazioni';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informazioni Generali')
                    ->schema([
                        Forms\Components\FileUpload::make('logo_path')
                            ->label('Logo Aziendale')
                            ->image()
                            ->directory('company-logos')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            ->label('Ragione Sociale')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('p_iva')
                            ->label('P.IVA / Codice Fiscale'),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->label('Email'),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->label('Telefono'),
                        Forms\Components\TextInput::make('iban')
                            ->label('IBAN (per pagamenti)')
                            ->columnSpanFull(),
                    ])->columns(2),
                Forms\Components\Section::make('Indirizzo')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->label('Indirizzo'),
                        Forms\Components\TextInput::make('city')
                            ->label('Città'),
                        Forms\Components\TextInput::make('cap')
                            ->label('CAP'),
                    ])->columns(3),
                Forms\Components\Section::make('Extra')
                    ->schema([
                        Forms\Components\Textarea::make('footer_notes')
                            ->label('Note a piè di pagina (PDF)')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo_path')->label('Logo'),
                Tables\Columns\TextColumn::make('name')->label('Nome'),
                Tables\Columns\TextColumn::make('p_iva')->label('P.IVA'),
                Tables\Columns\TextColumn::make('city')->label('Città'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
