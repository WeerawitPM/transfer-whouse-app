<?php

namespace App\Filament\Resources\TransferBookMenuResource\Pages;

use App\Filament\Resources\TransferBookMenuResource;
use App\Models\FormulaStockProd;
use App\Models\Sect;
use App\Models\SetupTag;
use App\Models\TransferBook;
use Filament\Forms\Components\Grid;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextInputColumn;
use Illuminate\Support\Facades\DB;
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
use Filament\Forms\Components\Select;

class Manual_old extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string $resource = TransferBookMenuResource::class;
    protected static string $view = 'filament.resources.transfer-book-menu-resource.pages.manual';
    protected static ?string $label = 'Add Item';
    protected static ?string $navigationLabel = 'Add Item';
    protected static ?string $breadcrumb = 'Add Item';
    protected static ?string $title = 'Add Item';


    public $id;
    public $fc_type;
    public $cpart_no = "Hello World!"; // ตัวแปรที่จะเก็บค่า CPART_NO ที่เลือก
    public $part_selected;
    public $packing;
    public $book;
    public $sections;
    public $user;

    public function mount()
    {
        $this->id = Route::current()->parameter('record'); // Get the ID from the route parameters
        $this->part_selected = [];
        $this->book = TransferBook::where('id', $this->id)->get()->first()->book;
        $this->sections = Sect::all()->toArray();
        $this->user = Auth::user();
        $this->fc_type = "";
        // dd($this->sections);
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
        $product_type = DB::connection('formula')->table('PRODTYPE')
            ->selectRaw('FCCODE, LTRIM(RTRIM(FCNAME)) AS FCNAME')
            ->get()
            ->pluck('FCNAME', 'FCCODE') // แปลงข้อมูลเป็น ['FCCODE' => 'FCNAME']
            ->toArray(); // แปลงเป็น array
        // dd($product_type);
        return [
            Grid::make(2) // แบ่งฟอร์มออกเป็น 2 คอลัมน์
                ->schema([
                    Select::make('product_type')
                        ->label('Product Type')
                        ->options($product_type) // ใส่ข้อมูลที่แปลงแล้วใน options
                        ->placeholder('Select a product type'),
                    TextInput::make('input_search_part')
                        ->label('Search')
                        ->suffixIcon('heroicon-m-magnifying-glass'),
                ]),
        ];
    }

    public function handleSearchPart($state, $product_type)
    {
        $this->cpart_no = $state;
        $this->fc_type = $product_type;
        $this->resetTable();
        $this->packing = SetupTag::all()->keyBy('FCSKID'); // สร้างคีย์ตาม FCSKID
    }


    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->query(
                // FormulaStockProd::query()
                //     ->selectRaw('TOP 10 FCSKID, FCCODE, FCSNAME, FCNAME')
                fn() => (
                    // dd(FormulaStockProd::getProduct('1', '5T')->get())
                    FormulaStockProd::getProduct($this->fc_type, $this->cpart_no ?? "")->limit(10)
                )
            )
            ->columns([
                TextColumn::make('FCSKID')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('CPART_NO')
                    ->label('Part No')
                    ->searchable(
                        "PROD.FCCODE"
                    ),
                TextColumn::make('CCODE')
                    ->label('Part Code'),
                TextColumn::make('CPART_NAME')
                    ->label('Part Name'),
                TextColumn::make('MODEL')
                    ->label('Model')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('SMODEL')
                    ->label('SModel')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('STOCKQTY')
                    ->label('Stock Qty')
                    ->numeric(),
                // TextInputColumn::make('QTY')
                //     ->label('Qty')
                //     ->type('integer')
                //     ->getStateUsing(fn ($record) => 0) // กำหนดค่าเริ่มต้น
            ])
            ->filters([
                // Add filters if needed
            ])
            ->actions([
                Action::make('Add')
                    ->button()
                    ->action(function ($record) {
                        // ตรวจสอบ stockqty ห้ามน้อยกว่าหรือ = 0
                        if ($record->STOCKQTY <= 0) {
                            $this->handleNotification('เกิดข้อผิดพลาด', 'ห้ามเลือกรายการที่ stockqty เป็น 0', 'danger');
                            return;
                        }

                        // ตรวจสอบและแจ้งเตือนในกรณีที่เลือกข้อมูลเดิม
                        if (collect($this->part_selected)->contains('FCSKID', $record->FCSKID)) {
                            $this->handleNotification('เกิดข้อผิดพลาด', 'ข้อมูลนี้มีอยู่ในตารางแล้ว', 'warning');
                            return;
                        }

                        // เพิ่มข้อมูลลงใน part_selected
                        $this->part_selected[] = $record->toArray();
                        $this->handleNotification('สำเร็จ', 'เพิ่มข้อมูลเรียบร้อยแล้ว', 'success');
                    })
                    ->icon('heroicon-o-plus') // ไอคอนของปุ่ม
                    ->color('primary') // สีของปุ่ม
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
                        $this->handleNotification('สำเร็จ', 'เพิ่มข้อมูลเรียบร้อยแล้ว', 'success');
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

    public function handleConfirmSave($data, $section)
    {
        // dd($data, $section);
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
        handleSaveProduct::handleSaveProduct($data, $book, $user, $remark, $section);
        // handleSaveWrProduct::handleSaveWrProduct($data, $user, $remark);

        $this->handleNotification("แจ้งเตือน", "บันทึกข้อมูลสำเร็จ", "success");

        $url = $this->getUrl([$this->id]);
        return redirect($url);
    }
}
