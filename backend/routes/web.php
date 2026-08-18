<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Portal\UnitTransferController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('home');
Route::view('/about', 'pages.about')->name('about');
Route::view('/programs', 'pages.programs')->name('programs');
Route::view('/leadership', 'pages.leadership')->name('leadership');
Route::view('/teams', 'pages.teams')->name('teams');
Route::view('/gallery', 'pages.gallery')->name('gallery');
Route::view('/blog', 'pages.blog')->name('blog');
Route::view('/impact', 'pages.impact')->name('impact');
Route::view('/contact', 'pages.contact')->name('contact');

Route::prefix('portal')->name('portal.')->group(function (): void {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
    Route::middleware('auth')->group(function (): void {
        Route::view('/dashboard', 'portal.dashboard')->name('dashboard');
        Route::get('/unit-transfers', [UnitTransferController::class, 'index'])->name('unit-transfers.index');
        Route::get('/unit-transfers/create', [UnitTransferController::class, 'create'])->name('unit-transfers.create');
        Route::post('/unit-transfers', [UnitTransferController::class, 'store'])->name('unit-transfers.store');
        Route::post('/unit-transfers/{transfer}/release', [UnitTransferController::class, 'release'])->name('unit-transfers.release');
        Route::post('/unit-transfers/{transfer}/accept', [UnitTransferController::class, 'accept'])->name('unit-transfers.accept');
    });
});
