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
                            Notification::make()
                                ->title('เกิดข้อผิดพลาด ห้ามเลือกรายการที่ stockqty เป็น 0')
                                ->body('ข้อมูลบางรายการที่ท่านเลือกมี stockqty = 0')
                                ->danger()
                                ->send();
                        }

                        //ตรวจสอบและแจ้งเตือนไหนกรณีที่เลือกข้อมูลเดิม
                        $duplicateRecords = $records->filter(fn($record) => collect($this->part_selected)->contains('FCSKID', $record->FCSKID));
                        if ($duplicateRecords->isNotEmpty()) {
                            Notification::make()
                                ->title('เกิดข้อผิดพลาด มีการเลือกข้อมูลเดิม')
                                ->body('ข้อมูลบางรายการที่ท่านเลือก มีอยู่ในตารางแล้ว')
                                ->warning()
                                ->send();
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
}
