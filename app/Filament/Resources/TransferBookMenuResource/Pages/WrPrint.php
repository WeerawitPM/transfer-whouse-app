<?php

namespace App\Filament\Resources\TransferBookMenuResource\Pages;

use App\Filament\Resources\TransferBookMenuResource;
use App\Filament\Resources\TransferBookMenuResource\Functions\printDocument;
use App\Filament\Resources\TransferBookMenuResource\Functions\printTag;
use App\Filament\Resources\TransferBookMenuResource\Functions\saveJob;
use App\Models\JobToTag;
use App\Models\SetupTag;
use App\Models\VcstTrackDetail;
use App\Models\JobHead;
use App\Models\TransferBook;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Route;
use Filament\Pages\Actions\ButtonAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Notifications\Notification;
use Auth;
use Filament\Tables\Columns\Summarizers\Count;

class WrPrint extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string $resource = TransferBookMenuResource::class;

    protected static string $view = 'filament.resources.transfer-book-menu-resource.pages.wr-print';

    public $id; // Property to hold the ID
    public $transfer_book_id;
    public $job_no;
    public $file_name;
    public $part_no;

    // Override the mount method to access the request
    public function mount()
    {
        $this->id = Route::current()->parameter('record'); // Get the ID from the route parameters

        // แยกข้อมูลโดยใช้เครื่องหมาย @
        $parts = explode('@', $this->id);

        if (count($parts) === 3) {
            $this->transfer_book_id = $parts[0];

            // เปลี่ยนเครื่องหมาย "-" เป็น "/" ใน job_no
            $this->job_no = str_replace('-', '/', $parts[1]);
            $this->file_name = $parts[1];

            $this->part_no = $parts[2];
        }

        // dd([
        //     'transfer_book_id' => $this->transfer_book_id,
        //     'job_no' => $this->job_no,
        //     'part_no' => $this->part_no
        // ]);
    }

    protected function getTableData()
    {
        if ($this->job_no && $this->part_no) {
            // ดึง KANBAN ทั้งหมดใน JobToTag
            $excludedKanbans = JobToTag::pluck('kanban')->toArray();

            // กรองข้อมูลที่ไม่ตรงกับ KANBAN ใน JobToTag
            return VcstTrackDetail::getTrackDetail($this->job_no, $this->part_no)
                ->whereNotIn('KANBAN', $excludedKanbans);
        }

        return VcstTrackDetail::getTrackDetail('Hello World', 'Hello World');
    }

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->query(fn() => $this->getTableData())
            ->columns([
                ImageColumn::make('image')
                    ->label('image')
                    ->getStateUsing(function ($record) {
                        // dd($record->CPART_NO);
                        $setupTag = SetupTag::where('FCCODE', $record->CPART_NO)->first();
                        // dd($setupTag);
                        if ($setupTag && $setupTag->image) {
                            return $setupTag->image;
                        }
                        return asset('storage/image_part/error.jpg');
                    }),
                TextColumn::make('KANBAN')
                    ->label('KANBAN')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('CPART_NO')
                    ->label('Part No')
                    ->sortable(),
                TextColumn::make('FCSNAME')
                    ->label('Part Code')
                    ->sortable(),
                TextColumn::make('FCNAME')
                    ->label('Part Name')
                    ->sortable(),
                TextColumn::make('CMODEL')
                    ->label('Model')
                    ->sortable(),
                TextColumn::make('CPACK')
                    ->label('Package Name')
                    ->sortable(),
                TextColumn::make('QTY')
                    ->label('Quantity')
                    ->summarize(Count::make()),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                BulkAction::make('print')
                    // ->requiresConfirmation()
                    ->label('Print')
                    ->action(fn(Collection $records) => $this->submit($records))
                    ->icon('heroicon-o-printer')
            ])
            ->striped();
    }

    public function submit($records)
    {
        // dd($records->toArray());
        $from_whs_get = TransferBook::query()
            ->where('id', $this->transfer_book_id)
            ->with('book.from_whs')
            ->first();
        $from_whs = $from_whs_get->book->from_whs->FCCODE ?? null;

        $to_whs_get = TransferBook::query()
            ->where('id', $this->transfer_book_id)
            ->with('book.to_whs')
            ->first();
        $to_whs = $to_whs_get->book->to_whs->FCCODE ?? null;

        $book = TransferBook::where('id', $this->transfer_book_id)->get()->first()->book;

        $whouse = '';
        if ($to_whs == 'XXX') {
            $whouse = '005';
        }

        $user_id = Auth::user()->id;
        $department = Auth::user()->dept->FCNAME;
        $created_date = date('Y-m-d');

        // สร้าง job_no
        $yearMonth = date('Ym'); // ปีและเดือนปัจจุบัน
        $lastJob = JobHead::where('job_no', 'like', "JB{$yearMonth}/%")
            ->orderBy('id', 'desc')
            ->first();

        $lastRunNumber = $lastJob ? intval(explode('/', $lastJob->job_no)[1]) : 0;
        $newRunNumber = $lastRunNumber + 1;
        $job_no = sprintf('JB%s/%d', $yearMonth, $newRunNumber);

        $jobHead = JobHead::Create(
            [
                'job_no' => $job_no,
                'doc_no' => $yearMonth . $newRunNumber,
                'doc_ref_no' => $book['FCPREFIX'] . $yearMonth . $newRunNumber,
                'department' => $department,
                'from_whs' => $from_whs,
                'to_whs' => $to_whs,
                'status' => 0,
                'created_date' => $created_date,
                'user_id' => $user_id,
                'job_master' => $this->job_no,
            ]
        );
        $jobHead->save();

        saveJob::saveJobToTag($jobHead->id, $from_whs, $to_whs, $whouse, $user_id, $created_date, $records->toArray());
        saveJob::saveJobDetail($jobHead->id, $from_whs, $to_whs, $whouse, $user_id, $created_date);

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();

        $url = $this->getUrl([$this->id]);
        return $this->print($job_no);
        // return redirect($url);
    }

    public function print($job_no)
    {
        // dd($job_no);
        $zip = new \ZipArchive();
        $zipFileName = storage_path('app/public/documents.zip');
        $pdfFiles = [];  // เก็บ paths ของไฟล์ PDF ที่สร้างขึ้น

        if ($zip->open($zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            // รับ path ของไฟล์ PDF ที่สร้างขึ้นจาก printDocument
            $documentPath = printDocument::print_document($job_no);
            if ($documentPath) {
                $zip->addFile($documentPath, basename($documentPath));
                $pdfFiles[] = $documentPath; // เก็บ path ของไฟล์ PDF ไว้สำหรับลบภายหลัง
            }

            // รับ path ของไฟล์ PDF ที่สร้างขึ้นจาก printTag
            $tagPath = printTag::print_tags($job_no);
            if ($tagPath) {
                $zip->addFile($tagPath, basename($tagPath));
                $pdfFiles[] = $tagPath; // เก็บ path ของไฟล์ PDF ไว้สำหรับลบภายหลัง
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
}
