<?php

namespace App\Filament\Resources\TransferBookMenuResource\Pages;

use App\Filament\Resources\TransferBookMenuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransferBookMenus extends ListRecords
{
    protected static string $resource = TransferBookMenuResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //     ];
    // }
}
