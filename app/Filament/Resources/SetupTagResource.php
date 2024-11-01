<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SetupTagResource\Pages;
use App\Models\SetupTag;
use App\Models\FormulaProd;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SetupTagResource extends Resource
{
    protected static ?string $model = SetupTag::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Setup';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('FCSKID')
                    ->label('Formula Product')
                    ->required()
                    ->searchable()
                    ->getSearchResultsUsing(
                        fn(string $query) =>
                        FormulaProd::query()
                            ->where('FCCODE', 'like', "%{$query}%")
                            ->limit(50)
                            ->pluck('FCCODE', 'FCSKID')
                    )
                    ->reactive()
                    ->afterStateUpdated(fn($state, callable $set) => $state ? self::populateFormulaProdData($state, $set) : null),
                Forms\Components\TextInput::make('FCSKID')
                    ->required()
                    ->readOnly(),
                Forms\Components\TextInput::make('FCCODE')
                    ->required()
                    ->readOnly(),
                Forms\Components\TextInput::make('FCSNAME')
                    ->required()
                    ->readOnly(),
                Forms\Components\TextInput::make('FCNAME')
                    ->required()
                    ->readOnly(),
                Forms\Components\TextInput::make('packing_name')
                    // ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('packing_qty')
                    // ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('image')
                    ->directory('image_part')
                    ->image(),
                // ->required(),
            ]);
    }

    protected static function populateFormulaProdData(string $fcskid, callable $set)
    {
        $formulaProd = FormulaProd::find($fcskid);

        if ($formulaProd) {
            $set('FCCODE', trim($formulaProd->FCCODE));
            $set('FCSNAME', trim($formulaProd->FCSNAME));
            $set('FCNAME', trim($formulaProd->FCNAME));
        }
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
                Tables\Columns\TextColumn::make('FCSNAME')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCNAME')
                    ->searchable(),
                Tables\Columns\TextColumn::make('packing_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('packing_qty')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
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
            'index' => Pages\ListSetupTags::route('/'),
            // 'create' => Pages\CreateSetupTag::route('/create'),
            // 'edit' => Pages\EditSetupTag::route('/{record}/edit'),
        ];
    }
}
