<?php

namespace App\Filament\Resources\TransferBookMenuResource\Pages;

use App\Filament\Resources\TransferBookMenuResource;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Route;

class Manual extends Page
{
    protected static string $resource = TransferBookMenuResource::class;
    protected static string $view = 'filament.resources.transfer-book-menu-resource.pages.manual';

    public $id;

    public function mount()
    {
        $this->id = Route::current()->parameter('record'); // Get the ID from the route parameters
    }
}
