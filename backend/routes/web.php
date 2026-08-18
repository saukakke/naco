<?php

declare(strict_types=1);
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Portal\IdCardRenewalController;
use App\Http\Controllers\Portal\InstructorController;
use App\Http\Controllers\Portal\UnitTransferController;
use App\Http\Controllers\Portal\WardTransferController;
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
 Route::get('/login',[AuthController::class,'login'])->name('login'); Route::post('/login',[AuthController::class,'authenticate'])->name('authenticate'); Route::post('/logout',[AuthController::class,'logout'])->middleware('auth')->name('logout');
 Route::middleware('auth')->group(function (): void {
  Route::view('/dashboard','portal.dashboard')->name('dashboard');
  Route::get('/unit-transfers',[UnitTransferController::class,'index'])->name('unit-transfers.index'); Route::get('/unit-transfers/create',[UnitTransferController::class,'create'])->name('unit-transfers.create'); Route::post('/unit-transfers',[UnitTransferController::class,'store'])->name('unit-transfers.store'); Route::post('/unit-transfers/{transfer}/release',[UnitTransferController::class,'release'])->name('unit-transfers.release'); Route::post('/unit-transfers/{transfer}/accept',[UnitTransferController::class,'accept'])->name('unit-transfers.accept'); Route::post('/unit-transfers/{transfer}/verify-payment',[UnitTransferController::class,'verifyPayment'])->name('unit-transfers.verify-payment');
  Route::get('/id-card-renewals',[IdCardRenewalController::class,'index'])->name('id-card-renewals.index'); Route::get('/id-card-renewals/create',[IdCardRenewalController::class,'create'])->name('id-card-renewals.create'); Route::post('/id-card-renewals',[IdCardRenewalController::class,'store'])->name('id-card-renewals.store'); Route::post('/id-card-renewals/{application}/verify-payment',[IdCardRenewalController::class,'verifyPayment'])->name('id-card-renewals.verify-payment'); Route::post('/id-card-renewals/{application}/approve',[IdCardRenewalController::class,'approve'])->name('id-card-renewals.approve'); Route::post('/id-card-renewals/{application}/issue',[IdCardRenewalController::class,'issue'])->name('id-card-renewals.issue');
  Route::get('/ward-transfers',[WardTransferController::class,'index'])->name('ward-transfers.index'); Route::get('/ward-transfers/create',[WardTransferController::class,'create'])->name('ward-transfers.create'); Route::post('/ward-transfers',[WardTransferController::class,'store'])->name('ward-transfers.store'); Route::post('/ward-transfers/{transfer}/{action}',[WardTransferController::class,'action'])->whereIn('action',['release','source-lga','source-state','destination-accept','destination-lga','destination-state','national-approve'])->name('ward-transfers.action');
  Route::get('/instructors',[InstructorController::class,'index'])->name('instructors.index'); Route::get('/instructors/courses',[InstructorController::class,'courses'])->name('instructors.courses'); Route::post('/instructors/courses/{course}/enroll',[InstructorController::class,'enroll'])->name('instructors.enroll'); Route::get('/instructors/warrants/{warrant}',[InstructorController::class,'showWarrant'])->name('instructors.warrant');
 });
});
