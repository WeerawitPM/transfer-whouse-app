<?php

namespace App\Filament\Resources\RefTypeResource\Pages;

use App\Filament\Resources\RefTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRefType extends EditRecord
{
    protected static string $resource = RefTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
