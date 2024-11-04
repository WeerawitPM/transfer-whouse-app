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

    public function mount()
    {
        $this->id = Route::current()->parameter('record'); // Get the ID from the route parameters
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
                    ->action(
                        fn(Collection $records) =>
                        dd($records->each)
                    )
                    ->deselectRecordsAfterCompletion()
            ]);
    }
}
