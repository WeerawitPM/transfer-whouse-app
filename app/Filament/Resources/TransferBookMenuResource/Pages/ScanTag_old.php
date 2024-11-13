<?php

namespace App\Filament\Resources\TransferBookMenuResource\Pages;

use App\Filament\Resources\TransferBookMenuResource;
use App\Filament\Resources\TransferBookMenuResource\Functions\handleJob;
use App\Models\JobToTag;
use App\Models\TransferBook;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Route;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\ButtonAction;
use Filament\Notifications\Notification;
use Auth;
use App\Filament\Resources\TransferBookMenuResource\Functions\handleSaveProduct;
use App\Filament\Resources\TransferBookMenuResource\Functions\handleSaveWrProduct;


class ScanTag_old extends Page implements HasTable
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
                ->action('handleSave')
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

        if ($this->input_qr_code == '') {
            return;
        }

        $tag = JobToTag::where('qr_code', $state)->get()->first();
        // dd($tag->part_no);
        if ($tag) {
            if ($tag->status == 1) {
                Notification::make()
                    ->title('Tag นี้ถูก scan ไปแล้ว')
                    ->warning()
                    ->color('warning')
                    ->send();
                return;
            } else {
                $this->qr_code_array[] = $state;
                $this->resetTable();
                return;
            }
        } else {
            Notification::make()
                ->title('ไม่พบ Tag')
                ->warning()
                ->color('warning')
                ->send();
            return;
        }
    }

    public function table(Table $table): Table
    {
        $query = JobToTag::query()->whereIn('qr_code', $this->qr_code_array ?? []);
        $partNoOptions = $query->pluck('part_no', 'part_no')->toArray();

        return $table
            ->paginated(false)
            ->defaultSort('qr_code', 'desc')
            ->query(
                $query,
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
                SelectFilter::make('part_no')
                    ->label('Part No')
                    ->options($partNoOptions),
            ])
            ->actions([
                Action::make('Delete')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->button()
                    ->action(fn($record) => $this->handleDelete($record->qr_code))
            ])
            ->bulkActions([
                // Define bulk actions if needed
            ]);
    }

    public function handleDelete($qr_code)
    {
        // Remove the selected QR code from qr_code_array
        $this->qr_code_array = array_filter(
            $this->qr_code_array,
            fn($item) => $item !== $qr_code
        );
        // dd($qr_code);
        $this->resetTable();
    }

    public function handleSave()
    {
        // ดึงข้อมูลจาก JobToTag
        $jobToTag = $this->getTableRecords()->toArray();

        // สร้าง array เพื่อเก็บจำนวนรวมของแต่ละ part_no ใน JobToTag
        $jobDetail = [];
        foreach ($jobToTag as $tag) {
            $partNo = $tag['part_no'];

            // ถ้า part_no นี้มีอยู่ใน jobDetail แล้ว ให้รวม qty เข้าด้วยกัน
            if (isset($jobDetail[$partNo])) {
                $jobDetail[$partNo]['qty'] += $tag['qty'];
            } else {
                // ถ้ายังไม่มี part_no นี้ใน jobDetail ให้เพิ่มเข้าไป
                $jobDetail[$partNo] = $tag;
            }
        }

        // แปลง jobDetail กลับเป็น array เพื่อให้ง่ายต่อการใช้งาน
        $jobDetail = array_values($jobDetail);

        // dd($jobDetail);

        $user = Auth::user();
        $book = TransferBook::where('id', $this->id)->get()->first()->book;
        $remark = "Scan";
        handleSaveProduct::handleSaveProduct($jobDetail, $book, $user, $remark);
        handleSaveWrProduct::handleSaveWrProduct($jobDetail, $user, $remark);
        // handleJob::handleUpdateJobHead($job_id);
        handleJob::handleUpdateJobToTag($jobToTag);
        // handleJob::handleUpdateJobDetail($jobDetail);
        Notification::make()
            ->title('Scan tag ครบแล้ว')
            ->success()
            ->color('success')
            ->send();
        $this->qr_code_array = [];
        $this->resetTable();

        // Debug ข้อมูล
        // dd($jobToTag, $jobDetail, $jobToTagQuantities, $isComplete);
    }
}
