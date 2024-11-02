<?php

namespace App\Filament\Resources\TransferBookMenuResource\Pages;

use App\Filament\Resources\TransferBookMenuResource;
use App\Models\JobToTag;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Route;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\ButtonAction;
use Filament\Notifications\Notification;

class ScanTag extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string $resource = TransferBookMenuResource::class;
    protected static string $view = 'filament.resources.transfer-book-menu-resource.pages.scan-tag';
    public $id;
    public $input_qr_code;
    public $qr_code_array;

    public function mount()
    {
        $this->id = Route::current()->parameter('record'); // Get the ID from the route parameters
        $this->input_qr_code = ''; // Initialize input_qr_code
    }

    protected function getActions(): array
    {
        return [
            ButtonAction::make('btn_save')
                ->label('Save')
                ->color('primary')
                ->action('save')
            // ->icon('heroicon-o-cloud'),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('input_qr_code')
                ->label('Scan Tag')
                ->suffixIcon('heroicon-m-magnifying-glass')
        ];
    }

    public function handleQrCodeInput($state)
    {
        $this->input_qr_code = $state;

        $results = JobToTag::query()->where('qr_code', $state)->get();
        if ($results->isNotEmpty()) {
            $this->qr_code_array[] = $state;
        } else {
            Notification::make()
                ->title('ไม่พบ Tag')
                ->warning()
                ->send();
        }

        $this->resetTable();
    }

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->query(
                JobToTag::query()
                    // ->where('qr_code', $this->input_qr_code)
                    ->whereIn('qr_code', $this->qr_code_array ?? [])
            )
            ->columns([
                ImageColumn::make('image')
                    ->label('image'),
                TextColumn::make('qr_code')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('part_no')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('part_code')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('part_name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('model')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('qty')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('packing_name')
                    ->searchable(),
                TextColumn::make('from_whs')
                    ->searchable(),
                TextColumn::make('to_whs')
                    ->searchable(),
            ])
            ->filters([
                // Add filters if needed
            ])
            ->actions([
                // Define row actions if needed
            ])
            ->bulkActions([
                // Define bulk actions if needed
            ]);
    }

    public function save()
    {
        $data = $this->getTableRecords();
        dd($data);
    }
}
