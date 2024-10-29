<?php

namespace App\Filament\Resources\WhouseResource\Pages;

use App\Filament\Resources\WhouseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWhouses extends ListRecords
{
    protected static string $resource = WhouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
