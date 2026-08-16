<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DepensePrevisionController;
use App\Http\Controllers\RevenuPrevisionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth:web')->group(function (): void {
    Route::get('budgets/{budget}/historique', [BudgetController::class, 'history'])->name('budgets.history');
    Route::resource('budgets', BudgetController::class);
    Route::post('revenu-previsions/{revenu_prevision}/receive', [RevenuPrevisionController::class, 'markAsReceived'])
        ->name('revenu-previsions.receive');
    Route::resource('revenu-previsions', RevenuPrevisionController::class);
    Route::post('depense-previsions/{depense_prevision}/validate', [DepensePrevisionController::class, 'validate'])
        ->name('depense-previsions.validate');
    Route::resource('depense-previsions', DepensePrevisionController::class);
});

Route::view('/{any}', 'welcome')->where('any', '^(?!api(?:/|$)).*');
