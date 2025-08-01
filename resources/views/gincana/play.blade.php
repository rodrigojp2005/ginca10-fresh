@extends('layouts.app')

@section('title')
    Jogando: {{ $gincana->nome }} - Gincaneiros
@endsection

@section('content')
<div class="game-container">
    <!-- Informações da gincana -->
    <div class="game-info" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div><strong>Gincana:</strong> {{ $gincana->nome }}</div>
        <div><strong>Pontuação:</strong> <span id="score">1000</span></div>
        <div><strong>Tentativas:</strong> <span id="attempts">5</span></div>
        <div><strong>Rodada:</strong> <span id="round">1</span></div>
        <div><strong>Jogador:</strong> {{ auth()->user()->name }}</div>
    </div>

    <!-- Container do Street View -->
    <div id="streetview" class="street-view-container"></div>

    <!-- Controles do jogo -->
    <div class="game-controls">
        <button id="showMapBtn" class="btn">Ver Mapa</button>
        <button id="newGameBtn" class="btn btn-success" style="display: none;">Novo Jogo</button>
        
        <a href="{{ route('gincana.show', $gincana) }}" class="btn btn-secondary" style="margin-left: 10px; background-color: #6b7280;">
            Ver Detalhes
        </a>
        <a href="{{ route('ranking.show', $gincana) }}" class="btn btn-primary" style="margin-left: 10px; background-color: #6f42c1;">
            🏆 Ver Ranking
        </a>
        <a href="{{ route('gincana.index') }}" class="btn btn-secondary" style="margin-left: 10px; background-color: #9ca3af;">
            Voltar
        </a>
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

    <!-- Modal de resultado -->
    <div id="resultModal" class="modal">
        <div class="modal-content">
            <h2 id="resultTitle">Resultado</h2>
            <div id="resultContent">
                <p><strong>Pontos:</strong> <span id="pointsEarned">0</span></p>
                <p><strong>Distância:</strong> <span id="distance">0</span> km</p>
                <p><strong>Localização real:</strong> <span id="actualLocation">-</span></p>
            </div>
            <div class="modal-buttons">
                <button id="continueBtn" class="btn btn-success">Continuar</button>
                <button id="finishBtn" class="btn btn-primary">Finalizar</button>
            </div>
        </div>
    </div>
</div>

<!-- Passar dados para o JavaScript -->
<script>
    window.gameData = {
        locations: @json($locations),
        isAuthenticated: true,
        currentGincana: @json($gincana)
    };
</script>

@vite(['resources/css/game.css', 'resources/js/game.js'])
@endsection