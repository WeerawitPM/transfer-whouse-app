<?php

namespace App\Filament\Resources\JobDetailResource\Pages;

use App\Filament\Resources\JobDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJobDetail extends CreateRecord
{
    protected static string $resource = JobDetailResource::class;
}
