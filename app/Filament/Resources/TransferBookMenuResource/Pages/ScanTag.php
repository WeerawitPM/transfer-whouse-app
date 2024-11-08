<?php

namespace App\Filament\Resources\TransferBookMenuResource\Pages;

use App\Filament\Resources\TransferBookMenuResource;
use App\Models\Book;
use App\Models\FormulaFormulas;
use App\Models\JobDetail;
use App\Models\JobHead;
use App\Models\JobToTag;
use App\Models\RefType;
use App\Models\TransferBook;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Route;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\ButtonAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Auth;


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
                if (!empty($this->qr_code_array)) {
                    $last_tag = JobToTag::where('qr_code', end($this->qr_code_array))->get()->first();
                    if ($tag->job_id == $last_tag->job_id) {
                        $this->qr_code_array[] = $state;
                    } else {
                        Notification::make()
                            ->title('ห้าม scan tag คนละ job กัน')
                            ->warning()
                            ->color('warning')
                            ->send();
                        return;
                    }
                } else {
                    $this->qr_code_array[] = $state;
                }
            }
        } else {
            Notification::make()
                ->title('ไม่พบ Tag')
                ->warning()
                ->color('warning')
                ->send();
            return;
        }

        $this->resetTable();
    }

    public function table(Table $table): Table
    {
        $query = JobToTag::query()->whereIn('qr_code', $this->qr_code_array ?? []);

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
                // Add filters if needed
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
        // ดึงข้อมูลจาก JobToTag และ JobDetail
        $jobToTag = $this->getTableRecords()->toArray();
        $job_id = $jobToTag[0]['job_id'];
        $jobDetail = JobDetail::where('job_id', $job_id)->get()->toArray();

        // สร้าง array เพื่อเก็บจำนวนรวมของแต่ละ part_no ใน JobToTag
        $jobToTagQuantities = [];
        foreach ($jobToTag as $tag) {
            $partNo = $tag['part_no'];
            $qty = $tag['qty'];

            // รวม qty สำหรับ part_no เดียวกัน
            if (isset($jobToTagQuantities[$partNo])) {
                $jobToTagQuantities[$partNo] += $qty;
            } else {
                $jobToTagQuantities[$partNo] = $qty;
            }
        }

        // ตรวจสอบว่า qty ใน JobDetails ตรงกับ qty รวมใน JobToTag หรือไม่
        $isComplete = true;
        foreach ($jobDetail as $detail) {
            $partNo = $detail['part_no'];
            $requiredQty = $detail['qty'];

            // ตรวจสอบว่ามีจำนวนรวมที่ตรงกับ JobDetails หรือไม่
            if (!isset($jobToTagQuantities[$partNo]) || $jobToTagQuantities[$partNo] != $requiredQty) {
                $isComplete = false;
                break;
            }
        }

        // แสดงผลลัพธ์การตรวจสอบ
        if ($isComplete) {
            $user = Auth::user();
            $book = TransferBook::where('id', $this->id)->get()->first()->book;
            handleSaveProduct($jobDetail, $book, $user);
            hadleSaveWrProduct($jobDetail, $user);
            handleUpdateJobHead($job_id);
            handleUpdateJobToTag($jobToTag);
            handleUpdateJobDetail($jobDetail);
            Notification::make()
                ->title('Scan tag ครบแล้ว')
                ->success()
                ->color('success')
                ->send();
            $this->qr_code_array = [];
            $this->resetTable();
        } else {
            Notification::make()
                ->title('Scan tag ไม่ครบ')
                ->warning()
                ->color('warning')
                ->send();
        }

        // Debug ข้อมูล
        // dd($jobToTag, $jobDetail, $jobToTagQuantities, $isComplete);
    }
}
