<?php

namespace App\Filament\Resources\TransferBookMenuResource\Pages;

use App\Filament\Resources\TransferBookMenuResource;
use App\Models\Fccode_Glref;
use Filament\Resources\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Support\Facades\Route;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;

class DetailPrint extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string $resource = TransferBookMenuResource::class;
    protected static string $view = 'filament.resources.transfer-book-menu-resource.pages.detail-print';

    public $id; // Property to hold the ID
    public $transfer_book_id;
    public $ref_no;
    public $file_name;
    public $glref_fcskid;

    public function mount()
    {
        $this->id = Route::current()->parameter('record'); // Get the ID from the route parameters

        // แยกข้อมูลโดยใช้เครื่องหมาย @
        $items = explode('@', $this->id);

        if (count($items) === 2) {
            $this->transfer_book_id = $items[0];

            // เปลี่ยนเครื่องหมาย "-" เป็น "/" ใน job_no
            $this->ref_no = str_replace('-', '/', $items[1]);
            $this->file_name = $items[1];
            $this->glref_fcskid = Fccode_Glref::where('FCREFNO', $this->ref_no)->pluck('FCSKID')->first();
            // dd($this->glref_fcskid);
        }

        // dd([
        //     'transfer_book_id' => $this->transfer_book_id,
        //     'ref_no' => $this->ref_no,
        //     'file_name' => $this->file_name
        // ]);
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
            ])
            ->filters([
                // ...
            ])
            ->actions([
            ])
            ->bulkActions([
            ]);
    }
}
