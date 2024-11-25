<?php

namespace App\Filament\Resources\TransferBookMenuResource\Pages;

use App\Filament\Resources\TransferBookMenuResource;
use App\Models\FormulaStockProd;
use App\Models\Sect;
use App\Models\SetupTag;
use App\Models\TransferBook;
use Filament\Forms\Components\Grid;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\ButtonAction;
use Auth;
use App\Filament\Resources\TransferBookMenuResource\Functions\handleSaveProduct;
use App\Filament\Resources\TransferBookMenuResource\Functions\handleSaveWrProduct;
use Filament\Forms\Components\Select;

class Manual extends Page
{
    protected static string $resource = TransferBookMenuResource::class;
    protected static string $view = 'filament.resources.transfer-book-menu-resource.pages.manual';
    protected static ?string $label = 'Add Item';
    protected static ?string $navigationLabel = 'Add Item';
    protected static ?string $breadcrumb = 'Add Item';
    protected static ?string $title = 'Add Item';


    public $id;
    public $fc_type;
    public $cpart_no = "Hello World!"; // ตัวแปรที่จะเก็บค่า CPART_NO ที่เลือก
    public $packing;
    public $book;
    public $sections;
    public $user;
    public $products;

    public function mount()
    {
        $this->id = Route::current()->parameter('record'); // Get the ID from the route parameters
        $this->book = TransferBook::where('id', $this->id)->get()->first()->book;
        $this->sections = Sect::all()->toArray();
        $this->user = Auth::user();
        $this->fc_type = "";
        $this->products = [];
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
        $this->products = FormulaStockProd::getProduct($this->fc_type, $this->cpart_no ?? "")->limit(10)->get()->toArray();
        // dd($this->products);
        $this->packing = SetupTag::all()->keyBy('FCSKID'); // สร้างคีย์ตาม FCSKID
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
        dd($data, $section);
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
