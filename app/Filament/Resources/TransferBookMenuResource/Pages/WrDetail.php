<?php

namespace App\Filament\Resources\TransferBookMenuResource\Pages;

use App\Filament\Resources\TransferBookMenuResource;
// use Filament\Actions\Action;
use App\Filament\Resources\TransferBookMenuResource\Functions\printDocument;
use App\Filament\Resources\TransferBookMenuResource\Functions\printTag;
use App\Filament\Resources\TransferBookMenuResource\Functions\saveJob;
use App\Models\JobHead;
use App\Models\TransferBook;
use App\Models\VcstTrack;
use App\Models\VcstTrackDetail;
use Auth;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\DatePicker;
// use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
// use Filament\Pages\Actions\ButtonAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Route; // Import Route facade
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class WrDetail extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string $resource = TransferBookMenuResource::class;

    protected static string $view = 'filament.resources.transfer-book-menu-resource.pages.wr-detail';

    public $startDate;
    public $endDate;
    public $id; // Property to hold the ID

    // Override the mount method to access the request
    public function mount()
    {
        $this->id = Route::current()->parameter('record'); // Get the ID from the route parameters
    }


    protected function getFormSchema(): array
    {
        return [
            Grid::make()
                ->columns(2)
                ->schema([
                    DatePicker::make('startDate')
                        ->label('Start Date')
                        ->required(),
                    DatePicker::make('endDate')
                        ->label('End Date')
                        ->required(),
                ])
        ];
    }

    protected function getTableData()
    {
        // If start and end dates are set, filter the query; otherwise, return an empty collection or a default query.
        if ($this->startDate && $this->endDate) {
            return VcstTrack::getTrack($this->startDate, $this->endDate);
        }

        // // return VcstTrack::getTrack('2024-10-29', '2024-10-31');
        return VcstTrack::query()
            //return null
            ->where('JOB_NO', 'Hello World');
    }

    public function table(Table $table): Table
    {
        // dd($this->id);
        return $table
            ->defaultSort('JOB_NO', 'DESC')
            ->query(fn() => $this->getTableData())
            ->columns([
                TextColumn::make('JOB_NO')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('CPART_NO')
                    ->sortable(),
                TextColumn::make('FCSNAME')
                    ->sortable(),
                TextColumn::make('FCNAME')
                    ->sortable(),
                TextColumn::make('STARTDATE')->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('ENDDATE')->date('Y-m-d')
                    ->sortable(),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                Action::make('wr_print')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->url(fn(VcstTrack $record): string => TransferBookMenuResource::getUrl(
                        'wr_print',
                        [
                            'record' => $this->id . '@' .
                                str_replace('/', '-', $record->JOB_NO) . "@" .
                                $record->CPART_NO
                        ]
                    ))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                BulkAction::make('print')
                    // ->requiresConfirmation()
                    ->action(fn(Collection $records) => $this->print_tag($records))
            ]);
    }

    public function submit()
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ], [
            'endDate.after_or_equal' => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
        ]);

        // Add the logic to handle form submission
        // dd($this->endDate);
    }

    public function print_tag($records)
    {
        $zip = new \ZipArchive();
        $zipFileName = storage_path('app/public/documents.zip');
        $pdfFiles = [];  // เก็บ paths ของไฟล์ PDF ที่สร้างขึ้น

        if ($zip->open($zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            foreach ($records as $record) {
                $track_detail = VcstTrackDetail::getTrackDetail($record->JOB_NO, $record->CPART_NO)->get()->toArray();
                $this->generate_document($track_detail, $record->JOB_NO);

                // รับ path ของไฟล์ PDF ที่สร้างขึ้นจาก printDocument
                $documentPath = printDocument::print_document($record->JOB_NO);
                if ($documentPath) {
                    $zip->addFile($documentPath, basename($documentPath));
                    $pdfFiles[] = $documentPath; // เก็บ path ของไฟล์ PDF ไว้สำหรับลบภายหลัง
                }

                // รับ path ของไฟล์ PDF ที่สร้างขึ้นจาก printTag
                $tagPath = printTag::print_tags($record->JOB_NO);
                if ($tagPath) {
                    $zip->addFile($tagPath, basename($tagPath));
                    $pdfFiles[] = $tagPath; // เก็บ path ของไฟล์ PDF ไว้สำหรับลบภายหลัง
                }
            }
            $zip->close();
        } else {
            return response()->json(['error' => 'Failed to create zip file.'], 500);
        }

        // ลบไฟล์ PDF ที่เก็บไว้หลังจาก ZIP เสร็จสิ้น
        foreach ($pdfFiles as $pdfFile) {
            if (file_exists($pdfFile)) {
                unlink($pdfFile);
            }
        }

        // ดาวน์โหลด ZIP ไฟล์
        return response()->download($zipFileName)->deleteFileAfterSend(true);
    }

    public function generate_document($data, $job_no)
    {
        $from_whs_get = TransferBook::query()
            ->where('id', $this->id)
            ->with('book.from_whs')
            ->first();
        $from_whs = $from_whs_get->book->from_whs->FCCODE ?? null;

        $to_whs_get = TransferBook::query()
            ->where('id', $this->id)
            ->with('book.to_whs')
            ->first();
        $to_whs = $to_whs_get->book->to_whs->FCCODE ?? null;

        // dd($from_whs, $to_whs);

        $whouse = '';
        if ($to_whs == 'XXX') {
            $whouse = '005';
        }

        $user_id = Auth::user()->id;
        $department = Auth::user()->dept->FCNAME;
        $created_date = date('Y-m-d');

        $jobHead = JobHead::firstOrCreate(
            ['job_no' => $job_no],
            [
                'doc_no' => $job_no,
                'doc_ref_no' => $job_no,
                'department' => $department,
                'from_whs' => $from_whs,
                'to_whs' => $to_whs,
                'status' => 0,
                'created_date' => $created_date,
                'user_id' => $user_id,
            ]
        );
        $jobHead->save();

        saveJob::saveJobToTag($jobHead->id, $from_whs, $to_whs, $whouse, $user_id, $created_date, $data);
        saveJob::saveJobDetail($jobHead->id, $from_whs, $to_whs, $whouse, $user_id, $created_date);
    }
}
