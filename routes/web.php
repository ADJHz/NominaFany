<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\NominaController;
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('nominas', function () {
    return Inertia::render('Nominas');
})->middleware(['auth', 'verified'])->name('nominas');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';


Route::get('/nominas', [NominaController::class, 'index'])->name('nominas');
Route::post('/nominasProcesar', [NominaController::class, 'procesar'])->name('nominas.procesar');
Route::post('/nominas/descargar-recibos', [NominaController::class, 'descargarRecibos'])->name('nominas.descargarRecibos');
