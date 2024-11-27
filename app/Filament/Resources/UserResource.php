<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Corp;
use App\Models\Dept;
use App\Models\Emplr;
use App\Models\Sect;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)
                    ->schema([
                        Forms\Components\FileUpload::make('avatar_url')
                            ->alignCenter()
                            ->image()
                            ->avatar()
                            ->imageEditor()
                            ->disk(config('filament-edit-profile.disk', 'public'))
                            ->visibility(config('filament-edit-profile.visibility', 'public'))
                            ->directory(filament('filament-edit-profile')->getAvatarDirectory())
                            ->rules(filament('filament-edit-profile')->getAvatarRules()),
                    ]),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('email_verified_at'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $context): bool => $context === 'create'),
                Forms\Components\TextInput::make('first_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('emp_id')
                    ->numeric(),
                Select::make('corp_id')
                    ->searchable()
                    ->required()
                    ->options(Corp::all()
                        ->mapWithKeys(function (Corp $corp) {
                            return [$corp->id => "{$corp->FCCODE} {$corp->FCNAME}"];
                        }))
                    ->reactive()
                    ->afterStateUpdated(function (callable $set) {
                        $set('dept_id', null);
                    }),
                Select::make('dept_id')
                    ->label('Department')
                    ->options(function (callable $get) {
                        $corp_id = $get('corp_id');
                        if ($corp_id) {
                            $corp_fcskid = Corp::find($corp_id)->FCSKID;
                            return Dept::where('FCCORP', $corp_fcskid)
                                ->get()
                                ->mapWithKeys(function (Dept $item) {
                                    return [$item->id => "{$item->FCNAME2} {$item->FCNAME}"];
                                });
                        }
                        return [];
                    })
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set) {
                        $set('sect_id', null);
                    }),
                Select::make('sect_id')
                    ->label('Section')
                    ->options(function (callable $get) {
                        $dept_id = $get('dept_id');
                        if ($dept_id) {
                            $dept_fcskid = Dept::find($dept_id)->FCSKID;
                            return Sect::where('FCDEPT', $dept_fcskid)
                                ->get()
                                ->mapWithKeys(function (Sect $item) {
                                    return [$item->id => "{$item->FCCODE} {$item->FCNAME}"];
                                });
                        }
                        return [];
                    })
                    ->searchable()
                    ->required(),
                Select::make('emplr_id')
                    ->label('Emplr')
                    ->options(Emplr::all()->pluck('FCLOGIN', 'id'))
                    ->searchable(),
                Select::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('emp_id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('corp')
                    ->formatStateUsing(fn(User $record) => "{$record->corp->FCCODE} {$record->corp->FCNAME}")
                    ->sortable(),
                Tables\Columns\TextColumn::make('dept.FCNAME')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sect.FCNAME')
                    ->sortable(),
                Tables\Columns\TextColumn::make('emplr.FCLOGIN')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
