@extends('layouts.app')
@section('content')
<div class="container" style="max-width: 800px; margin: 10px auto 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
        <h2 style="margin: 0; font-weight: 600; color: #198754; font-size: 2rem;">Gincanas que Criei</h2>
    </div>
    
    @if($gincanas->isEmpty())
        <div class="text-center py-16">
            <div class="max-w-md mx-auto">
                <div class="text-6xl mb-6">🎯</div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">
                    Nenhuma gincana criada ainda
                </h3>
                <p class="text-gray-600 mb-8">
                    Que tal criar sua primeira gincana personalizada?
                </p>
            </div>
        </div>
    @else
        <div style="overflow-x:auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <table class="table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr style="background: #198754; color: #fff;">
                        <th style="padding: 12px 8px; font-weight: 500;">Nome</th>
                        <th style="padding: 12px 8px; font-weight: 500;">Duração</th>
                        <th style="padding: 12px 8px; font-weight: 500;">Localização</th>
                        <th style="padding: 12px 8px; font-weight: 500;">Privacidade</th>
                        <th style="padding: 12px 8px; font-weight: 500;">Criada em</th>
                        <th style="padding: 12px 8px; font-weight: 500; text-align:center;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($gincanas as $i => $gincana)
                        <tr style="background: {{ $i % 2 == 0 ? '#eafaf1' : '#c7eed8' }};">
                            <td style="padding: 10px 8px;">{{ $gincana->nome }}</td>
                            <td style="padding: 10px 8px;">{{ $gincana->duracao }} min</td>
                            <td style="padding: 10px 8px;">{{ number_format($gincana->latitude, 4) }}, {{ number_format($gincana->longitude, 4) }}</td>
                            <td style="padding: 10px 8px;">
                                <span style="padding: 4px 8px; border-radius: 12px; font-size: 12px; background: {{ $gincana->privacidade == 'publica' ? '#d4edda' : '#fff3cd' }}; color: {{ $gincana->privacidade == 'publica' ? '#155724' : '#856404' }};">
                                    {{ $gincana->privacidade == 'publica' ? '🌍 Pública' : '🔒 Privada' }}
                                </span>
                            </td>
                            <td style="padding: 10px 8px;">{{ $gincana->created_at->format('d/m/Y') }}</td>
                            <td style="padding: 10px 8px; text-align:center;">
                                <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                    <button type="button" title="Compartilhar" onclick="compartilharGincana('{{ $gincana->nome }}', '{{ route('gincana.show', $gincana) }}')" style="background: none; border: none; font-size: 1.3em; vertical-align: middle; display: flex; align-items: center; cursor: pointer;">
                                        <img src="https://media2.giphy.com/media/v1.Y2lkPTc5MGI3NjExd2R5aDI5bnVkNHAyMG5zM2tnNHVlOGY5NjA1ZW04ZzZrNzNpZGx4biZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/XfmFPcUZTddaFZhLgt/giphy.gif" alt="Compartilhar" style="width: 33px; height: 33px; display: inline-block; margin-right: 2px;">
                                    </button>
                                    <a href="{{ route('gincana.edit', $gincana) }}" title="Editar" style="background: none; border: none; color: #ffc107; font-size: 1.3em; vertical-align: middle; display: flex; align-items: center; text-decoration: none;">
                                        ✏️
                                    </a>
                                    <form action="{{ route('gincana.destroy', $gincana) }}" method="POST" style="display:inline; margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Excluir" style="background: none; border: none; color: #dc3545; font-size: 1.3em; vertical-align: middle; display: flex; align-items: center; cursor: pointer;" onclick="return confirm('Tem certeza que deseja excluir esta gincana?')">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    
    <div style="width: 100%; display: flex; justify-content: center; margin-top: 32px;">
        <a href="{{ route('gincana.create') }}" class="btn btn-primary" style="font-weight: 500; background: #198754; border: none; min-width: 220px; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none;">🎮 Criar Nova Gincana</a>
    </div>

    <script>
    function compartilharGincana(nome, url) {
        if (navigator.share) {
            navigator.share({
                title: nome,
                text: `Jogue a gincana "${nome}"!`,
                url: url
            });
        } else {
            alert('Compartilhamento nativo não disponível neste dispositivo.');
        }
    }
    </script>
</div>
@endsection
