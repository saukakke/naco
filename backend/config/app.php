<?php

use Illuminate\Support\Facades\Vite;

return [
    'name' => env('APP_NAME', 'NACO'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'Africa/Lagos',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => [...array_filter(explode(',', (string) env('APP_PREVIOUS_KEYS', '')))],
    'maintenance' => ['driver' => 'file', 'store' => 'database'],
    'providers' => Illuminate\Support\ServiceProvider::defaultProviders()->merge([])->toArray(),
    'aliases' => [],
];
