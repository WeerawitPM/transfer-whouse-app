<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransferBookMenuResource\Pages;
use App\Models\TransferBook;
use App\Models\TransferRefType;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter; // เพิ่มการนำเข้าฟิลเตอร์

class TransferBookMenuResource extends Resource
{
    protected static ?string $model = TransferBook::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Transfer Book Menu';
    protected static ?string $breadcrumb = 'Transfer Book Menu';
    protected static ?string $label = 'Transfer Book Menu';
    protected static ?string $pluralLabel = 'Transfer Book Menu';
    protected static ?string $navigationGroup = 'WMS Transfer Book';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(TransferBook::query()->where('is_active', true)) // ใช้เมธอด query ตรงๆ
            ->columns([
                Tables\Columns\TextColumn::make('transfer_ref_type')
                    ->formatStateUsing(fn(TransferBook $record) => "{$record->transfer_ref_type->ref_type->FCCODE} {$record->transfer_ref_type->ref_type->FCNAME}")
                    ->sortable(),
                Tables\Columns\TextColumn::make('book')
                    ->formatStateUsing(fn(TransferBook $record) => "{$record->book->FCCODE} {$record->book->FCNAME}")
                    ->sortable(),
                Tables\Columns\TextColumn::make('book.from_whs')
                    ->label('From Warehouse')
                    ->formatStateUsing(fn(TransferBook $record) => $record->book->from_whs ? "{$record->book->from_whs->FCCODE} {$record->book->from_whs->FCNAME}" : 'N/A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('book.to_whs')
                    ->label('To Warehouse')
                    ->formatStateUsing(fn(TransferBook $record) => $record->book->to_whs ? "{$record->book->to_whs->FCCODE} {$record->book->to_whs->FCNAME}" : 'N/A')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('transfer_ref_type')
                    // ->relationship('transfer_ref_type', 'id')
                    ->options(
                        TransferRefType::with('ref_type')
                            ->get()
                            ->mapWithKeys(function ($transfer_ref_type) {
                                $refType = $transfer_ref_type->ref_type;
                                return [$transfer_ref_type->id => "{$refType->FCCODE} {$refType->FCNAME}"];
                            })
                    )
                    ->searchable()
                    ->attribute('transfer_ref_type_id')
            ])
            ->actions([
                Tables\Actions\Action::make('scan_tag')
                    ->label('Scan Tag')
                    ->url(fn(TransferBook $record): string => self::getUrl('scan_tag', ['record' => $record]))
                    ->visible(fn(TransferBook $record) => $record->transfer_ref_type->ref_type->FCCODE != 'WR'),
                Tables\Actions\Action::make('manual')
                    ->label('Manual')
                    ->url(fn(TransferBook $record): string => self::getUrl('manual', ['record' => $record]))
                    ->visible(fn(TransferBook $record) => $record->transfer_ref_type->ref_type->FCCODE != 'WR'),
                Tables\Actions\Action::make('wr_detail')
                    ->label('Detail')
                    ->url(fn(TransferBook $record): string => self::getUrl('wr_detail', ['record' => $record]))
                    ->visible(fn(TransferBook $record) => $record->transfer_ref_type->ref_type->FCCODE === 'WR'),
                // ->openUrlInNewTab()
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
            'index' => Pages\ListTransferBookMenus::route('/'),
            'wr_detail' => Pages\WrDetail::route('/{record}/wr_detail'),
            'wr_print' => Pages\WrPrint::route('/{record}/wr_print'),
            'scan_tag' => Pages\ScanTag::route('/{record}/scan_tag'),
            'manual' => Pages\Manual::route('/{record}/manual'),
        ];
    }
}
