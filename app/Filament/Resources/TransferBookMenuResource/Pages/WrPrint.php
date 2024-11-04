<?php

namespace App\Filament\Resources\TransferBookMenuResource\Pages;

use App\Filament\Resources\TransferBookMenuResource;
use App\Models\JobDetail;
use App\Models\JobToTag;
use App\Models\SetupTag;
use App\Models\VcstTrackDetail;
use App\Models\JobHead;
use App\Models\TransferBook;
use Filament\Resources\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Route;
use Filament\Pages\Actions\ButtonAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Notifications\Notification;
use Auth;

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
        $job_head = JobHead::query()->where('job_no', $this->job_no)->first();
        if ($job_head) {
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
        } else {
            return [
                ButtonAction::make('generate_document')
                    ->label('Generate Document')
                    ->color('primary')
                    ->icon('heroicon-o-printer')
                    // ->action(function () {
                    //     $this->generate_document();
                    // })
                    ->extraAttributes([
                        'id' => 'generate_document',
                        'onclick' => 'disableButton()',
                    ])
            ];
        }
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
                    ->searchable()
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
                TextColumn::make('qty')
                    ->label('Quantity')
                    ->getStateUsing(function ($record) {
                        $data = explode(',', $record->KANBAN);
                        return isset($data[1]) ? $data[1] : 'N/A';
                    }),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                // ...
            ])
            ->striped();
    }

    public function generate_document()
    {
        $from_whs_get = TransferBook::query()
            ->where('id', $this->transfer_book_id)
            ->with('book.from_whs')
            ->first();
        $from_whs = $from_whs_get->book->from_whs->FCCODE ?? null;

        $to_whs_get = TransferBook::query()
            ->where('id', $this->transfer_book_id)
            ->with('book.to_whs')
            ->first();
        $to_whs = $to_whs_get->book->to_whs->FCCODE ?? null;

        $whouse = '';
        if ($to_whs == 'XXX') {
            $whouse = '005';
        }

        $user_id = Auth::user()->id;
        $department = Auth::user()->dept->FCNAME;
        $created_date = date('Y-m-d');

        $jobHead = JobHead::firstOrCreate(
            ['job_no' => $this->job_no],
            [
                'doc_no' => $this->job_no,
                'doc_ref_no' => $this->job_no,
                'department' => $department,
                'from_whs' => $from_whs,
                'to_whs' => $to_whs,
                'status' => 0,
                'created_date' => $created_date,
                'user_id' => $user_id,
            ]
        );
        $jobHead->save();

        $this->saveJobToTag($jobHead->id, $from_whs, $to_whs, $whouse, $user_id, $created_date);
        $this->saveJobDetail($jobHead->id, $from_whs, $to_whs, $whouse, $user_id, $created_date);

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();

        $url = $this->getUrl([$this->id]);
        // dd($url);

        return redirect($url);
    }

    public function saveJobToTag($job_id, $from_whs, $to_whs, $whouse, $user_id, $created_date)
    {
        $data = $this->getTableRecords()->toArray();

        foreach ($data as $item) {
            $setupTag = SetupTag::where('FCCODE', $item['CPART_NO'])->first();
            if ($setupTag && $setupTag->image) {
                $item['image'] = asset('storage/' . $setupTag->image);
            } else {
                $item['image'] = asset('storage/image_part/error.jpg');
            }
            $kanban = explode(',', $item['KANBAN']);
            $qty = isset($kanban[1]) ? $kanban[1] : 0;
            $packing_name = isset($kanban[2]) ? $kanban[2] : 0;

            // dd($item);
            $jobToTag = JobToTag::create([
                'image' => $item['image'],
                'kanban' => $item['KANBAN'],
                'part_no' => $item['CPART_NO'],
                'part_code' => $item['FCSNAME'],
                'part_name' => $item['FCNAME'],
                'model' => $item['CMODEL'],
                'qty' => $qty,
                'packing_name' => $packing_name,
                'whouse' => $whouse,
                'from_whs' => $from_whs,
                'to_whs' => $to_whs,
                'status' => 0,
                'job_id' => $job_id,
                'created_date' => $created_date,
                'user_id' => $user_id,
            ]);
            $qr_code = $jobToTag->part_no . '@' . $jobToTag->qty . '@' . $jobToTag->packing_name . '@' . $jobToTag->whouse . '@' . $jobToTag->id;
            $jobToTag->qr_code = $qr_code;
            $jobToTag->save();
        }
    }

    public function saveJobDetail($job_id, $from_whs, $to_whs, $whouse, $user_id, $created_date)
    {
        $data = JobToTag::where('job_id', $job_id)->get();
        $groupedData = $data->groupBy('part_no');

        foreach ($groupedData as $part_no => $items) {
            $image = $items->first()->image;
            $kanban = $items->first()->kanban;
            $part_code = $items->first()->part_code;
            $part_name = $items->first()->part_name;
            $model = $items->first()->model;
            $totalQty = $items->sum('qty');
            $packing_name = $items->first()->packing_name;

            JobDetail::create([
                'image' => $image,
                'kanban' => $kanban,
                'part_no' => $part_no,
                'part_code' => $part_code,
                'part_name' => $part_name,
                'model' => $model,
                'qty' => $totalQty,
                'packing_name' => $packing_name,
                'whouse' => $whouse,
                'from_whs' => $from_whs,
                'to_whs' => $to_whs,
                'status' => 0,
                'job_id' => $job_id,
                'created_date' => $created_date,
                'user_id' => $user_id,
            ]);
        }
    }
}
