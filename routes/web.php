<?php

use App\Http\Controllers\PartidaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



/*
Rotas Partidas antes do Breeze
*/
Route::get('/partidas', [PartidaController::class, 'index'])->name('partidas.index');
Route::get('/partidas/{partida}', [PartidaController::class, 'show'])->name('partidas.show');