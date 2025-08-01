<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GincanaController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Models\GincanaLocal;
use App\Models\Gincana;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

// Função para carregar locais de jogo
function getGameLocations() {
    $locations = [];
    
    // 1. Buscar locais principais das gincanas públicas criadas pelos usuários
    $gincanas = Gincana::where('privacidade', 'publica')->get();
    foreach ($gincanas as $gincana) {
        $locations[] = [
            'lat' => (float) $gincana->latitude,
            'lng' => (float) $gincana->longitude,
            'name' => $gincana->nome,
            'gincana_id' => $gincana->id,
            'contexto' => $gincana->contexto
        ];
    }
    
    // 2. Buscar locais adicionais das gincanas públicas (tabela gincana_locais)
    $locaisAdicionais = GincanaLocal::whereHas('gincana', function($query) {
        $query->where('privacidade', 'publica');
    })->with('gincana')->get();
    
    foreach ($locaisAdicionais as $local) {
        $locations[] = [
            'lat' => (float) $local->latitude,
            'lng' => (float) $local->longitude,
            'name' => $local->gincana->nome . ' - Local Adicional',
            'gincana_id' => $local->gincana_id,
            'contexto' => $local->gincana->contexto
        ];
    }
    
    // Se não houver gincanas criadas pelos usuários, retornar um marcador especial para exibir alerta no front-end
    if (empty($locations)) {
        $locations[] = [
            'no_gincana' => true
        ];
    }
    
    return $locations;
}

// Rota principal - funciona para visitantes e usuários logados
Route::get('/', function (Request $request) {
    $locations = getGameLocations();
    return view('welcome', compact('locations'));
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rotas das Gincanas
    Route::get('/gincana/create', [GincanaController::class, 'create'])->name('gincana.create');
    Route::post('/gincana', [GincanaController::class, 'store'])->name('gincana.store');
    Route::get('/gincana', [GincanaController::class, 'index'])->name('gincana.index');
    Route::get('/gincana/jogadas', [GincanaController::class, 'jogadas'])->name('gincana.jogadas');
    Route::get('/gincana/disponiveis', [GincanaController::class, 'disponiveis'])->name('gincana.disponiveis');
    Route::get('/gincana/{gincana}', [GincanaController::class, 'show'])->name('gincana.show');
    Route::get('/gincana/{gincana}/jogar', [GincanaController::class, 'jogar'])->name('gincana.jogar');
    Route::get('/gincana/{gincana}/edit', [GincanaController::class, 'edit'])->name('gincana.edit');
    Route::put('/gincana/{gincana}', [GincanaController::class, 'update'])->name('gincana.update');
    Route::delete('/gincana/{gincana}', [GincanaController::class, 'destroy'])->name('gincana.destroy');
    
    // Rotas do jogo
    Route::post('/game/save-score', [GameController::class, 'saveScore'])->name('game.save-score');
    
    // Rotas de Ranking
    Route::get('/rankings', [RankingController::class, 'index'])->name('ranking.index');
    Route::get('/ranking/{gincana}', [RankingController::class, 'show'])->name('ranking.show');
    Route::get('/ranking-geral', [RankingController::class, 'geral'])->name('ranking.geral');
});

// Rotas do Google OAuth
Route::get('auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);

require __DIR__.'/auth.php';
