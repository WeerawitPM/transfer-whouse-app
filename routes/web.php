<?php

use App\Http\Controllers\ProfileController;
use App\Http\Middleware\RedirectToWeb;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScanController;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

Route::middleware(RedirectToWeb::class)->group(function () {
    Route::get('/', function () {
    });
});

Route::resource('index', ScanController::class);

require __DIR__ . '/auth.php';
