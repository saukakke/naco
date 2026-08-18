<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
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
    Route::view('/dashboard', 'portal.dashboard')->middleware('auth')->name('dashboard');
});
