<?php

namespace App\Filament\Resources\JobDetailResource\Pages;

use App\Filament\Resources\JobDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJobDetail extends EditRecord
{
    protected static string $resource = JobDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
