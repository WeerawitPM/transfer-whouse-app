<?php

namespace App\Filament\Resources\TransferBookMenuResource\Pages;

use App\Filament\Resources\TransferBookMenuResource;
use App\Filament\Resources\TransferBookMenuResource\Functions\printDocument;
use App\Filament\Resources\TransferBookMenuResource\Functions\printTag;
use App\Models\Fccode_Glref;
use App\Models\JobHead;
use Filament\Resources\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Support\Facades\Route;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Pages\Actions\ButtonAction;
use App\Models\TransferBook;
use Filament\Notifications\Notification;
use App\Filament\Resources\TransferBookMenuResource\Functions\saveJob;
use Auth;

class DetailPrint extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string $resource = TransferBookMenuResource::class;
    protected static string $view = 'filament.resources.transfer-book-menu-resource.pages.detail-print';

    public $id; // Property to hold the ID
    public $transfer_book_id;
    public $doc_no;
    public $ref_no;
    public $file_name;
    public $glref_fcskid;

    public function mount()
    {
        $this->id = Route::current()->parameter('record'); // Get the ID from the route parameters

        // แยกข้อมูลโดยใช้เครื่องหมาย @
        $items = explode('@', $this->id);

        if (count($items) === 3) {
            $this->transfer_book_id = $items[0];

            $this->doc_no = $items[1];
            // เปลี่ยนเครื่องหมาย "-" เป็น "/" ใน job_no
            $this->ref_no = str_replace('-', '/', $items[2]);
            $this->file_name = $items[2];
            $this->glref_fcskid = Fccode_Glref::where('FCREFNO', $this->ref_no)->pluck('FCSKID')->first();
            // dd($this->glref_fcskid);
        }

        // dd([
        //     'transfer_book_id' => $this->transfer_book_id,
        //     'doc_no' => $this->doc_no,
        //     'ref_no' => $this->ref_no,
        //     'file_name' => $this->file_name
        // ]);
    }

    protected function getActions(): array
    {
        $job_head = JobHead::query()->where('doc_ref_no', $this->ref_no)->first();
        // dd($job_head->doc_ref_no);
        if ($job_head) {
            return [
                ButtonAction::make('print_document')
                    ->label('Print Document')
                    ->color('primary')
                    ->icon('heroicon-o-printer')
                    ->action(fn() => printDocument::print_document_one($job_head->doc_ref_no)),
                // ButtonAction::make('print_tag')
                //     ->label('Print Tag')
                //     ->color('primary')
                //     ->icon('heroicon-o-printer')
                //     ->action(fn() => printTag::print_tags_one($job_head->doc_ref_no)),
            ];
        } else {
            return [
                ButtonAction::make('generate_document')
                    ->label('Generate Document')
                    ->color('primary')
                    ->icon('heroicon-o-printer')
                    // ->action(function () {
                    //     $this->generate_document();
                    // })
                    ->extraAttributes([
                        'id' => 'generate_document',
                        'onclick' => 'disableButton()',
                    ])
            ];
        }
    }

    protected function getTableData()
    {
        // dd(Fccode_Glref::get_glref_prod($this->glref_fcskid)->get());
        return Fccode_Glref::get_glref_prod($this->glref_fcskid);
    }

    public function table(Table $table): Table
    {
        // dd($this->id);
        return $table
            ->defaultSort('PART_NO', 'DESC')
            ->query(fn() => $this->getTableData())
            ->columns([
                TextColumn::make('FCSKID')
                    ->label('FCSKID')
                    ->hidden(true),
                // TextColumn::make('DOC_NO')
                //     ->label('Doc No')
                //     ->sortable()
                //     ->searchable(),
                // TextColumn::make('REF_NO')
                //     ->label('Ref No')
                //     ->sortable()
                //     ->searchable(),
                // TextColumn::make('FROM_WHS')
                //     ->label('From whouse'),
                // TextColumn::make('TO_WHS')
                //     ->label('From whouse'),
                TextColumn::make('PART_NO')
                    ->label('Part No')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('PART_CODE')
                    ->label('Part Code')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('PART_NAME')
                    ->label('Part Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('QTY')
                    ->label('Qty')
                    ->numeric(0),
                TextColumn::make('UNIT')
                    ->label('Unit'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
            ])
            ->bulkActions([
            ]);
    }

    public function generate_document()
    {
        // dd($this->getTableData()->get()->toArray());
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

        $whouse = '';
        if ($to_whs == 'XXX') {
            $whouse = '005';
        }

        $user_id = Auth::user()->id;
        $department = Auth::user()->dept->FCNAME;
        $created_date = date('Y-m-d');

        $jobHead = JobHead::firstOrCreate(
            ['job_no' => $this->ref_no],
            [
                'doc_no' => $this->doc_no,
                'doc_ref_no' => $this->ref_no,
                'department' => $department,
                'from_whs' => $from_whs,
                'to_whs' => $to_whs,
                'status' => 0,
                'created_date' => $created_date,
                'user_id' => $user_id,
            ]
        );
        $jobHead->save();

        $data = $this->getTableData()->get()->toArray();
        saveJob::saveJobToTag_No_Kanban($jobHead->id, $from_whs, $to_whs, $whouse, $user_id, $created_date, $data);
        saveJob::saveJobDetail($jobHead->id, $from_whs, $to_whs, $whouse, $user_id, $created_date);

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();

        $url = $this->getUrl([$this->id]);
        // dd($url);

        return redirect($url);
    }
}
