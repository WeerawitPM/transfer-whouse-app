<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SectResource\Pages;
use App\Filament\Resources\SectResource\RelationManagers;
use App\Models\Sect;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SectResource extends Resource
{
    protected static ?string $model = Sect::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?string $navigationGroup = 'Formula';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('FCSKID')
                    ->required(),
                Forms\Components\TextInput::make('FCCORP')
                    ->required(),
                Forms\Components\TextInput::make('FCDEPT')
                    ->required(),
                Forms\Components\TextInput::make('FCCODE')
                    ->required(),
                Forms\Components\TextInput::make('FCNAME')
                    ->required(),
                Forms\Components\TextInput::make('FCNAME2')
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
                Tables\Columns\TextColumn::make('FCCORP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCDEPT')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCCODE')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCNAME')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCNAME2')
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
            'index' => Pages\ListSects::route('/'),
            // 'create' => Pages\CreateSect::route('/create'),
            // 'edit' => Pages\EditSect::route('/{record}/edit'),
        ];
    }
}
