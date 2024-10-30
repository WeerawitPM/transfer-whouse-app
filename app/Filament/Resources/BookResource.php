<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Models\Book;
use App\Models\Whouse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Formula';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('FCSKID')
                    ->required(),
                Forms\Components\TextInput::make('FCREFTYPE')
                    ->required(),
                Forms\Components\TextInput::make('FCCORP')
                    ->required(),
                Forms\Components\TextInput::make('FCBRANCH')
                    ->required(),
                Forms\Components\TextInput::make('FCCODE')
                    ->required(),
                Forms\Components\TextInput::make('FCNAME')
                    ->required(),
                Forms\Components\TextInput::make('FCNAME2')
                    ->required(),
                Forms\Components\TextInput::make('FCACCBOOK')
                    ->required(),
                Select::make('from_whs_id')
                    ->searchable()
                    ->options(Whouse::all()->mapWithKeys(function (Whouse $whouse) {
                        return [$whouse->id => "{$whouse->FCCODE} {$whouse->FCNAME}"];
                    })),
                Select::make('to_whs_id')
                    ->searchable()
                    ->options(Whouse::all()->mapWithKeys(function (Whouse $whouse) {
                        return [$whouse->id => "{$whouse->FCCODE} {$whouse->FCNAME}"];
                    })),
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
                Tables\Columns\TextColumn::make('FCREFTYPE')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCCORP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCBRANCH')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCCODE')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCNAME')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCNAME2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCACCBOOK')
                    ->searchable(),
                Tables\Columns\TextColumn::make('from_whs.FCCODE')
                    ->sortable(),
                Tables\Columns\TextColumn::make('to_whs.FCCODE')
                    ->sortable(),
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
            'index' => Pages\ListBooks::route('/'),
            // 'create' => Pages\CreateBook::route('/create'),
            // 'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
