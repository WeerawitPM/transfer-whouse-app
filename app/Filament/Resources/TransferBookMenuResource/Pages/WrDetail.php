<?php

namespace App\Filament\Resources\TransferBookMenuResource\Pages;

use App\Filament\Resources\TransferBookMenuResource;
// use Filament\Actions\Action;
use App\Models\Book;
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

class WrDetail extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string $resource = TransferBookMenuResource::class;

    protected static string $view = 'filament.resources.transfer-book-menu-resource.pages.wr-detail';

    public $startDate;
    public $endDate;

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

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('FCCODE'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                VcstTrack::query()
                    ->where('STEP', 1) // กรองเฉพาะข้อมูลที่ต้องการ
                    ->limit(1000) // จำกัดที่ 1000 รายการ
            )
            ->columns([
                TextColumn::make('KANBAN'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }

    // protected function getActions(): array
    // {
    //     return [
    //         ButtonAction::make('submit')
    //             ->label('Submit')
    //             ->action('submit')
    //             ->color('primary')
    //             ->icon('heroicon-o-check'),
    //     ];
    // }

    public function submit()
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ], [
            'endDate.after_or_equal' => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
        ]);

        // Add the logic to handle form submission
        dd($this->endDate);
    }
}
