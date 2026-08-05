<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DepensePrevisionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth:web')->group(function (): void {
    Route::resource('budgets', BudgetController::class);
    Route::post('depense-previsions/{depense_prevision}/validate', [DepensePrevisionController::class, 'validate'])
        ->name('depense-previsions.validate');
    Route::resource('depense-previsions', DepensePrevisionController::class);
});

Route::view('/{any}', 'welcome')->where('any', '.*');
