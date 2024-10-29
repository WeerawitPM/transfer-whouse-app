<?php

namespace App\Filament\Resources\EmplrResource\Pages;

use App\Filament\Resources\EmplrResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmplrs extends ListRecords
{
    protected static string $resource = EmplrResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
