@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Cabeçalho -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">
                        🏆 Ranking - {{ $gincana->nome }}
                    </h1>
                    <p class="text-gray-600 mt-2">
                        {{ $gincana->contexto }}
                    </p>
                    <div class="flex items-center mt-4 space-x-4 text-sm text-gray-500">
                        <span>📍 Duração: {{ $gincana->duracao }} minutos</span>
                        <span>👥 {{ $participacoes->count() }} participantes</span>
                    </div>
                </div>
                <div class="text-right">
                    <a href="{{ route('gincana.show', $gincana->id) }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                        Ver Gincana
                    </a>
                </div>
            </div>
        </div>

        <!-- Ranking -->
        @if($participacoes->count() > 0)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-yellow-400 to-yellow-600 p-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        🏅 Classificação Final
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
                                    Participante
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pontuação
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tempo
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Locais Visitados
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Conclusão
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($participacoes as $participacao)
                                <tr class="hover:bg-gray-50 transition-colors
                                    @if($participacao->posicao == 1) bg-yellow-50 border-l-4 border-yellow-400
                                    @elseif($participacao->posicao == 2) bg-gray-50 border-l-4 border-gray-400
                                    @elseif($participacao->posicao == 3) bg-orange-50 border-l-4 border-orange-400
                                    @endif">
                                    
                                    <!-- Posição -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($participacao->posicao == 1)
                                                <span class="text-2xl">🥇</span>
                                                <span class="ml-2 text-lg font-bold text-yellow-600">1º</span>
                                            @elseif($participacao->posicao == 2)
                                                <span class="text-2xl">🥈</span>
                                                <span class="ml-2 text-lg font-bold text-gray-600">2º</span>
                                            @elseif($participacao->posicao == 3)
                                                <span class="text-2xl">🥉</span>
                                                <span class="ml-2 text-lg font-bold text-orange-600">3º</span>
                                            @else
                                                <span class="text-lg font-semibold text-gray-700">{{ $participacao->posicao }}º</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Participante -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                                                {{ $participacao->user ? strtoupper(substr($participacao->user->name, 0, 1)) : '?' }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $participacao->user ? $participacao->user->name : 'Usuário Desconhecido' }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $participacao->user ? $participacao->user->email : 'Email não disponível' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Pontuação -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-2xl">⭐</span>
                                            <span class="ml-2 text-lg font-bold text-blue-600">
                                                {{ number_format($participacao->pontuacao) }}
                                            </span>
                                            <span class="ml-1 text-sm text-gray-500">pts</span>
                                        </div>
                                    </td>

                                    <!-- Tempo -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-lg">⏱️</span>
                                            <span class="ml-2 text-sm font-medium text-gray-900">
                                                {{ $participacao->tempo_formatado }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Locais Visitados -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-lg">📍</span>
                                            <span class="ml-2 text-sm font-medium text-gray-900">
                                                {{ $participacao->locais_visitados }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Data de Conclusão -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $participacao->fim_participacao ? $participacao->fim_participacao->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-full">
                            <span class="text-2xl">👥</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total de Participantes</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $participacoes->count() }}</p>
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
                                {{ number_format($participacoes->avg('pontuacao'), 0) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-full">
                            <span class="text-2xl">⏱️</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Tempo Médio</p>
                            <p class="text-2xl font-bold text-gray-900">
                                @php
                                    $tempoMedio = $participacoes->avg('tempo_total_segundos');
                                    $horas = floor($tempoMedio / 3600);
                                    $minutos = floor(($tempoMedio % 3600) / 60);
                                @endphp
                                {{ sprintf('%02d:%02d', $horas, $minutos) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <!-- Nenhuma participação -->
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <div class="text-6xl mb-4">🏆</div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                    Nenhuma participação concluída ainda
                </h3>
                <p class="text-gray-600 mb-6">
                    Seja o primeiro a completar esta gincana e aparecer no ranking!
                </p>
                <a href="{{ route('gincana.show', $gincana->id) }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors">
                    Participar da Gincana
                </a>
            </div>
        @endif

        <!-- Botões de Navegação -->
        <div class="mt-8 flex justify-between">
            <a href="{{ route('gincana.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors">
                ← Voltar às Gincanas
            </a>
            <a href="{{ route('ranking.geral') }}" 
               class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-3 rounded-lg transition-colors">
                Ver Ranking Geral 🌟
            </a>
        </div>
    </div>
</div>
@endsection