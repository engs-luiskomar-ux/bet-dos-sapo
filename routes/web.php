<?php
use App\Http\Controllers\PartidaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimeController;

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

    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');

    Route::resource('times', TimeController::class);
});

    // Partidas (João)
    Route::get('/partidas', [PartidaController::class, 'index'])->name('partidas.index');
    Route::get('/partidas/{partida}', [PartidaController::class, 'show'])->name('partidas.show');

require __DIR__.'/auth.php';