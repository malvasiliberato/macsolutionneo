<?php

use App\Http\Controllers\Api\V1\MeController;
use App\Http\Controllers\Api\V1\SetActiveMembershipController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'application' => config('app.name'),
            'timestamp' => now()->toIso8601String(),
        ]);
    })->name('health');

    Route::middleware(['web', 'auth:sanctum'])->group(function () {
        Route::get('/me', MeController::class)->name('me');
        Route::post('/context/active-membership', SetActiveMembershipController::class)->name('context.active-membership');
    });
});
