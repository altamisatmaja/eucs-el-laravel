<?php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});


Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::middleware(['auth'])->group(function () {

    Route::prefix('data')->group(function () {
        Route::get('/analysis/{type?}', [DataController::class, 'index'])
            ->name('data.index')
            ->where('type', 'x|y')
            ->where('reference', '[0-9]+');
        Route::post('/upload', [DataController::class, 'upload'])->name('data.upload');
        Route::get('/edit/{id}', [DataController::class, 'edit'])->name('data.edit');
        Route::put('/update/{id}', [DataController::class, 'update'])->name('data.update');
        Route::delete('/delete/{id}', [DataController::class, 'destroy'])->name('data.destroy');
    });

    Route::get('/analysis', [AnalysisController::class, 'index'])->name('analysis');
});
