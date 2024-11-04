<?php

namespace App\Filament\Resources\TransferBookMenuResource\Pages;

use App\Filament\Resources\TransferBookMenuResource;
use App\Models\FormulaStockProd;
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

class Manual2 extends Page implements HasTable
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
                ->action('handleSave')
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
    }


    public function table(Table $table): Table
    {
        return $table
            ->striped()
            ->query(
                // FormulaStockProd::query()
                //     ->selectRaw('TOP 100 FCSKID, FCCODE, FCSNAME, FCNAME')
                fn() => (
                    // dd(FormulaStockProd::getProduct('1', '5T')->get())
                    FormulaStockProd::getProduct($this->fc_type, $this->cpart_no)
                )
            )
            ->columns([
                TextColumn::make('FCSKID')
                    ->sortable(),
                TextColumn::make('CPART_NO')
                    ->sortable(),
                TextColumn::make('CCODE')
                    ->sortable(),
                TextColumn::make('CPART_NAME')
                    ->sortable(),
                TextColumn::make('MODEL')
                    ->sortable(),
                TextColumn::make('SMODEL')
                    ->sortable(),
                TextColumn::make('STOCKQTY')
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
                        $existingParts = session()->get('part_selected', []); // ดึงข้อมูลที่มีอยู่ใน session
                        $newParts = $records->toArray(); // แปลงข้อมูลใหม่เป็น array
            
                        // กรองข้อมูลใหม่ให้เฉพาะข้อมูลที่ยังไม่มีใน session
                        $filteredParts = array_filter($newParts, function ($newPart) use ($existingParts) {
                            foreach ($existingParts as $existingPart) {
                                if ($existingPart['FCSKID'] === $newPart['FCSKID']) {
                                    return false; // หากข้อมูลมีอยู่แล้ว ให้ข้าม
                                }
                            }
                            return true; // เพิ่มเฉพาะข้อมูลที่ยังไม่มีใน session
                        });

                        // รวมข้อมูลเก่าและข้อมูลใหม่ที่ผ่านการกรองแล้ว
                        $mergedParts = array_merge($existingParts, $filteredParts);

                        // บันทึกข้อมูลทั้งหมดกลับเข้าไปใน session
                        session()->put('part_selected', $mergedParts);
                    })
                    ->deselectRecordsAfterCompletion()
            ]);
    }
}
