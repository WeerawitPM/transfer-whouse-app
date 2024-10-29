<?php

namespace App\Filament\Resources\EmplrResource\Pages;

use App\Filament\Resources\EmplrResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmplr extends EditRecord
{
    protected static string $resource = EmplrResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
