@extends('layouts.app')

@section('title')
    @auth
        Gincaneiros - Jogue sua gincana!
    @else
        Gincaneiros - Desafio do bem!
    @endauth
@endsection

@section('content')
<div class="game-container">
    <!-- Informações do jogo -->
    <div class="game-info">
        <div><strong>Gincana:</strong> {{ $gincana->nome }}</div>
        <div><strong>Pontuação:</strong> <span id="score">1000</span></div>
        <div><strong>Tentativas:</strong> <span id="attempts">5</span></div>
        <div><strong>Rodada:</strong> <span id="round">1</span></div>
        @auth
            <div><strong>Jogador:</strong> {{ auth()->user()->name }}</div>
        @else
            <div><strong>Modo:</strong> Visitante</div>
        @endauth
    </div>

    <!-- Container do Street View -->
    <div id="streetview" class="street-view-container"></div>

    <!-- Controles do jogo -->
    <div class="game-controls">
        <button id="showMapBtn" class="btn">📍 JOGAR</button>
        <button id="newGameBtn" class="btn btn-success" style="display: none;">🎮 Novo Jogo</button>
        
        @auth
            <a href="{{ route('gincana.index') }}" class="btn btn-secondary" style="margin-left: 10px; background-color: #6b7280; color: white; padding: 10px 16px; border-radius: 6px; text-decoration: none;">
                ← Voltar
            </a>
        @else
            <a href="{{ url('/') }}" class="btn btn-secondary" style="margin-left: 10px; background-color: #6b7280; color: white; padding: 10px 16px; border-radius: 6px; text-decoration: none;">
                ← Voltar
            </a>
        @endauth
    </div>

    <!-- Slider do mapa -->
    <div id="mapSlider" class="map-slider">
        <!-- Header com título e botão fechar -->
        <div class="map-slider-header">
            <h3 class="map-slider-title">📍 Faça seu Palpite</h3>
            <button id="closeMapBtn" class="close-btn">
                <span>✕</span>
            </button>
        </div>

        <!-- Container do mapa -->
        <div id="map" class="map-container"></div>

        <!-- Footer com botões -->
        <div class="map-slider-footer">
            <button id="confirmGuessBtn" class="btn btn-confirm">Confirmar Palpite</button>
        </div>
    </div>

    <div id="overlay" class="overlay"></div>
    <div id="popup" class="popup">
        <h3 id="popupTitle">Resultado</h3>
        <p id="popupMessage"></p>
        <button id="continueBtn" class="btn">Continuar</button>
    </div>
</div>

<script>
    // Passar dados da gincana para o JS
    window.gameLocations = [
        {
            lat: {{ $gincana->latitude }},
            lng: {{ $gincana->longitude }},
            name: "{{ $gincana->nome }}",
            gincana_id: {{ $gincana->id }},
            contexto: "{{ $gincana->contexto }}"
        }
    ];
    
    window.gameData = {
        locations: window.gameLocations,
        isAuthenticated: {{ auth()->check() ? 'true' : 'false' }},
        currentGincana: @json($gincana)
    };
</script>
@endsection
