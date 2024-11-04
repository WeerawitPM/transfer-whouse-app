<?php

namespace App\Filament\Resources\TransferBookMenuResource\Pages;

use App\Filament\Resources\TransferBookMenuResource;
use App\Models\JobDetail;
use App\Models\JobHead;
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

    public function handleSave()
    {
        // ดึงข้อมูลจาก JobToTag และ JobDetail
        $jobToTag = $this->getTableRecords()->toArray();
        $job_id = $jobToTag[0]['job_id'];
        $jobDetail = JobDetail::query()->where('job_id', $job_id)->get()->toArray();

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
            $this->handleUpdateJobHead($job_id);
            $this->handleUpdateJobToTag($jobToTag);
            $this->handleUpdateJobDetail($jobDetail);
            Notification::make()
                ->title('Scan tag ครบแล้ว')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Scan tag ไม่ครบ')
                ->warning()
                ->send();
        }

        // Debug ข้อมูล
        // dd($jobToTag, $jobDetail, $jobToTagQuantities, $isComplete);
    }

    public function handleUpdateJobHead($job_id)
    {
        JobHead::where('id', $job_id)->update(['status' => 1]);
        dd($job_id, JobHead::where('id', $job_id)->first()->get());
    }

    public function handleUpdateJobToTag($jobToTag)
    {
        foreach ($jobToTag as $tag) {
            JobToTag::where('id', $tag['id'])->update(['status' => 1]);
        }
    }

    public function handleUpdateJobDetail($jobDetail)
    {
        foreach ($jobDetail as $tag) {
            JobDetail::where('id', $tag['id'])->update(['status' => 1]);
        }
    }
}
