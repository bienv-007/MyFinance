<?php

use App\Http\Controllers\BudgetController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth:web')->group(function (): void {
    Route::resource('budgets', BudgetController::class);
});

Route::view('/{any}', 'welcome')->where('any', '.*');
