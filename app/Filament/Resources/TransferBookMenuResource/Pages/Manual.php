<?php

namespace App\Filament\Resources\TransferBookMenuResource\Pages;

use App\Filament\Resources\TransferBookMenuResource;
use App\Models\FormulaStockProd;
use App\Models\SetupTag;
use App\Models\TransferBook;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Route;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\ButtonAction;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Auth;
use App\Filament\Resources\TransferBookMenuResource\Functions\handleSaveProduct;
use App\Filament\Resources\TransferBookMenuResource\Functions\handleSaveWrProduct;

class Manual extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string $resource = TransferBookMenuResource::class;
    protected static string $view = 'filament.resources.transfer-book-menu-resource.pages.manual';
    protected static ?string $label = 'Add Item';
    protected static ?string $navigationLabel = 'Add Item';
    protected static ?string $breadcrumb = 'Add Item';
    protected static ?string $title = 'Add Item';


    public $id;
    public $fc_type = "1";
    public $cpart_no = "Hello World!"; // ตัวแปรที่จะเก็บค่า CPART_NO ที่เลือก
    public $part_selected;
    public $packing;

    public function mount()
    {
        $this->id = Route::current()->parameter('record'); // Get the ID from the route parameters
        $this->part_selected = [];
    }

    protected function getActions(): array
    {
        return [
            ButtonAction::make('btn_save')
                ->label('Save')
                ->color('primary')
                ->extraAttributes(
                    [
                        'id' => 'btn_save',
                        'onclick' => 'handleSave()',
                    ]
                ),
            // ->action('handleSave')
            // ->icon('heroicon-o-cloud'),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('input_search_part')
                ->label('Search')
                ->suffixIcon('heroicon-m-magnifying-glass')
        ];
    }

    public function handleSearchPart($state)
    {
        $this->cpart_no = $state;
        $this->resetTable();
        $this->packing = SetupTag::all()->keyBy('FCSKID'); // สร้างคีย์ตาม FCSKID
    }


    public function table(Table $table): Table
    {
        return $table
            ->query(
                // FormulaStockProd::query()
                //     ->selectRaw('TOP 100 FCSKID, FCCODE, FCSNAME, FCNAME')
                fn() => (
                    // dd(FormulaStockProd::getProduct('1', '5T')->get())
                    FormulaStockProd::getProduct($this->fc_type, $this->cpart_no ?? "")
                )
            )
            ->columns([
                TextColumn::make('FCSKID')
                    ->sortable(),
                TextColumn::make('CPART_NO')
                    ->label('Part No')
                    ->sortable()
                    ->searchable(
                        "PROD.FCCODE"
                    ),
                TextColumn::make('CCODE')
                    ->label('Part Code')
                    ->sortable(),
                TextColumn::make('CPART_NAME')
                    ->label('Part Name')
                    ->sortable(),
                TextColumn::make('MODEL')
                    ->label('Model')
                    ->sortable(),
                TextColumn::make('SMODEL')
                    ->label('SModel')
                    ->sortable(),
                TextColumn::make('STOCKQTY')
                    ->label('Stock Qty')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                // Add filters if needed
            ])
            ->actions([
                // Define row actions if needed
            ])
            ->bulkActions([
                BulkAction::make('Add')
                    ->action(function (Collection $records) {
                        // $this->part_selected = $records;
            
                        //ตรวจสอบ stockqty ห้ามน้อยกว่าหรือ = 0
                        $invalidRecords = $records->filter(fn($record) => $record->STOCKQTY <= 0);
                        if ($invalidRecords->isNotEmpty()) {
                            $this->handleNotification('เกิดข้อผิดพลาด', 'ห้ามเลือกรายการที่ stockqty เป็น 0', 'danger');
                        }

                        //ตรวจสอบและแจ้งเตือนไหนกรณีที่เลือกข้อมูลเดิม
                        $duplicateRecords = $records->filter(fn($record) => collect($this->part_selected)->contains('FCSKID', $record->FCSKID));
                        if ($duplicateRecords->isNotEmpty()) {
                            $this->handleNotification('เกิดข้อผิดพลาด', 'ข้อมูลบางรายการที่ท่านเลือก มีอยู่ในตารางแล้ว', 'warning');
                        }

                        // เพิ่มข้อมูลที่ไม่ซ้ำ
                        $newRecords = $records->reject(function ($record) {
                            return collect($this->part_selected)->contains('FCSKID', $record->FCSKID) || $record->STOCKQTY <= 0;
                        });

                        // บันทึกข้อมูลลง part_selected
                        $this->part_selected = array_merge($this->part_selected, $newRecords->toArray());
                    })
                    ->deselectRecordsAfterCompletion()
            ])
            ->striped();
    }

    public function removePart($index)
    {
        unset($this->part_selected[$index]);
        $this->part_selected = array_values($this->part_selected); // Re-index the array
    }

    public function handleNotification($title, $message, $status)
    {
        // กำหนด notification ตาม status ที่ได้รับ
        $notification = Notification::make()
            ->title($title)
            ->body($message);

        // ตรวจสอบสถานะ
        if ($status === 'success') {
            $notification->success();
        } elseif ($status === 'warning') {
            $notification->warning();
        } elseif ($status === 'danger') {
            $notification->danger();
        }

        // ส่ง notification
        $notification->send();
    }

    public function handleConfirmSave($data)
    {
        // dd($data);
        foreach ($data as $item) {
            // ค้นหาข้อมูลตาม FCSKID
            $setupTag = SetupTag::where('FCSKID', $item['FCSKID'])->first();

            if ($setupTag) {
                // ถ้าพบข้อมูล ให้ทำการอัปเดต
                $setupTag->update([
                    'FCCODE' => $item['part_no'],
                    'FCSNAME' => $item['FCSNAME'],
                    'FCNAME' => $item['FCNAME'],
                    'packing_qty' => $item['packing_qty'],
                ]);
            } else {
                // ถ้าไม่พบข้อมูล ให้สร้างใหม่
                SetupTag::create([
                    'FCSKID' => $item['FCSKID'],
                    'FCCODE' => $item['part_no'],
                    'FCSNAME' => $item['FCSNAME'],
                    'FCNAME' => $item['FCNAME'],
                    'packing_qty' => $item['packing_qty'],
                ]);
            }
        }
        // dd($data);

        $user = Auth::user();
        $book = TransferBook::where('id', $this->id)->get()->first()->book;
        $remark = "Manual";
        handleSaveProduct::handleSaveProduct($data, $book, $user, $remark);
        handleSaveWrProduct::handleSaveWrProduct($data, $user, $remark);

        $this->handleNotification("แจ้งเตือน", "บันทึกข้อมูลสำเร็จ", "success");

        $url = $this->getUrl([$this->id]);
        return redirect($url);
    }
}
