<?php

namespace App\Filament\Resources\TransferBookMenuResource\Pages;

use App\Filament\Resources\TransferBookMenuResource;
use App\Models\SetupTag;
use App\Models\VcstTrackDetail;
use Filament\Resources\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Route;
use Filament\Pages\Actions\ButtonAction;
use Filament\Tables\Columns\ImageColumn;

class WrPrint extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string $resource = TransferBookMenuResource::class;

    protected static string $view = 'filament.resources.transfer-book-menu-resource.pages.wr-print';

    public $id; // Property to hold the ID
    public $transfer_book_id;
    public $job_no;
    public $part_no;


    // Override the mount method to access the request
    public function mount()
    {
        $this->id = Route::current()->parameter('record'); // Get the ID from the route parameters

        // แยกข้อมูลโดยใช้เครื่องหมาย @
        $parts = explode('@', $this->id);

        if (count($parts) === 3) {
            $this->transfer_book_id = $parts[0];

            // เปลี่ยนเครื่องหมาย "-" เป็น "/" ใน job_no
            $this->job_no = str_replace('-', '/', $parts[1]);

            $this->part_no = $parts[2];
        }

        // dd([
        //     'transfer_book_id' => $this->transfer_book_id,
        //     'job_no' => $this->job_no,
        //     'part_no' => $this->part_no
        // ]);
    }

    protected function getActions(): array
    {
        return [
            ButtonAction::make('print_document')
                ->label('Print Document')
                ->color('primary')
                ->icon('heroicon-o-printer'),
            ButtonAction::make('print_tag')
                ->label('Print Tag')
                ->color('primary')
                ->icon('heroicon-o-printer'),
        ];
    }

    protected function getTableData()
    {
        if ($this->job_no && $this->part_no) {
            return VcstTrackDetail::getTrackDetail($this->job_no, $this->part_no);
        }

        return VcstTrackDetail::getTrackDetail('Hello World', 'Hello World');
    }

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->query(fn() => $this->getTableData())
            ->columns([
                ImageColumn::make('image')
                    ->label('image')
                    ->getStateUsing(function ($record) {
                        // dd($record->CPART_NO);
                        $setupTag = SetupTag::where('FCCODE', $record->CPART_NO)->first();
                        // dd($setupTag);
                        if ($setupTag && $setupTag->image) {
                            return $setupTag->image;
                        }
                        return asset('storage/image_part/error.jpg');
                    }),
                TextColumn::make('KANBAN')
                    ->label('KANBAN')
                    ->sortable(),
                TextColumn::make('CPART_NO')
                    ->label('Part No')
                    ->sortable(),
                TextColumn::make('FCSNAME')
                    ->label('Part Code')
                    ->sortable(),
                TextColumn::make('FCNAME')
                    ->label('Part Name')
                    ->sortable(),
                TextColumn::make('CMODEL')
                    ->label('Model')
                    ->sortable(),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                // ...
            ]);
    }
}
