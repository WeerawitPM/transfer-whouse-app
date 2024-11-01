<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobToTagResource\Pages;
use App\Models\JobToTag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;

class JobToTagResource extends Resource
{
    protected static ?string $model = JobToTag::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Job';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('part_no')
                    ->maxLength(255),
                Forms\Components\TextInput::make('part_code')
                    ->maxLength(255),
                Forms\Components\TextInput::make('part_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('model')
                    ->maxLength(255),
                Forms\Components\TextInput::make('qty')
                    ->numeric(),
                Forms\Components\TextInput::make('whouse')
                    ->maxLength(255),
                Forms\Components\TextInput::make('from_whs')
                    ->maxLength(255),
                Forms\Components\TextInput::make('to_whs')
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->numeric(),
                Forms\Components\TextInput::make('remark')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('created_date'),
                Select::make('job_id')
                    ->relationship('job_head', 'job_no'),
                Select::make('user_id')
                    ->relationship('user', 'name'),
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
                Tables\Columns\TextColumn::make('part_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('part_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('part_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('qty')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('whouse')
                    ->searchable(),
                Tables\Columns\TextColumn::make('from_whs')
                    ->searchable(),
                Tables\Columns\TextColumn::make('to_whs')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('remark')
                    ->searchable(),
                Tables\Columns\TextColumn::make('job_head.job_no')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
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
            'index' => Pages\ListJobToTags::route('/'),
            // 'create' => Pages\CreateJobToTag::route('/create'),
            // 'edit' => Pages\EditJobToTag::route('/{record}/edit'),
        ];
    }
}
