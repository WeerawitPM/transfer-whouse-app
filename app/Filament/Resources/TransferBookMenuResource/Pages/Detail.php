<?php

namespace App\Filament\Resources\TransferBookMenuResource\Pages;

use App\Filament\Resources\TransferBookMenuResource;
use App\Models\Fccode_Glref;
use App\Models\TransferBook;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\JobHead;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Route;

class Detail extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string $resource = TransferBookMenuResource::class;

    protected static string $view = 'filament.resources.transfer-book-menu-resource.pages.detail';

    public $startDate;
    public $endDate;
    public $id;
    public $book;

    public function mount()
    {
        $this->id = Route::current()->parameter('record'); // Get the ID from the route parameters
        $this->book = TransferBook::where('id', $this->id)->get()->first()->book;
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

    protected function getTableData()
    {
        // ดึง `job_no` ทั้งหมดจาก `JobHead`
        $jobNosInJobHead = JobHead::all()->pluck('job_no')->toArray();

        // If start and end dates are set, filter the query; otherwise, return an empty collection or a default query.
        if ($this->startDate && $this->endDate) {
            return Fccode_Glref::get_job($this->book->FCSKID, $this->startDate, $this->endDate)
                ->whereNotIn('FCREFNO', $jobNosInJobHead);
        }

        // // return VcstTrack::getTrack('2024-10-29', '2024-10-31');
        return Fccode_Glref::query()
            //return null
            ->where('FCSKID', 'Hello World');
    }

    public function table(Table $table): Table
    {
        // dd($this->id);
        return $table
            ->defaultSort('DOC_NO', 'DESC')
            ->query(fn() => $this->getTableData())
            ->columns([
                TextColumn::make('FCSKID')
                    ->label('FCSKID')
                    ->hidden(true)
                    ->sortable(),
                TextColumn::make('DOC_NO')
                    ->label('Doc No')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('REF_NO')
                    ->label('Ref No')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('FDDATE')->date('Y-m-d')
                    ->sortable()
                    ->label("Date"),
                TextColumn::make('FROM_WHS')
                    ->label('From whouse'),
                TextColumn::make('TO_WHS')
                    ->label('From whouse'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                Action::make('detail_print')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->url(fn(Fccode_Glref $record): string => TransferBookMenuResource::getUrl(
                        'detail_print',
                        [
                            'record' => $this->id . '@' .
                                str_replace('/', '-', $record->REF_NO)
                        ]
                    ))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
            ]);
    }
}
