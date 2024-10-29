<?php

namespace App\Filament\Resources\SectResource\Pages;

use App\Filament\Resources\SectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSect extends EditRecord
{
    protected static string $resource = SectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
