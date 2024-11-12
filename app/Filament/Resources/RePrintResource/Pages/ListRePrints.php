<?php

namespace App\Filament\Resources\RePrintResource\Pages;

use App\Filament\Resources\RePrintResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRePrints extends ListRecords
{
    protected static string $resource = RePrintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
