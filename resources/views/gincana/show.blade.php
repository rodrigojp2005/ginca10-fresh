@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-blue-700 mb-6">Detalhes da Gincana</h1>
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $gincana->nome }}</h2>
        <p class="text-gray-600 mb-2">{{ $gincana->contexto }}</p>
        <div class="mb-2"><strong>Duração:</strong> {{ $gincana->duracao }} min</div>
        <div class="mb-2"><strong>Privacidade:</strong> {{ ucfirst($gincana->privacidade) }}</div>
        <div class="mb-2"><strong>Criada em:</strong> {{ $gincana->created_at->format('d/m/Y') }}</div>

        <div class="mb-2"><strong>Localização:</strong> {{ $gincana->cidade ?? 'Não informado' }}</div>
        <div class="mb-2"><strong>Latitude:</strong> {{ $gincana->latitude ?? 'Não informado' }} | <strong>Longitude:</strong> {{ $gincana->longitude ?? 'Não informado' }}</div>

        <div style="display: flex; gap: 16px; margin-top: 18px;">
            <div style="flex: 1;">
                <label style="display: block; font-weight: bold; margin-bottom: 6px;">Mapa</label>
                <div id="map-show" style="height: 250px; width: 100%; border: 1px solid #ccc; border-radius: 4px;"></div>
            </div>
            <div style="flex: 1;">
                <label style="display: block; font-weight: bold; margin-bottom: 6px;">Street View</label>
                <div id="street-view-show" style="height: 250px; width: 100%; border: 1px solid #ccc; border-radius: 4px;"></div>
            </div>
        </div>
    </div>
    <a href="{{ route('gincana.jogar', $gincana->id) }}" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Jogar Gincana</a>

<!-- Google Maps Script Show -->
<script>
    function initMapShow() {
        const lat = parseFloat({{ $gincana->latitude ?? -23.55052 }});
        const lng = parseFloat({{ $gincana->longitude ?? -46.633308 }});
        const location = { lat: lat, lng: lng };

        const mapShow = new google.maps.Map(document.getElementById('map-show'), {
            center: location,
            zoom: 15
        });

        new google.maps.Marker({
            position: location,
            map: mapShow
        });

        const streetViewShow = new google.maps.StreetViewPanorama(
            document.getElementById('street-view-show'), {
                position: location,
                pov: { heading: 165, pitch: 0 },
                zoom: 1
            }
        );
    }
    window.initMapShow = initMapShow;
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMapShow&libraries=geometry"></script>
</div>
@endsection
