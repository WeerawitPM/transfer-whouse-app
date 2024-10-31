<?php

namespace App\Filament\Resources\TransferBookMenuResource\Pages;

use App\Filament\Resources\TransferBookMenuResource;
use App\Models\VcstTrackDetail;
use Filament\Resources\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;

class WrPrint extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string $resource = TransferBookMenuResource::class;

    protected static string $view = 'filament.resources.transfer-book-menu-resource.pages.wr-print';

    public $job_no;
    public $part_no;

    protected function getTableData()
    {
        // If start and end dates are set, filter the query; otherwise, return an empty collection or a default query.
        if ($this->job_no && $this->part_no) {
            return VcstTrackDetail::getTrackDetail($this->job_no, $this->part_no);
        }

        return VcstTrackDetail::getTrackDetail('JOB202410/1690', '5T078-6310-7');
        // return VcstTrack::query()
        //     //return null
        //     ->where('JOB_NO', 'Hello World');
    }

    public function table(Table $table): Table
    {
        // dd($this->id);
        return $table
            ->query(fn() => $this->getTableData())
            ->columns([
                TextColumn::make('KANBAN')
                    ->sortable(),
                TextColumn::make('PART_NO')
                    ->sortable(),
                TextColumn::make('PART_CODE')
                    ->sortable(),
                TextColumn::make('PART_NAME')
                    ->sortable(),
                TextColumn::make('MODEL')
                    ->sortable(),
                TextColumn::make('PICTURE')
                    ->sortable(),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // Action::make('wr_print')
                //     ->label('Detail')
                //     ->icon('heroicon-o-eye')
                //     ->url(fn(VcstTrack $record): string => TransferBookMenuResource::getUrl(
                //         'wr_print',
                //         [
                //             'record' => $this->id . '@' .
                //                 str_replace('/', '-', $record->JOB_NO) . "@" .
                //                 $record->CPART_NO
                //         ]
                //     )),
            ])
            ->bulkActions([
                // ...
            ]);
    }
}
