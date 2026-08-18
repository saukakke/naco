<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/health', static fn () => response()->json([
    'status' => 'ok',
    'service' => 'naco-portal-api',
    'framework' => 'Laravel 12',
]));

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/me', static fn (\Illuminate\Http\Request $request) => response()->json($request->user()));
});
