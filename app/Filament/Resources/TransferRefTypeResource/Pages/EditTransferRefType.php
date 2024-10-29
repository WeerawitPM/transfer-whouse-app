<?php

namespace App\Filament\Resources\TransferRefTypeResource\Pages;

use App\Filament\Resources\TransferRefTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransferRefType extends EditRecord
{
    protected static string $resource = TransferRefTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
