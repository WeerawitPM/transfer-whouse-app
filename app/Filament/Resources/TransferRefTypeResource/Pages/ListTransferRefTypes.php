<?php

namespace App\Filament\Resources\TransferRefTypeResource\Pages;

use App\Filament\Resources\TransferRefTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransferRefTypes extends ListRecords
{
    protected static string $resource = TransferRefTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
