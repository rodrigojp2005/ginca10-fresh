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

            // Notificar criador e quem já comentou; atualizar contador agregado por gincana
            try {
                $gincana = Gincana::find($comentario->gincana_id);
                if ($gincana) {
                    $criadorId = $gincana->user_id;
                    // quem comentou antes nesta gincana (exclui o comentário atual e o autor atual)
                    $comentouAntesIds = Comentario::where('gincana_id', $gincana->id)
                        ->where('id', '!=', $comentario->id)
                        ->pluck('user_id')
                        ->unique()
                        ->filter(fn($id) => (int)$id !== (int)$userId);

                    // Regra: criador sempre recebe (mesmo que não tenha comentado)
                    $targets = collect();
                    if ($criadorId && (int)$criadorId !== (int)$userId) {
                        $targets->push($criadorId);
                    }
                    $targets = $targets->merge($comentouAntesIds)->unique();

                    if ($targets->isNotEmpty()) {
                        $notificaveis = \App\Models\User::whereIn('id', $targets)->get();
                        // Enviar push leve e atualizar contador agregado
                        foreach ($notificaveis as $u) {
                            // Push/WebPush sem poluir lista com 1 item por comentário
                            $u->notify(new NewCommentNotification($comentario));

                            // Atualização do contador agregado
                            \App\Models\GincanaCommentNotification::query()
                                ->updateOrCreate(
                                    ['user_id' => $u->id, 'gincana_id' => $gincana->id],
                                    [
                                        'last_comentario_id' => $comentario->id,
                                        'last_author_name' => $comentario->user?->name,
                                        'last_preview' => \Illuminate\Support\Str::limit($comentario->conteudo, 80),
                                    ]
                                );
                            // incrementar sem condições de corrida relevantes (operação simples)
                            \DB::table('gincana_comment_notifications')
                                ->where('user_id', $u->id)
                                ->where('gincana_id', $gincana->id)
                                ->increment('unread_count');
                        }
                    }
                }
            } catch (\Exception $notifyEx) {
                Log::warning('Falha ao enviar notificações de comentário: ' . $notifyEx->getMessage());
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
