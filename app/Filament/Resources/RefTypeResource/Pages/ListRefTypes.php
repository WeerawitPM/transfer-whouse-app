<?php

namespace App\Filament\Resources\RefTypeResource\Pages;

use App\Filament\Resources\RefTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRefTypes extends ListRecords
{
    protected static string $resource = RefTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
