<?php

namespace App\Filament\Resources\EstimateResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InteractionsRelationManager extends RelationManager
{
    protected static string $relationship = 'interactions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Tipo')
                    ->options([
                        'call' => 'Chiamata Telefonica',
                        'email' => 'Email',
                        'meeting' => 'Incontro / Appuntamento',
                        'note' => 'Nota Generica',
                        'whatsapp' => 'WhatsApp',
                    ])
                    ->required()
                    ->prefixIcon('heroicon-m-tag'),
                Forms\Components\Textarea::make('content')
                    ->label('Contenuto / Note')
                    ->required()
                    ->columnSpanFull()
                    ->rows(3),
                Forms\Components\Hidden::make('user_id')
                    ->default(fn() => auth()->id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->width('150px'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'call' => 'info',
                        'email' => 'warning',
                        'meeting' => 'success',
                        'note' => 'gray',
                        'whatsapp' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'call' => 'Chiamata',
                        'email' => 'Email',
                        'meeting' => 'Incontro',
                        'note' => 'Nota',
                        'whatsapp' => 'WhatsApp',
                        default => ucfirst($state),
                    }),
                Tables\Columns\TextColumn::make('content')
                    ->label('Note')
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Autore')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
