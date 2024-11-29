<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeptResource\Pages;
use App\Filament\Resources\DeptResource\RelationManagers;
use App\Models\Dept;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeptResource extends Resource
{
    protected static ?string $model = Dept::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Formula';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('FCSKID')
                    ->required(),
                Forms\Components\TextInput::make('FCCORP')
                    ->required(),
                Forms\Components\TextInput::make('FCTYPE')
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
                Tables\Columns\TextColumn::make('FCTYPE')
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
            'index' => Pages\ListDepts::route('/'),
            // 'create' => Pages\CreateDept::route('/create'),
            // 'edit' => Pages\EditDept::route('/{record}/edit'),
        ];
    }
}
