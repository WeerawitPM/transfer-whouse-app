<?php

namespace App\Filament\Resources\TransferBookMenuResource\Pages;

use App\Filament\Resources\TransferBookMenuResource;
use Filament\Resources\Pages\Page;

class DetailPrint extends Page
{
    protected static string $resource = TransferBookMenuResource::class;

    protected static string $view = 'filament.resources.transfer-book-menu-resource.pages.detail-print';
}
