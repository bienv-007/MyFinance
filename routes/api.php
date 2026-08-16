<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BudgetController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DepenseController;
use App\Http\Controllers\Api\DepensePrevisionController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\RevenuController;
use App\Http\Controllers\Api\RevenuPrevisionController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function (): void {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:web')->group(function (): void {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::apiResource('categories', CategoryController::class);
        Route::patch('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::patch('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::apiResource('notifications', NotificationController::class)
            ->only(['index', 'show', 'destroy'])
            ->names([
                'index' => 'api.notifications.index',
                'show' => 'api.notifications.show',
                'destroy' => 'api.notifications.destroy',
            ]);
        Route::apiResource('budgets', BudgetController::class)->names([
            'index' => 'api.budgets.index',
            'store' => 'api.budgets.store',
            'show' => 'api.budgets.show',
            'update' => 'api.budgets.update',
            'destroy' => 'api.budgets.destroy',
        ]);
        Route::apiResource('revenus', RevenuController::class);
        Route::apiResource('revenu-previsions', RevenuPrevisionController::class)->names([
            'index' => 'api.revenu-previsions.index',
            'store' => 'api.revenu-previsions.store',
            'show' => 'api.revenu-previsions.show',
            'update' => 'api.revenu-previsions.update',
            'destroy' => 'api.revenu-previsions.destroy',
        ]);
        Route::post('revenu-previsions/{revenu_prevision}/receive', [RevenuPrevisionController::class, 'markAsReceived'])
            ->name('api.revenu-previsions.receive');
        Route::apiResource('depenses', DepenseController::class);
        Route::apiResource('depense-previsions', DepensePrevisionController::class)->names([
            'index' => 'api.depense-previsions.index',
            'store' => 'api.depense-previsions.store',
            'show' => 'api.depense-previsions.show',
            'update' => 'api.depense-previsions.update',
            'destroy' => 'api.depense-previsions.destroy',
        ]);
        Route::post('depense-previsions/{depense_prevision}/validate', [DepensePrevisionController::class, 'validate'])
            ->name('api.depense-previsions.validate');
    });
});

Route::any('{any}', fn () => response()->json(['message' => 'Route API introuvable.'], 404))
    ->where('any', '.*');
