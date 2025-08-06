@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Cabeçalho -->
        <div class="bg-gradient-to-r from-purple-600 to-purple-800 rounded-lg shadow-md p-8 mb-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold flex items-center">
                        🌟 Ranking Geral dos Gincaneiros!
                    </h1>
                    <p class="text-purple-100 mt-2 text-lg">
                        Os melhores jogadores de todas as gincanas
                    </p>
                </div>
                <div class="text-right">
                    <a href="{{ route('ranking.index') }}" 
                       class="bg-white hover:bg-gray-100 text-purple-600 px-6 py-3 rounded-lg transition-colors font-semibold">
                        Ver Rankings por Gincana
                    </a>
                </div>
            </div>
        </div>

        @if($rankingGeral->count() > 0)
            <!-- Pódio dos Top 3 -->
            @if($rankingGeral->count() >= 3)
                <div class="grid grid-cols-3 gap-4 mb-8">
                    <!-- 2º Lugar -->
                    <div class="bg-white rounded-lg shadow-md p-6 text-center transform hover:scale-105 transition-transform">
                        <div class="text-6xl mb-2">🥈</div>
                        <div class="text-xl font-bold text-gray-800">2º Lugar</div>
                        <div class="w-16 h-16 bg-gradient-to-r from-gray-400 to-gray-600 rounded-full mx-auto mt-4 mb-3 flex items-center justify-center text-white text-2xl font-bold">
                            {{ strtoupper(substr($rankingGeral[1]->user_name, 0, 1)) }}
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $rankingGeral[1]->user_name }}</h3>
                        <div class="text-2xl font-bold text-blue-600 mt-2">{{ number_format($rankingGeral[1]->pontuacao_total) }} pts</div>
                        <div class="text-sm text-gray-600 mt-1">{{ $rankingGeral[1]->gincanas_concluidas }} gincanas</div>
                    </div>

                    <!-- 1º Lugar -->
                    <div class="bg-white rounded-lg shadow-lg p-8 text-center transform hover:scale-105 transition-transform border-4 border-yellow-400">
                        <div class="text-8xl mb-2">🥇</div>
                        <div class="text-2xl font-bold text-yellow-600">CAMPEÃO</div>
                        <div class="w-20 h-20 bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-full mx-auto mt-4 mb-3 flex items-center justify-center text-white text-3xl font-bold">
                            {{ strtoupper(substr($rankingGeral[0]->user_name, 0, 1)) }}
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $rankingGeral[0]->user_name }}</h3>
                        <div class="text-3xl font-bold text-blue-600 mt-2">{{ number_format($rankingGeral[0]->pontuacao_total) }} pts</div>
                        <div class="text-sm text-gray-600 mt-1">{{ $rankingGeral[0]->gincanas_concluidas }} gincanas</div>
                    </div>

                    <!-- 3º Lugar -->
                    <div class="bg-white rounded-lg shadow-md p-6 text-center transform hover:scale-105 transition-transform">
                        <div class="text-6xl mb-2">🥉</div>
                        <div class="text-xl font-bold text-gray-800">3º Lugar</div>
                        <div class="w-16 h-16 bg-gradient-to-r from-orange-400 to-orange-600 rounded-full mx-auto mt-4 mb-3 flex items-center justify-center text-white text-2xl font-bold">
                            {{ strtoupper(substr($rankingGeral[2]->user_name, 0, 1)) }}
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $rankingGeral[2]->user_name }}</h3>
                        <div class="text-2xl font-bold text-blue-600 mt-2">{{ number_format($rankingGeral[2]->pontuacao_total) }} pts</div>
                        <div class="text-sm text-gray-600 mt-1">{{ $rankingGeral[2]->gincanas_concluidas }} gincanas</div>
                    </div>
                </div>
            @endif

            <!-- Tabela Completa do Ranking -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="bg-gradient-to-r from-purple-600 to-purple-800 p-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        📊 Ranking Completo
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Posição
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jogador
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pontuação Total
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Gincanas Concluídas
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tempo Médio
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total de Locais
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($rankingGeral as $jogador)
                                <tr class="hover:bg-gray-50 transition-colors
                                    @if($jogador->posicao == 1) bg-yellow-50 border-l-4 border-yellow-400
                                    @elseif($jogador->posicao == 2) bg-gray-50 border-l-4 border-gray-400
                                    @elseif($jogador->posicao == 3) bg-orange-50 border-l-4 border-orange-400
                                    @endif">
                                    
                                    <!-- Posição -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($jogador->posicao == 1)
                                                <span class="text-2xl">🥇</span>
                                                <span class="ml-2 text-lg font-bold text-yellow-600">1º</span>
                                            @elseif($jogador->posicao == 2)
                                                <span class="text-2xl">🥈</span>
                                                <span class="ml-2 text-lg font-bold text-gray-600">2º</span>
                                            @elseif($jogador->posicao == 3)
                                                <span class="text-2xl">🥉</span>
                                                <span class="ml-2 text-lg font-bold text-orange-600">3º</span>
                                            @elseif($jogador->posicao <= 10)
                                                <span class="text-lg font-semibold text-blue-600">{{ $jogador->posicao }}º</span>
                                            @else
                                                <span class="text-lg font-semibold text-gray-700">{{ $jogador->posicao }}º</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Jogador -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-12 h-12 bg-gradient-to-r from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                                {{ strtoupper(substr($jogador->user_name, 0, 1)) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $jogador->user_name }}
                                                </div>
                                                @if($jogador->posicao <= 3)
                                                    <div class="text-xs text-yellow-600 font-semibold">
                                                        ⭐ TOP PLAYER
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Pontuação Total -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-xl">⭐</span>
                                            <span class="ml-2 text-lg font-bold text-blue-600">
                                                {{ number_format($jogador->pontuacao_total) }}
                                            </span>
                                            <span class="ml-1 text-sm text-gray-500">pts</span>
                                        </div>
                                    </td>

                                    <!-- Gincanas Concluídas -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-lg">🎯</span>
                                            <span class="ml-2 text-sm font-medium text-gray-900">
                                                {{ $jogador->gincanas_concluidas }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Tempo Médio -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-lg">⏱️</span>
                                            <span class="ml-2 text-sm font-medium text-gray-900">
                                                @php
                                                    $tempoMedio = $jogador->tempo_medio;
                                                    $horas = floor($tempoMedio / 3600);
                                                    $minutos = floor(($tempoMedio % 3600) / 60);
                                                @endphp
                                                {{ sprintf('%02d:%02d', $horas, $minutos) }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Total de Locais -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-lg">📍</span>
                                            <span class="ml-2 text-sm font-medium text-gray-900">
                                                {{ $jogador->total_locais_visitados }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Estatísticas Gerais -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8 w-full">
            <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-full">
                            <span class="text-2xl">👥</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total de Jogadores</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $rankingGeral->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <span class="text-2xl">⭐</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Pontuação Média</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ number_format($rankingGeral->avg('pontuacao_total'), 0) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-full">
                            <span class="text-2xl">🎯</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Gincanas por Jogador</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ number_format($rankingGeral->avg('gincanas_concluidas'), 1) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-full">
                            <span class="text-2xl">📍</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Locais Visitados</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ number_format($rankingGeral->sum('total_locais_visitados')) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <!-- Nenhum ranking ainda -->
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <div class="text-6xl mb-4">🌟</div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                    Ranking geral ainda não disponível
                </h3>
                <p class="text-gray-600 mb-6">
                    Complete algumas gincanas para começar a ver o ranking geral aparecer aqui!
                </p>
                <a href="{{ route('gincana.index') }}" 
                   class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-3 rounded-lg transition-colors">
                    Participar de Gincanas
                </a>
            </div>
        @endif

        <!-- Botão de Voltar -->
        <div class="mt-8 text-center">
            <a href="{{ route('gincana.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors">
                ← Voltar às Gincanas
            </a>
        </div>
    </div>
</div>
@endsection