<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ComentarioController extends Controller
{
    public function index($gincana_id)
    {
        try {
            Log::info("Buscando comentários para gincana_id: " . $gincana_id);
            
            $comentarios = Comentario::where('gincana_id', $gincana_id)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            Log::info("Encontrados " . $comentarios->count() . " comentários");
            
            return response()->json($comentarios);
        } catch (\Exception $e) {
            Log::error("Erro ao carregar comentários: " . $e->getMessage());
            return response()->json([]);
        }
    }

    public function store(Request $request)
    {
        try {
            Log::info("Dados recebidos: ", $request->all());
            Log::info("Usuário autenticado: " . (Auth::check() ? Auth::id() : 'Não logado'));
            
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

            $comentario->load('user');

            Log::info("Comentário salvo com sucesso", ['id' => $comentario->id]);

            return response()->json([
                'success' => true,
                'comentario' => $comentario
            ]);
            
        } catch (\Exception $e) {
            Log::error("Erro completo: " . $e->getMessage() . " | Linha: " . $e->getLine() . " | Arquivo: " . $e->getFile());
            return response()->json([
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ], 500);
        }
    }
}
