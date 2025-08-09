<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Tenancy\EmpresaController;
use App\Http\Controllers\Tenancy\LocalController;
use App\Http\Controllers\Tenancy\SuscripcionController;
use App\Http\Controllers\Tenancy\SuscripcionLocalController;
use App\Http\Controllers\Tenancy\SubscriptionStatusController;

Route::prefix('v1/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth.jwt');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth.jwt');
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/change-password', [AuthController::class, 'changePassword'])->middleware('auth.jwt');
    Route::post('/force-invalidate', [AuthController::class, 'forceInvalidate'])->middleware('auth.jwt');
});

Route::prefix('v1')->middleware('auth.jwt')->group(function () {
    Route::middleware('check.subscription')->group(function () {
        Route::get('/empresas', [EmpresaController::class, 'index']);
        Route::post('/empresas', [EmpresaController::class, 'store']);
        Route::get('/empresas/{id}', [EmpresaController::class, 'show']);
        Route::put('/empresas/{id}', [EmpresaController::class, 'update']);
        Route::delete('/empresas/{id}', [EmpresaController::class, 'destroy']);

        Route::get('/locales', [LocalController::class, 'index']);
        Route::post('/locales', [LocalController::class, 'store']);
        Route::get('/locales/{id}', [LocalController::class, 'show']);
        Route::put('/locales/{id}', [LocalController::class, 'update']);
        Route::delete('/locales/{id}', [LocalController::class, 'destroy']);

        Route::get('/suscripciones', [SuscripcionController::class, 'index']);
        Route::post('/suscripciones', [SuscripcionController::class, 'store']);
        Route::get('/suscripciones/{id}', [SuscripcionController::class, 'show']);
        Route::put('/suscripciones/{id}', [SuscripcionController::class, 'update']);

        Route::get('/suscripciones-locales', [SuscripcionLocalController::class, 'index']);
        Route::post('/suscripciones-locales', [SuscripcionLocalController::class, 'store']);
        Route::get('/suscripciones-locales/{id}', [SuscripcionLocalController::class, 'show']);
        Route::put('/suscripciones-locales/{id}', [SuscripcionLocalController::class, 'update']);
    });

    Route::get('/estado-suscripcion', SubscriptionStatusController::class);
});
