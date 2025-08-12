@extends('layouts.app')

@section('content')
@if($gincanasJogadas->count() > 0)
<div class="bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
@else
<div class="flex flex-col min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="flex-1 max-w-md mx-auto">
@endif
    @if($gincanasJogadas->count() > 0)
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                🎮 Gincanas Jogadas!
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Aqui estão todas as gincanas que você participou. Clique em uma gincana para ver o ranking!
            </p>
        </div>
            <!-- Gincanas List -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($gincanasJogadas as $gincana)
                    @php
                        // Buscar a primeira (ou melhor) participação
                        $participacao = $gincana->participacoes->sortByDesc('pontuacao')->first();
                        $isCompleted = $participacao && $participacao->status === 'concluida';
                        $statusColor = $isCompleted ? 'green' : 'orange';
                        $statusText = $isCompleted ? 'Concluída' : 'Em Progresso';
                        $statusIcon = $isCompleted ? '✅' : '⏳';
                    @endphp
                    
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="p-6">
                            <!-- Header with status -->
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-xl font-bold text-gray-900 line-clamp-2">
                                    {{ $gincana->nome }}
                                </h3>
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                                    {{ $statusIcon }} {{ $statusText }}
                                </span>
                            </div>

                            <!-- Creator info -->
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-600">
                                        {{ $gincana->user ? substr($gincana->user->name, 0, 1) : '?' }}
                                    </span>
                                </div>
                                <span class="text-sm text-gray-600">
                                    Criada por {{ $gincana->user ? $gincana->user->name : 'Usuário Desconhecido' }}
                                </span>
                            </div>

                            <!-- Context -->
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                {{ $gincana->contexto }}
                            </p>

                            <!-- Stats -->
                            <div class="space-y-2 mb-4">
                                @if($isCompleted && $participacao)
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Pontuação:</span>
                                        <span class="text-sm font-medium text-green-600">
                                            {{ number_format($participacao->pontuacao ?? 0, 0) }} pts
                                        </span>
                                    </div>
                                    
                                    @if($participacao->tempo_total_segundos)
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Tempo:</span>
                                            <span class="text-sm font-medium text-blue-600">
                                                {{ gmdate('H:i:s', $participacao->tempo_total_segundos) }}
                                            </span>
                                        </div>
                                    @endif
                                @elseif($participacao)
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Locais visitados:</span>
                                        <span class="text-sm font-medium text-orange-600">
                                            {{ $participacao->locais_visitados ?? 0 }} / {{ $gincana->duracao }}
                                        </span>
                                    </div>
                                @endif
                                
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Duração:</span>
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ $gincana->duracao }} {{ $gincana->duracao == 1 ? 'local' : 'locais' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                <a href="{{ route('gincana.jogar', $gincana) }}" 
                                   class="flex-1 bg-green-600 text-white text-center px-4 py-2 rounded-lg hover:bg-green-700 transition-colors duration-200 text-sm font-medium">
                                    🎮 Jogar
                                </a>
                                <a href="{{ route('ranking.show', $gincana->id) }}" 
                                   class="flex-1 bg-blue-600 text-white text-center px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm font-medium">
                                    🏆 Ver Ranking
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        @else
            <!-- Empty State -->
           <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    🎮
                    <br> Nenhuma Gincana Jogada Ainda!
                </h1>
            </div>  
            <div class="text-center py-4">
                <div class="max-w-md mx-auto">                  
                    <div class="space-y-4">
                        <a href="{{ route('home') }}" 
                           class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200 font-medium">
                            🎯 Jogar Agora
                        </a>
                        <div class="text-gray-500">ou</div>
                        <a href="{{ route('ranking.index') }}" 
                           class="inline-block bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition-colors duration-200 font-medium">
                            🏆 Ver Rankings
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection
