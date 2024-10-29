<?php

namespace App\Filament\Resources\TransferBookResource\Pages;

use App\Filament\Resources\TransferBookResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransferBook extends EditRecord
{
    protected static string $resource = TransferBookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
