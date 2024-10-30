<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransferBookResource\Pages;
use App\Models\TransferBook;
use App\Models\TransferRefType;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;

class TransferBookResource extends Resource
{
    protected static ?string $model = TransferBook::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Setup Book';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('transfer_ref_type_id')
                    ->label('Transfer Ref Type ID')
                    ->options(
                        TransferRefType::with('ref_type')
                            ->get()
                            ->mapWithKeys(function ($transferRefType) {
                                $refType = $transferRefType->ref_type;
                                return [$transferRefType->id => "{$refType->FCCODE} {$refType->FCNAME}"];
                            })
                    )
                    ->searchable()
                    ->required()
                    ->reactive() // ทำให้เป็น reactive เพื่อให้เรียกใช้การเปลี่ยนแปลง
                    ->afterStateUpdated(function (callable $set) {
                        $set('book_id', null); // รีเซ็ต book_id เมื่อ transfer_ref_type_id เปลี่ยน
                    }),
                Select::make('book_id')
                    ->label('Book')
                    // ->relationship('book')
                    // ->getOptionLabelFromRecordUsing(fn(Book $record) => "{$record->FCCODE} {$record->FCNAME}")
                    ->options(function (callable $get) {
                        $transferRefTypeId = $get('transfer_ref_type_id');
                        // หาก transfer_ref_type_id ไม่ว่างให้กรอง Book
                        if ($transferRefTypeId) {
                            $refTypeCode = TransferRefType::find($transferRefTypeId)->ref_type->FCCODE;
                            return Book::where('FCREFTYPE', $refTypeCode)
                                ->get()
                                ->mapWithKeys(function (Book $book) {
                                    return [$book->id => "{$book->FCCODE} {$book->FCNAME}"];
                                });
                        }
                        return []; // หากไม่มี transfer_ref_type_id ให้แสดงเป็นว่าง
                    })
                    ->searchable()
                    ->required(),
                Forms\Components\Toggle::make('is_active')
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
                Tables\Columns\TextColumn::make('transfer_ref_type')
                    ->formatStateUsing(fn(TransferBook $record) => "{$record->transfer_ref_type->ref_type->FCCODE} {$record->transfer_ref_type->ref_type->FCNAME}")
                    ->sortable(),
                Tables\Columns\TextColumn::make('book')
                    ->formatStateUsing(fn(TransferBook $record) => "{$record->book->FCCODE} {$record->book->FCNAME}")
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
            'index' => Pages\ListTransferBooks::route('/'),
            // 'create' => Pages\CreateTransferBook::route('/create'),
            // 'edit' => Pages\EditTransferBook::route('/{record}/edit'),
        ];
    }
}
