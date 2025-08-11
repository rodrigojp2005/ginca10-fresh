<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\Gincana;
use App\Notifications\NewCommentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ComentarioController extends Controller
{
    public function index($gincana_id)
    {
        try {
            // Primeiro verificar se é um número válido
            if (!is_numeric($gincana_id)) {
                return response()->json(['error' => 'ID inválido'], 400);
            }
            
            $comentarios = Comentario::where('gincana_id', $gincana_id)
                ->with('user:id,name')  // Carregar apenas id e name do usuário
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json($comentarios);
            
        } catch (\Exception $e) {
            \Log::error("Erro ao buscar comentários: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validação básica
            $validated = $request->validate([
                'gincana_id' => 'required|integer',
                'conteudo' => 'required|string|max:500'
            ]);

            // Se não estiver logado, usar user_id = 1 para teste
            $userId = Auth::check() ? Auth::id() : 1;

            $comentario = Comentario::create([
                'gincana_id' => $validated['gincana_id'],
                'user_id' => $userId,
                'conteudo' => $validated['conteudo']
            ]);

            $comentario->load('user:id,name');

            // Notificar participantes e comentaristas anteriores (simples)
            try {
                $gincana = Gincana::with(['participantes' => function($q){ $q->select('users.id'); }])->find($comentario->gincana_id);
                if ($gincana) {
                    // IDs de usuários que participaram ou comentaram antes
                    $comentouAntesIds = Comentario::where('gincana_id', $gincana->id)
                        ->where('id', '!=', $comentario->id)
                        ->pluck('user_id')
                        ->unique();
                    $participanteIds = $gincana->participantes->pluck('id');
                    $targets = $participanteIds->merge($comentouAntesIds)->unique()->filter(fn($id) => (int)$id !== (int)$userId);
                    if ($targets->isNotEmpty()) {
                        $notificaveis = \App\Models\User::whereIn('id', $targets)->get();
                        foreach ($notificaveis as $u) {
                            $u->notify(new NewCommentNotification($comentario));
                        }
                    }
                }
            } catch (\Exception $notifyEx) {
                Log::warning('Falha ao enviar notificações push: ' . $notifyEx->getMessage());
            }

            return response()->json([
                'success' => true,
                'comentario' => $comentario
            ]);
            
        } catch (\Exception $e) {
            \Log::error("Erro ao salvar comentário: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
