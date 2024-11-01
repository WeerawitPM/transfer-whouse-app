<?php

namespace App\Filament\Resources\SetupTagResource\Pages;

use App\Filament\Resources\SetupTagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSetupTags extends ListRecords
{
    protected static string $resource = SetupTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
