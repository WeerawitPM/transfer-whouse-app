<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmplrResource\Pages;
use App\Filament\Resources\EmplrResource\RelationManagers;
use App\Models\Emplr;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmplrResource extends Resource
{
    protected static ?string $model = Emplr::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('FCSKID')
                    ->required(),
                Forms\Components\TextInput::make('FCLOGIN')
                    ->required(),
                Forms\Components\TextInput::make('FCPW')
                    ->required(),
                Forms\Components\TextInput::make('FCRCODE')
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
                Tables\Columns\TextColumn::make('FCSKID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCLOGIN')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCPW')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCRCODE')
                    ->searchable(),
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
            'index' => Pages\ListEmplrs::route('/'),
            'create' => Pages\CreateEmplr::route('/create'),
            'edit' => Pages\EditEmplr::route('/{record}/edit'),
        ];
    }
}
