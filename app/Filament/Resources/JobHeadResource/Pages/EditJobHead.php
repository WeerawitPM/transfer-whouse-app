<?php

namespace App\Filament\Resources\JobHeadResource\Pages;

use App\Filament\Resources\JobHeadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJobHead extends EditRecord
{
    protected static string $resource = JobHeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
