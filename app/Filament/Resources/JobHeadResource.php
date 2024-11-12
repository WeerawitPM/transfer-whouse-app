<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobHeadResource\Pages;
use App\Filament\Resources\JobHeadResource\RelationManagers;
use App\Models\JobHead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Auth;

class JobHeadResource extends Resource
{
    protected static ?string $model = JobHead::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Job';

    public static function shouldRegisterNavigation(): bool
    {
        // ตรวจสอบว่า user ที่ล็อกอินมี role เป็น 'user' หรือไม่
        return !Auth::user()->hasRole('user');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('job_no')
                    ->maxLength(255),
                Forms\Components\TextInput::make('doc_no')
                    ->maxLength(255),
                Forms\Components\TextInput::make('doc_ref_no')
                    ->maxLength(255),
                Forms\Components\TextInput::make('department')
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
                Tables\Columns\TextColumn::make('job_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('doc_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('doc_ref_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('department')
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
            'index' => Pages\ListJobHeads::route('/'),
            // 'create' => Pages\CreateJobHead::route('/create'),
            // 'edit' => Pages\EditJobHead::route('/{record}/edit'),
        ];
    }
}
