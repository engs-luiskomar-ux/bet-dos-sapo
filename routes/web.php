<?php

use App\Http\Controllers\ApostaController;
use App\Http\Controllers\PartidaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TimeController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Apostas (Luis)
    Route::get('/apostas', [ApostaController::class, 'index'])->name('apostas.index');
    Route::get('/apostas/historico', [ApostaController::class, 'historico'])->name('apostas.historico');
    Route::post('/apostas', [ApostaController::class, 'store'])->name('apostas.store');
    Route::post('/apostas/{aposta}/cancelar', [ApostaController::class, 'cancelar'])->name('apostas.cancelar');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::patch('/usuarios/{usuario}/papel', [UsuarioController::class, 'alterarPapel'])->name('usuarios.alterar-papel');

    Route::resource('times', TimeController::class);

    // Partidas (João)
    Route::get('/partidas', [PartidaController::class, 'index'])->name('partidas.index');
    Route::get('/partidas/criar', [PartidaController::class, 'create'])->name('partidas.create');
    Route::post('/partidas', [PartidaController::class, 'store'])->name('partidas.store');
    Route::get('/partidas/{partida}', [PartidaController::class, 'show'])->name('partidas.show');
});

require __DIR__.'/auth.php';
