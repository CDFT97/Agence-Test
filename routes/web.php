<?php

use App\Http\Controllers\AgenceController;
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

Route::get('/', [AgenceController::class, 'agence'])->name('agence');

Route::post('/process-form', [AgenceController::class, 'processForm'])->name('process-form');
Route::get('/client', [AgenceController::class, 'client'])->name('client');
Route::post('/inform', [AgenceController::class, 'inform'])->name('inform');
Route::post('/chart', [AgenceController::class, 'grafico'])->name('chart');
Route::post('/pizza', [AgenceController::class, 'pizza'])->name('pizza');


