<?php

declare(strict_types=1);

use App\Http\Controllers\Api\CadetController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\DemotionController;
use App\Http\Controllers\Api\IdCardRenewalController;
use App\Http\Controllers\Api\LgaController;
use App\Http\Controllers\Api\PostAssignmentController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PromotionController;
use App\Http\Controllers\Api\RankCategoryController;
use App\Http\Controllers\Api\RankController;
use App\Http\Controllers\Api\StateController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\UnitTransferController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WardController;
use App\Http\Controllers\Api\WardTransferController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', static fn () => response()->json(['status'=>'ok','service'=>'naco-portal-api','framework'=>'Laravel 12']));

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/me', static fn (Request $request) => response()->json($request->user()));
    Route::apiResource('cadets', CadetController::class)->only(['index','show','store','update']);
    Route::apiResource('courses', CourseController::class);
    Route::apiResource('units', UnitController::class);
    Route::apiResource('states', StateController::class);
    Route::apiResource('lgas', LgaController::class);
    Route::apiResource('wards', WardController::class);
    Route::apiResource('rank-categories', RankCategoryController::class);
    Route::apiResource('ranks', RankController::class);
    Route::apiResource('posts', PostController::class);
    Route::apiResource('post-assignments', PostAssignmentController::class);
    Route::apiResource('users', UserController::class);
    Route::post('/cadets/{cadet}/promotions', [PromotionController::class,'store']);
    Route::post('/cadets/{cadet}/demotions', [DemotionController::class,'store']);
    Route::post('/cadets/{cadet}/unit-transfers', [UnitTransferController::class,'store']);
    Route::get('/unit-transfers/{transfer}', [UnitTransferController::class,'show']);
    Route::post('/unit-transfers/{transfer}/release', [UnitTransferController::class,'release']);
    Route::post('/unit-transfers/{transfer}/accept', [UnitTransferController::class,'accept']);
    Route::post('/unit-transfers/{transfer}/verify-payment', [UnitTransferController::class,'verifyPayment']);
    Route::get('/ward-transfers', [WardTransferController::class,'index']);
    Route::post('/ward-transfers', [WardTransferController::class,'store']);
    Route::get('/ward-transfers/{transfer}', [WardTransferController::class,'show']);
    Route::post('/ward-transfers/{transfer}/release', [WardTransferController::class,'release']);
    Route::post('/ward-transfers/{transfer}/source-lga', [WardTransferController::class,'sourceLga']);
    Route::post('/ward-transfers/{transfer}/source-state', [WardTransferController::class,'sourceState']);
    Route::post('/ward-transfers/{transfer}/destination-hcs', [WardTransferController::class,'destinationHcs']);
    Route::post('/ward-transfers/{transfer}/destination-lga', [WardTransferController::class,'destinationLga']);
    Route::post('/ward-transfers/{transfer}/destination-state', [WardTransferController::class,'destinationState']);
    Route::post('/ward-transfers/{transfer}/national-approve', [WardTransferController::class,'nationalApprove']);
    Route::get('/id-card-renewals', [IdCardRenewalController::class,'index']);
    Route::post('/id-card-renewals', [IdCardRenewalController::class,'store']);
    Route::get('/id-card-renewals/{application}', [IdCardRenewalController::class,'show']);
    Route::post('/id-card-renewals/{application}/verify-payment', [IdCardRenewalController::class,'verifyPayment']);
    Route::post('/id-card-renewals/{application}/approve', [IdCardRenewalController::class,'approve']);
    Route::post('/id-card-renewals/{application}/issue', [IdCardRenewalController::class,'issue']);
});
