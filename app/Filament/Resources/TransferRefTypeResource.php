<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransferRefTypeResource\Pages;
use App\Models\TransferRefType;
use App\Models\RefType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;

class TransferRefTypeResource extends Resource
{
    protected static ?string $model = TransferRefType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Setup';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(1)
                    ->schema([
                        Select::make('ref_type_id')
                            ->label('Ref Type ID')
                            // ->relationship('ref_type')
                            // ->getOptionLabelFromRecordUsing(fn(RefType $record) => "{$record->FCCODE} {$record->FCNAME}")
                            ->options(RefType::all()->mapWithKeys(function (RefType $book) {
                                return [$book->id => "{$book->FCCODE} {$book->FCNAME}"];
                            }))
                            ->searchable()
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->required(),
                    ]),
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
                Tables\Columns\TextColumn::make('ref_type')
                    ->formatStateUsing(fn(TransferRefType $record) => "{$record->ref_type->FCCODE} {$record->ref_type->FCNAME}")
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
            'index' => Pages\ListTransferRefTypes::route('/'),
            // 'create' => Pages\CreateTransferRefType::route('/create'),
            // 'edit' => Pages\EditTransferRefType::route('/{record}/edit'),
        ];
    }
}
