<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gincana;
use App\Models\Participacao;

class RankingController extends Controller
{
    public function show($gincanaId)
    {
        $gincana = Gincana::findOrFail($gincanaId);
        
        // Buscar participações ordenadas por critérios de ranking
        $participacoes = Participacao::where('gincana_id', $gincanaId)
            ->where('status', 'concluida')
            ->with('user')
            ->whereHas('user') // Garante que só carrega participações com usuário válido
            ->orderBy('pontuacao', 'desc') // Primeiro critério: maior pontuação
            ->orderBy('tempo_total_segundos', 'asc') // Segundo critério: menor tempo
            ->orderBy('locais_visitados', 'desc') // Terceiro critério: mais locais visitados
            ->get();

        // Adicionar posição no ranking
        $participacoes->each(function ($participacao, $index) {
            $participacao->posicao = $index + 1;
        });

        return view('gincana.ranking', compact('gincana', 'participacoes'));
    }

    public function index()
    {
        // Listar todas as gincanas que têm participações
        $gincanas = Gincana::whereHas('participacoes', function($query) {
            $query->where('status', 'concluida');
        })->withCount(['participacoes' => function($query) {
            $query->where('status', 'concluida');
        }])->get();

        return view('gincana.rankings-list', compact('gincanas'));
    }

    public function geral()
    {
        // Ranking geral de todos os usuários em todas as gincanas
        $rankingGeral = Participacao::where('status', 'concluida')
            ->with(['user', 'gincana'])
            ->whereHas('user') // Garante que só carrega participações com usuário válido
            ->selectRaw('
                user_id,
                users.name as user_name,
                COUNT(*) as gincanas_concluidas,
                SUM(pontuacao) as pontuacao_total,
                AVG(tempo_total_segundos) as tempo_medio,
                SUM(locais_visitados) as total_locais_visitados
            ')
            ->join('users', 'participacoes.user_id', '=', 'users.id')
            ->groupBy('user_id', 'users.name')
            ->orderBy('pontuacao_total', 'desc')
            ->orderBy('gincanas_concluidas', 'desc')
            ->orderBy('tempo_medio', 'asc')
            ->get();

        // Adicionar posição no ranking
        $rankingGeral->each(function ($item, $index) {
            $item->posicao = $index + 1;
        });

        return view('gincana.ranking-geral', compact('rankingGeral'));
    }
}
