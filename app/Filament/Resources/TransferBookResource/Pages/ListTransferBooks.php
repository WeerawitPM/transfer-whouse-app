<?php

namespace App\Filament\Resources\TransferBookResource\Pages;

use App\Filament\Resources\TransferBookResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransferBooks extends ListRecords
{
    protected static string $resource = TransferBookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
