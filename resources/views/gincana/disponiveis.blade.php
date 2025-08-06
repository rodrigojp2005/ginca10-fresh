@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-br from-green-50 to-blue-100 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                🌟 Gincanas Disponíveis
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Descubra novas gincanas criadas pela comunidade e comece a jogar agora mesmo!
            </p>
        </div>

        @if($gincanasDisponiveis->count() > 0)
            <!-- Statistics Card -->
            <div class="bg-white rounded-xl shadow-lg p-6 text-center mb-8">
                <div class="text-3xl font-bold text-green-600">{{ $gincanasDisponiveis->count() }}</div>
                <div class="text-gray-600 mt-2">{{ $gincanasDisponiveis->count() == 1 ? 'Gincana Disponível' : 'Gincanas Disponíveis' }}</div>
            </div>

            <!-- Gincanas List -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($gincanasDisponiveis as $gincana)
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-green-500">
                        <div class="p-6">
                            <!-- Header -->
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-xl font-bold text-gray-900 line-clamp-2">
                                    {{ $gincana->nome }}
                                </h3>
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    🆕 Disponível
                                </span>
                            </div>

                            <!-- Creator info -->
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-8 h-8 bg-green-300 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-green-800">
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
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Duração:</span>
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ $gincana->duracao }} {{ $gincana->duracao == 1 ? 'local' : 'locais' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Privacidade:</span>
                                    <span class="text-sm font-medium text-green-600">
                                        Pública
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Criada em:</span>
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ $gincana->created_at->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                <a href="{{ route('gincana.jogar', $gincana) }}" 
                                   class="flex-1 bg-green-600 text-white text-center px-4 py-2 rounded-lg hover:bg-green-700 transition-colors duration-200 text-sm font-medium">
                                    🎮 Jogar Agora
                                </a>
                                <!-- <a href="{{ route('gincana.show', $gincana) }}" 
                                   class="flex-1 bg-gray-600 text-white text-center px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors duration-200 text-sm font-medium">
                                    👁️ Ver Detalhes
                                </a> -->
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        @else
            <div class="text-center mt-12">
                <div class="space-y-4">
                    <a href="{{ route('gincana.jogadas') }}" 
                    class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200 font-medium">
                        🎮 Ver Minhas Jogadas
                    </a>
                    <div class="text-gray-500">ou</div>
                    <a href="{{ route('gincana.create') }}" 
                    class="inline-block bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors duration-200 font-medium">
                        ➕ Criar Nova Gincana
                    </a>
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
