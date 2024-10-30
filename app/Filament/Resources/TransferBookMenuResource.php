<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransferBookMenuResource\Pages;
use App\Models\TransferBook;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TransferBookMenuResource extends Resource
{
    protected static ?string $model = TransferBook::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Transfer Book Menu';
    protected static ?string $breadcrumb = 'Transfer Book Menu';
    protected static ?string $label = 'Transfer Book Menu';
    protected static ?string $pluralLabel = 'Transfer Book Menu';
    protected static ?string $navigationGroup = 'WMS Transfer Book';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(TransferBook::query()->where('is_active', true)) // ใช้เมธอด query ตรงๆ
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('transfer_ref_type')
                    ->formatStateUsing(fn(TransferBook $record) => "{$record->transfer_ref_type->ref_type->FCCODE} {$record->transfer_ref_type->ref_type->FCNAME}")
                    ->sortable(),
                Tables\Columns\TextColumn::make('book')
                    ->formatStateUsing(fn(TransferBook $record) => "{$record->book->FCCODE} {$record->book->FCNAME}")
                    ->sortable(),
                Tables\Columns\TextColumn::make('book.from_whs')
                    ->label('From Warehouse')
                    ->formatStateUsing(fn(TransferBook $record) => $record->book->from_whs ? "{$record->book->from_whs->FCCODE} {$record->book->from_whs->FCNAME}" : 'N/A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('book.to_whs')
                    ->label('To Warehouse')
                    ->formatStateUsing(fn(TransferBook $record) => $record->book->to_whs ? "{$record->book->to_whs->FCCODE} {$record->book->to_whs->FCNAME}" : 'N/A')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListTransferBookMenus::route('/'),
            // 'create' => Pages\CreateTransferBookMenu::route('/create'),
            // 'edit' => Pages\EditTransferBookMenu::route('/{record}/edit'),
        ];
    }
}
