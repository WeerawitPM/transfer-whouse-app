<?php

namespace App\Filament\Resources\JobToTagResource\Pages;

use App\Filament\Resources\JobToTagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJobToTags extends ListRecords
{
    protected static string $resource = JobToTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
