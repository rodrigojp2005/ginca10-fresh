<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GincanaController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\ComentarioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rota principal - AGORA SEM O MIDDLEWARE 'guest' PARA FUNCIONAR PARA TODOS
Route::get('/', [GincanaController::class, 'welcome'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // ... O RESTO DAS SUAS ROTAS CONTINUA EXATAMENTE IGUAL ...
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
    
    // Rotas para comentários
    Route::post('/comentarios', [ComentarioController::class, 'store'])->name('comentarios.store');
    Route::get('/comentarios/{gincana_id}', [ComentarioController::class, 'index'])->name('comentarios.index');

    // Push subscription
    Route::post('/push/subscribe', [\App\Http\Controllers\PushSubscriptionController::class, 'store'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [\App\Http\Controllers\PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');

    // Notificações internas
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index']);
    Route::post('/notifications/read', [\App\Http\Controllers\NotificationController::class, 'markRead']);
});

// Rotas do Google OAuth
Route::get('auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);

require __DIR__.'/auth.php';
