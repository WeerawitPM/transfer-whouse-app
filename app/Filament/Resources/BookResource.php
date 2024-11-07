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
                Forms\Components\TextInput::make('FCSKID'),
                Forms\Components\TextInput::make('FCREFTYPE'),
                Forms\Components\TextInput::make('FCCORP'),
                Forms\Components\TextInput::make('FCBRANCH'),
                Forms\Components\TextInput::make('FCCODE'),
                Forms\Components\TextInput::make('FCNAME'),
                Forms\Components\TextInput::make('FCNAME2'),
                Forms\Components\TextInput::make('FCACCBOOK'),
                Forms\Components\TextInput::make('FCWHOUSE'),
                Forms\Components\TextInput::make('FCPREFIX'),
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
                Tables\Columns\TextColumn::make('FCWHOUSE')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCPREFIX')
                    ->searchable(),
                Tables\Columns\TextColumn::make('from_whs')
                    ->formatStateUsing(fn(Book $record) => "{$record->from_whs->FCCODE} {$record->from_whs->FCNAME}")
                    ->sortable(),
                Tables\Columns\TextColumn::make('to_whs')
                    ->formatStateUsing(fn(Book $record) => "{$record->to_whs->FCCODE} {$record->to_whs->FCNAME}")
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
