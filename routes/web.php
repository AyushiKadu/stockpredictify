<?php

use App\Http\Controllers\HoldingController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
}); 

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// ✅ STOCK ROUTES
Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
Route::post('/stocks/fetch', [StockController::class, 'fetch'])->name('stocks.fetch');

// ✅ PORTFOLIO ROUTES
Route::resource('portfolios', PortfolioController::class);

// ✅ HOLDINGS ROUTES (nested under portfolio)
Route::get('/portfolios/{portfolio}/holdings', [HoldingController::class, 'index'])->name('holdings.index');
Route::get('/portfolios/{portfolio}/holdings/create', [HoldingController::class, 'create'])->name('holdings.create');
Route::post('/portfolios/{portfolio}/holdings', [HoldingController::class, 'store'])->name('holdings.store');
Route::get('/holdings/{holding}/edit', [HoldingController::class, 'edit'])->name('holdings.edit');
Route::put('/holdings/{holding}', [HoldingController::class, 'update'])->name('holdings.update');
Route::delete('/holdings/{holding}', [HoldingController::class, 'destroy'])->name('holdings.destroy');
Route::get('/stocks/fetch', [StockController::class, 'showForm']);
Route::post('/stocks/fetch', [StockController::class, 'fetch'])->name('stocks.fetch');


// show smart prediction UI
Route::get('/stocks/smart', [StockController::class, 'smartForm'])->name('stocks.smart.form');

// call Flask and show results (POST)
Route::post('/stocks/predict-smart', [StockController::class, 'predictSmart'])->name('stocks.predict-smart');

Route::get('/portfolios/{portfolio}/export-pdf', [PortfolioController::class, 'exportPdf'])
    ->name('portfolios.exportPdf');

Route::get('/portfolios/{portfolio}/prices', [PortfolioController::class, 'prices'])
     ->name('portfolios.prices');
