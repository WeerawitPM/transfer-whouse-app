<?php

namespace App\Filament\Resources\TransferBookMenuResource\Pages;

use App\Filament\Resources\TransferBookMenuResource;
// use Filament\Actions\Action;
use App\Models\VcstTrack;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\DatePicker;
// use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
// use Filament\Pages\Actions\ButtonAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Route; // Import Route facade

class WrDetail extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string $resource = TransferBookMenuResource::class;

    protected static string $view = 'filament.resources.transfer-book-menu-resource.pages.wr-detail';

    public $startDate;
    public $endDate;
    public $id; // Property to hold the ID

    // Override the mount method to access the request
    public function mount()
    {
        $this->id = Route::current()->parameter('record'); // Get the ID from the route parameters
    }


    protected function getFormSchema(): array
    {
        return [
            Grid::make()
                ->columns(2)
                ->schema([
                    DatePicker::make('startDate')
                        ->label('Start Date')
                        ->required(),
                    DatePicker::make('endDate')
                        ->label('End Date')
                        ->required(),
                ])
        ];
    }

    protected function getTableData()
    {
        // If start and end dates are set, filter the query; otherwise, return an empty collection or a default query.
        if ($this->startDate && $this->endDate) {
            return VcstTrack::getTrack($this->startDate, $this->endDate);
        }

        // return VcstTrack::getTrack('2024-10-29', '2024-10-31');
        return VcstTrack::query()
            //return null
            ->where('JOB_NO', 'Hello World');
    }

    public function table(Table $table): Table
    {
        // dd($this->id);
        return $table
            ->query(fn() => $this->getTableData())
            ->columns([
                TextColumn::make('JOB_NO')
                    ->sortable(),
                TextColumn::make('CPART_NO')
                    ->sortable(),
                TextColumn::make('FCSNAME')
                    ->sortable(),
                TextColumn::make('FCNAME')
                    ->sortable(),
                TextColumn::make('STARTDATE')->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('ENDDATE')->date('Y-m-d')
                    ->sortable(),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                Action::make('wr_print')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->url(fn(VcstTrack $record): string => TransferBookMenuResource::getUrl(
                        'wr_print',
                        [
                            'record' => $this->id . '@' .
                                str_replace('/', '-', $record->JOB_NO) . "@" .
                                $record->CPART_NO
                        ]
                    ))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public function submit()
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ], [
            'endDate.after_or_equal' => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
        ]);

        // Add the logic to handle form submission
        // dd($this->endDate);
    }
}
