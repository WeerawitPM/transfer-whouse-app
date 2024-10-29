<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransferBookResource\Pages;
use App\Models\TransferBook;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;

class TransferBookResource extends Resource
{
    protected static ?string $model = TransferBook::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('transfer_ref_type_id')
                    ->label('Transfer Ref Type ID')
                    ->relationship('transfer_ref_type', 'FCNAME')
                    ->required(),
                Select::make('book_id')
                    ->label('Book')
                    ->relationship('book', 'FCNAME')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('transfer_ref_type_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('book_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
            'index' => Pages\ListTransferBooks::route('/'),
            'create' => Pages\CreateTransferBook::route('/create'),
            'edit' => Pages\EditTransferBook::route('/{record}/edit'),
        ];
    }
}
