<?php

namespace App\Filament\Resources\SectResource\Pages;

use App\Filament\Resources\SectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSects extends ListRecords
{
    protected static string $resource = SectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
