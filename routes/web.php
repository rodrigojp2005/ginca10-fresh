<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GincanaController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\Auth\SocialiteController;
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
    
    // Por enquanto, usar locais padrão até criar o modelo Gincana
    $locations = [
        ['lat' => -22.9068, 'lng' => -43.1729, 'name' => 'Cristo Redentor, Rio de Janeiro'],
        ['lat' => -22.9519, 'lng' => -43.2105, 'name' => 'Copacabana, Rio de Janeiro'],
        ['lat' => -23.5505, 'lng' => -46.6333, 'name' => 'São Paulo, SP'],
        ['lat' => -15.7942, 'lng' => -47.8822, 'name' => 'Brasília, DF'],
        ['lat' => -12.9714, 'lng' => -38.5014, 'name' => 'Salvador, BA']
    ];
    
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
