<?php

declare(strict_types=1);

use App\Http\Controllers\Api\CadetController;
use App\Http\Controllers\Api\DemotionController;
use App\Http\Controllers\Api\LookupController;
use App\Http\Controllers\Api\PromotionController;
use App\Http\Controllers\Api\UnitTransferController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', static fn () => response()->json([
    'status' => 'ok',
    'service' => 'naco-portal-api',
    'framework' => 'Laravel 12',
]));

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/me', static fn (Request $request) => response()->json($request->user()));
    Route::get('/units', [LookupController::class, 'units']);
    Route::get('/ranks', [LookupController::class, 'ranks']);
    Route::get('/posts', [LookupController::class, 'posts']);
    Route::get('/courses', [LookupController::class, 'courses']);
    Route::apiResource('cadets', CadetController::class)->only(['index', 'show', 'store', 'update']);
    Route::post('/cadets/{cadet}/promotions', [PromotionController::class, 'store']);
    Route::post('/cadets/{cadet}/demotions', [DemotionController::class, 'store']);
    Route::post('/cadets/{cadet}/unit-transfers', [UnitTransferController::class, 'store']);
    Route::get('/unit-transfers/{transfer}', [UnitTransferController::class, 'show']);
    Route::post('/unit-transfers/{transfer}/release', [UnitTransferController::class, 'release']);
    Route::post('/unit-transfers/{transfer}/accept', [UnitTransferController::class, 'accept']);
    Route::post('/unit-transfers/{transfer}/verify-payment', [UnitTransferController::class, 'verifyPayment']);
});
