<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participacao;
use App\Models\Gincana;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GameController extends Controller
{
    public function saveScore(Request $request)
    {
        $validated = $request->validate([
            'gincana_id' => 'nullable|exists:gincanas,id',
            'pontuacao' => 'required|integer|min:0',
            'tempo_total_segundos' => 'nullable|integer|min:0',
            'locais_visitados' => 'nullable|integer|min:0',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);

        // Se não foi especificada uma gincana, criar uma gincana "padrão" ou usar uma existente
        $gincanaId = $validated['gincana_id'];
        
        if (!$gincanaId) {
            // Buscar uma gincana pública padrão ou criar uma para jogo livre
            $gincana = Gincana::where('privacidade', 'publica')->first();
            
            if (!$gincana) {
                // Criar uma gincana padrão para jogo livre
                $gincana = Gincana::create([
                    'user_id' => 1, // Usuário admin/sistema
                    'nome' => 'Jogo Livre',
                    'duracao' => 1,
                    'latitude' => $validated['latitude'] ?? -22.9068,
                    'longitude' => $validated['longitude'] ?? -43.1729,
                    'contexto' => 'Jogo livre - exploração mundial',
                    'privacidade' => 'publica'
                ]);
            }
            
            $gincanaId = $gincana->id;
        }

        // Verificar se o usuário já tem uma participação nesta gincana
        $participacao = Participacao::where('user_id', Auth::id())
                                   ->where('gincana_id', $gincanaId)
                                   ->first();

        if ($participacao) {
            // Atualizar participação existente
            $participacao->update([
                'pontuacao' => $validated['pontuacao'],
                'tempo_total_segundos' => $validated['tempo_total_segundos'] ?? $participacao->tempo_total_segundos,
                'locais_visitados' => $validated['locais_visitados'] ?? $participacao->locais_visitados,
                'status' => 'concluida',
                'fim_participacao' => Carbon::now()
            ]);
        } else {
            // Criar nova participação
            $participacao = Participacao::create([
                'user_id' => Auth::id(),
                'gincana_id' => $gincanaId,
                'pontuacao' => $validated['pontuacao'],
                'inicio_participacao' => Carbon::now(),
                'fim_participacao' => Carbon::now(),
                'tempo_total_segundos' => $validated['tempo_total_segundos'] ?? 0,
                'locais_visitados' => $validated['locais_visitados'] ?? 1,
                'status' => 'concluida'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pontuação salva com sucesso!',
            'participacao_id' => $participacao->id
        ]);
    }
}
