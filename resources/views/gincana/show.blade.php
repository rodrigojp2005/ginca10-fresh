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

    <button id="btn-compartilhar" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700" style="margin-top: 12px;">Compartilhar</button>
    <button type="button" onclick="window.history.back()" style="margin-left: 12px; padding: 10px 28px; background-color: #ffc107; color: #212529; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 1.08em; display: inline-flex; align-items: center; gap: 8px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16"><path d="M15 8a.5.5 0 0 1-.5.5H3.707l3.147 3.146a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L3.707 7.5H14.5A.5.5 0 0 1 15 8z"/></svg>
        Voltar
    </button>

    <!-- Modal Compartilhar -->
    <div id="modal-compartilhar" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.35); z-index:9999; justify-content:center; align-items:center;">
        <div style="background:#fff; border-radius:10px; padding:28px 22px; max-width:340px; width:90vw; box-shadow:0 2px 12px rgba(0,0,0,0.12); text-align:center;">
            <h3 style="font-size:1.3em; font-weight:700; color:#198754; margin-bottom:18px;">Compartilhe com amigos!</h3>
            <div style="display:flex; flex-direction:column; gap:14px;">
                <a href="https://wa.me/?text={{ urlencode(route('gincana.show', $gincana->id)) }}" target="_blank" style="background:#25D366; color:#fff; padding:10px; border-radius:6px; font-weight:600; text-decoration:none;">WhatsApp</a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('gincana.show', $gincana->id)) }}" target="_blank" style="background:#4267B2; color:#fff; padding:10px; border-radius:6px; font-weight:600; text-decoration:none;">Facebook</a>
                <button onclick="copiarLinkGincana()" style="background:#6c757d; color:#fff; padding:10px; border-radius:6px; font-weight:600; border:none;">Copiar Link</button>
            </div>
            <button onclick="fecharModalCompartilhar()" style="margin-top:18px; background:#dc3545; color:#fff; padding:8px 18px; border-radius:6px; border:none; font-weight:600;">Fechar</button>
        </div>
    </div>

    <script>
        document.getElementById('btn-compartilhar').onclick = function() {
            document.getElementById('modal-compartilhar').style.display = 'flex';
        };
        function fecharModalCompartilhar() {
            document.getElementById('modal-compartilhar').style.display = 'none';
        }
        function copiarLinkGincana() {
            const link = "{{ route('gincana.show', $gincana->id) }}";
            navigator.clipboard.writeText(link);
            alert('Link copiado!');
        }
    </script>

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
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBzEzusC_k3oEoPnqynq2N4a0aA3arzH-c&callback=initMapShow&libraries=geometry"></script>
</div>
@endsection
