<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CorpResource\Pages;
use App\Filament\Resources\CorpResource\RelationManagers;
use App\Models\Corp;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CorpResource extends Resource
{
    protected static ?string $model = Corp::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Formula';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('FCSKID')
                    ->required(),
                Forms\Components\TextInput::make('FCCODE')
                    ->required(),
                Forms\Components\TextInput::make('FCNAME')
                    ->required(),
                Forms\Components\TextInput::make('FCTAXID')
                    ->required(),
                Forms\Components\TextInput::make('FCADDR1')
                    ->required(),
                Forms\Components\TextInput::make('FCADDR2')
                    ->required(),
                Forms\Components\TextInput::make('FCTEL')
                    ->required(),
                Forms\Components\TextInput::make('FCFAX')
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
                Tables\Columns\TextColumn::make('FCNAME')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCTAXID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCADDR1')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCADDR2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCTEL')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCFAX')
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
            'index' => Pages\ListCorps::route('/'),
            // 'create' => Pages\CreateCorp::route('/create'),
            // 'edit' => Pages\EditCorp::route('/{record}/edit'),
        ];
    }
}
