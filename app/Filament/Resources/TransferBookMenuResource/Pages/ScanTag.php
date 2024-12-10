<?php

namespace App\Filament\Resources\TransferBookMenuResource\Pages;

use App\Filament\Resources\TransferBookMenuResource;
use App\Filament\Resources\TransferBookMenuResource\Functions\handleJob;
use App\Models\JobToTag;
use App\Models\Sect;
use App\Models\TransferBook;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Route;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\ButtonAction;
use Filament\Notifications\Notification;
use Auth;
use App\Filament\Resources\TransferBookMenuResource\Functions\handleSaveProduct;
use App\Filament\Resources\TransferBookMenuResource\Functions\handleSaveWrProduct;


class ScanTag extends Page
{
    // use InteractsWithTable;
    protected static string $resource = TransferBookMenuResource::class;
    protected static string $view = 'filament.resources.transfer-book-menu-resource.pages.scan-tag';
    public $id;
    public $input_qr_code;
    public $book;
    public $sections;
    public $user;
    public $section;
    public $tag = [];

    public function mount()
    {
        $this->id = Route::current()->parameter('record'); // Get the ID from the route parameters
        $this->input_qr_code = ''; // Initialize input_qr_code
        $this->book = TransferBook::where('id', $this->id)->get()->first()->book;
        $this->sections = Sect::all()->toArray();
        $this->user = Auth::user();
        $this->section = $this->user->sect->FCSKID;
        $this->count = 0;
        // dd($this->section);
    }

    protected function getActions(): array
    {
        return [
            ButtonAction::make('btn_save')
                ->label('Save')
                ->color('primary')
                ->extraAttributes(
                    [
                        'id' => 'btn_save',
                        'onclick' => 'handleSave()',
                    ]
                )
            // ->requiresConfirmation()
            // ->icon('heroicon-o-cloud'),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('input_qr_code')
                ->label('Scan Tag')
                ->suffixIcon('heroicon-m-magnifying-glass')
                ->extraInputAttributes([
                    'type' => 'text',
                    'inputmode' => 'none',
                    'autocomplete' => "off",
                ])
        ];
    }

    public function handleQrCodeInput($state)
    {
        $this->input_qr_code = $state;

        if ($this->input_qr_code == '') {
            return 'ไม่พบ Tag';
        }

        $tag = JobToTag::where('qr_code', $state)->first();
        if ($tag) {
            if ($tag->status == 1) {
                return 'Tag นี้ถูก scan ไปแล้ว';
            }
            $this->tag = $tag->toArray();
            return $this->tag;
        } else {
            // Notification::make()
            //     ->title('ไม่พบ Tag')
            //     ->warning()
            //     ->color('warning')
            //     ->send();
            return 'ไม่พบ Tag';
        }
    }

    public function handleConfirmSave($section, $jobToTag, $jobDetail)
    {
        $user = Auth::user();
        $book = TransferBook::where('id', $this->id)->get()->first()->book;
        $remark = "Scan";
        handleSaveProduct::handleSaveProduct($jobDetail, $book, $user, $remark, $section);
        handleSaveWrProduct::handleSaveWrProduct($jobDetail, $user, $remark, $section);
        // handleJob::handleUpdateJobHead($job_id);
        handleJob::handleUpdateJobToTag($jobToTag);
        // handleJob::handleUpdateJobDetail($jobDetail);
        Notification::make()
            ->title('Scan tag สำเร็จ')
            ->success()
            ->color('success')
            ->send();

        // Debug ข้อมูล
        // dd($jobToTag, $jobDetail, $jobToTagQuantities, $isComplete);
    }

    public function handleUpdateSection($state)
    {
        $this->section = $state;
        // dd($this->section);
    }

}
