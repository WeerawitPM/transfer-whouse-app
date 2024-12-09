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
    public $tags;
    public $tags_detail;
    public $book;
    public $sections;
    public $user;
    public $section;

    public function mount()
    {
        $this->id = Route::current()->parameter('record'); // Get the ID from the route parameters
        $this->input_qr_code = ''; // Initialize input_qr_code
        $this->tags = [];
        $this->tags_detail = [];
        $this->book = TransferBook::where('id', $this->id)->get()->first()->book;
        $this->sections = Sect::all()->toArray();
        $this->user = Auth::user();
        $this->section = $this->user->sect->FCSKID;
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
            return;
        }

        $tag = JobToTag::where('qr_code', $state)->get()->first();
        // dd($tag);
        // dd($tag['qr_code']);
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
                // Check if the qr_code already exists in tags
                $existingTag = collect($this->tags)->firstWhere('qr_code', $tag->qr_code);
                if ($existingTag) {
                    Notification::make()
                        ->title('Tag นี้ถูกเพิ่มแล้ว')
                        ->warning()
                        ->color('warning')
                        ->send();
                    return;
                }

                $this->tags[] = $tag->toArray();
                // จัดเรียง tags ตาม id จากมากไปน้อย
                // usort($this->tags, function ($a, $b) {
                //     return $b['id'] <=> $a['id'];
                // });
                $this->updateTagsDetail();
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

    public function updateTagsDetail()
    {
        $tagsGrouped = [];

        // Group tags by part_no and calculate qty and tag_qty
        foreach ($this->tags as $tag) {
            $part_no = $tag['part_no'];

            if (!isset($tagsGrouped[$part_no])) {
                // Initialize data for this part_no
                $tagsGrouped[$part_no] = [
                    'part_no' => $tag['part_no'],
                    'part_code' => $tag['part_code'],
                    'part_name' => $tag['part_name'],
                    'model' => $tag['model'],
                    'qty' => 0, // Initialize total qty
                    'packing_name' => $tag['packing_name'],
                    'whouse' => $tag['whouse'],
                    'from_whs' => $tag['from_whs'],
                    'to_whs' => $tag['to_whs'],
                    'tag_qty' => 0, // Initialize tag count
                ];
            }

            // Accumulate the qty and tag_qty for each part_no
            $tagsGrouped[$part_no]['qty'] += $tag['qty'];
            $tagsGrouped[$part_no]['tag_qty']++;
        }

        // Update tags_detail with the grouped data
        $this->tags_detail = array_values($tagsGrouped);
        // dd($this->tags_detail);
    }

    public function handleConfirmSave($section)
    {
        // dd($section);
        $jobToTag = $this->tags;
        $jobDetail = $this->tags_detail;

        if (empty($jobToTag)) {
            Notification::make()
                ->title('เกิดข้อผิดพลาด ไม่มีข้อมูลที่จะบันทึก')
                ->danger()
                ->color('danger')
                ->send();
            return;
        }

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
        $this->tags = [];
        $this->tags_detail = [];

        // Debug ข้อมูล
        // dd($jobToTag, $jobDetail, $jobToTagQuantities, $isComplete);
    }

    public function handleDeleteTag($index)
    {
        // Remove the selected item from $tags array
        if (isset($this->tags[$index])) {
            unset($this->tags[$index]);
            $this->tags = array_values($this->tags); // Re-index the array after deletion
        }

        $this->updateTagsDetail();
        Notification::make()
            ->title('ลบข้อมูลเรียบร้อยแล้ว')
            ->success()
            ->color('success')
            ->send();
    }

    public function handleDeleteTagDetail($index)
    {
        // Check if the index exists in tags_detail
        if (isset($this->tags_detail[$index])) {
            $part_no_to_delete = $this->tags_detail[$index]['part_no'];

            // Remove the selected item from tags_detail
            unset($this->tags_detail[$index]);
            $this->tags_detail = array_values($this->tags_detail); // Re-index tags_detail after deletion

            // Remove all items from tags that have the same part_no
            $this->tags = array_filter($this->tags, function ($tag) use ($part_no_to_delete) {
                return $tag['part_no'] !== $part_no_to_delete;
            });

            // Re-index tags after filtering
            $this->tags = array_values($this->tags);

            // Update tags_detail based on remaining tags
            $this->updateTagsDetail();

            Notification::make()
                ->title('ลบข้อมูลเรียบร้อยแล้ว')
                ->success()
                ->color('success')
                ->send();
        }
    }

    public function handleUpdateSection($state)
    {
        $this->section = $state;
        // dd($this->section);
    }
}
