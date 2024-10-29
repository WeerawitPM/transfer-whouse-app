<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RefTypeResource\Pages;
use App\Filament\Resources\RefTypeResource\RelationManagers;
use App\Models\RefType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RefTypeResource extends Resource
{
    protected static ?string $model = RefType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('FCSKID')
                    ->required(),
                Forms\Components\TextInput::make('FCCODE')
                    ->required(),
                Forms\Components\TextInput::make('FCRETYPE')
                    ->required(),
                Forms\Components\TextInput::make('FCNAME')
                    ->required(),
                Forms\Components\TextInput::make('FCNAME2')
                    ->required(),
                Forms\Components\TextInput::make('FCNGLNAME')
                    ->required(),
                Forms\Components\TextInput::make('FCREPNAME')
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
                Tables\Columns\TextColumn::make('FCCODE')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCRETYPE')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCNAME')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCNAME2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCNGLNAME')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCREPNAME')
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
            'index' => Pages\ListRefTypes::route('/'),
            'create' => Pages\CreateRefType::route('/create'),
            'edit' => Pages\EditRefType::route('/{record}/edit'),
        ];
    }
}
