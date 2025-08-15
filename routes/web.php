<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GincanaController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\ComentarioController;
use App\Models\Gincana; // Pode remover se não for mais usado diretamente aqui
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request; // Pode remover se não for mais usado diretamente aqui
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

// A função getGameLocations() foi removida daqui.
// A lógica agora está no método welcome() do GincanaController.

// Rota principal - agora aponta para o GincanaController
Route::get('/', [GincanaController::class, 'welcome'])->middleware('guest')->name('home');

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
    
    // Rotas para comentários (sem middleware auth temporariamente)
    Route::get('/test-comentarios', function() {
        return response()->json(['message' => 'Rota de teste funcionando', 'timestamp' => now()]);
    });
    Route::post('/comentarios', [ComentarioController::class, 'store'])->name('comentarios.store');
    Route::get('/comentarios/{gincana_id}', [ComentarioController::class, 'index'])->name('comentarios.index');

    // Push subscription
    Route::post('/push/subscribe', [\App\Http\Controllers\PushSubscriptionController::class, 'store'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [\App\Http\Controllers\PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');
    Route::post('/push/test', function() { 
        $user = Auth::user();
        if(!$user) return response()->json(['error' => 'no auth'], 401);
        $fakeComentario = new \App\Models\Comentario([
            'gincana_id' => 1,
            'user_id' => $user->id,
            'conteudo' => 'Teste de notificação manual'
        ]);
        $fakeComentario->setRelation('user', $user);
        $fakeComentario->setRelation('gincana', new \App\Models\Gincana(['nome' => 'Gincana Teste']));
        $user->notify(new \App\Notifications\NewCommentNotification($fakeComentario));
        return response()->json(['sent' => true]);
    });

    // Notificações (agregadas por gincana)
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index']);
    Route::post('/notifications/read', [\App\Http\Controllers\NotificationController::class, 'markRead']);

    // DEBUG TEMPORÁRIO: verificar se a chave do Google Maps está disponível no servidor (valores mascarados)
    Route::get('/debug/maps-key', function () {
        $mask = function($v){ if(!$v) return null; $len = strlen($v); if($len<=8) return str_repeat('*', $len); return substr($v,0,4).str_repeat('*', max(0,$len-8)).substr($v,-4); };
        return response()->json([
            'config_services_google_maps_api_key' => $mask(config('services.google.maps_api_key')),
            'env_GOOGLE_MAPS_API_KEY' => $mask(env('GOOGLE_MAPS_API_KEY')),
            'env_VITE_GOOGLE_MAPS_API_KEY' => $mask(env('VITE_GOOGLE_MAPS_API_KEY')),
            'app_env' => app()->environment(),
            'php_version' => PHP_VERSION,
        ]);
    });
});

// Rotas do Google OAuth
Route::get('auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);

require __DIR__.'/auth.php';
