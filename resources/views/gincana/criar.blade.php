@extends('layouts.app')
@section('content')
<div id="form_container" style="max-width: 600px; margin: 24px auto 0 auto; padding: 28px 24px 22px 24px; background: #eafaf1; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.07);">
    <h2 style="margin-bottom: 22px; text-align: center; font-weight: 700; color: #198754; font-size: 2rem; letter-spacing: 0.5px;">Criar Gincana</h2>
    <form id="form-criar-gincana" method="POST" action="{{ route('gincana.store') }}">
        @csrf

        <!-- Nome da Gincana -->
        <div style="margin-bottom: 16px;">
            <label for="nome" style="display: block; font-weight: bold; margin-bottom: 6px;">Nome da Gincana</label>
            <input type="text" id="nome" name="nome" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;" placeholder="Ex: Mistério do Centro Histórico">
        </div>

        <!-- Duração -->
        <div style="margin-bottom: 16px;">
            <label for="duracao" style="display: block; font-weight: bold; margin-bottom: 6px;">Duração (em horas)</label>
            <select id="duracao" name="duracao" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;">
                <option value="">Selecione a duração</option>
                <option value="24">24 horas</option>
                <option value="48">48 horas</option>
                <option value="72">72 horas</option>
            </select>
        </div>

        <!-- Mapa e Street View -->
        <div style="display: flex; gap: 16px; margin-bottom: 16px;">
            <div style="flex: 1;">
                <label style="display: block; font-weight: bold; margin-bottom: 6px;">Escolha o local da gincana</label>
                <div id="map-criar" style="height: 300px; width: 100%; border: 1px solid #ccc; border-radius: 4px;"></div>
                <input type="hidden" id="latitude" name="latitude">
                <input type="hidden" id="longitude" name="longitude">
            </div>
            <div style="flex: 1;">
                <label style="display: block; font-weight: bold; margin-bottom: 6px;">Street View</label>
                <div id="street-view" style="height: 300px; width: 100%; border: 1px solid #ccc; border-radius: 4px;"></div>
            </div>
        </div>

        <!-- Campo de Cidade -->
        <div style="margin-bottom: 16px;">
            <label for="cidade" style="display: block; font-weight: bold; margin-bottom: 6px;">Cidade / Localização</label>
            <input type="text" id="cidade" name="cidade" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;" placeholder="Digite uma cidade ou endereço" oninput="debouncedBuscarCidade()">
            <small id="cidade-feedback" style="color: #6c757d; font-size: 12px;"></small>
        </div>

        <!-- Contexto -->
        <div style="margin-bottom: 16px;">
            <label for="contexto" style="display: block; font-weight: bold; margin-bottom: 6px;">Contexto/Dica</label>
            <textarea id="contexto" name="contexto" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; min-height: 80px;" placeholder="Dê uma dica sobre o local ou conte uma história..."></textarea>
        </div>

        <!-- Privacidade -->
        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: bold; margin-bottom: 6px;">Privacidade</label>
            <div style="display: flex; gap: 16px;">
                <div>
                    <input type="radio" id="publica" name="privacidade" value="publica" checked>
                    <label for="publica">Pública (todos podem jogar)</label>
                </div>
                <div>
                    <input type="radio" id="privada" name="privacidade" value="privada">
                    <label for="privada">Privada (apenas quem tem o link pode jogar)</label>
                </div>
            </div>
        </div>

        <!-- Botões -->
        <div style="display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; margin-top: 18px;">
            <button type="submit" style="padding: 10px 28px; background-color: #198754; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 1.08em;">
                Salvar Gincana
            </button>
            <a href="{{ route('gincana.index') }}" style="padding: 10px 28px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 4px; display: inline-block; font-weight: 600; font-size: 1.08em;">
                Cancelar
            </a>
        </div>
    </form>
</div>

<!-- Google Maps Script -->
<script>
    let map, marker, geocoder, streetView;

    function initMap() {
        const defaultLocation = { lat: -23.55052, lng: -46.633308 }; // São Paulo como exemplo
        
        // Inicializar mapa
        map = new google.maps.Map(document.getElementById('map-criar'), {
            center: defaultLocation,
            zoom: 12
        });

        // Inicializar geocoder
        geocoder = new google.maps.Geocoder();

        // Inicializar Street View
        streetView = new google.maps.StreetViewPanorama(
            document.getElementById('street-view'), {
                position: defaultLocation,
                pov: { heading: 165, pitch: 0 },
                zoom: 1
            }
        );

        // Cria o marcador no mapa
        marker = new google.maps.Marker({
            position: defaultLocation,
            map: map,
            draggable: true
        });

        function updateLatLngFields(position) {
            document.getElementById('latitude').value = position.lat();
            document.getElementById('longitude').value = position.lng();
        }

        updateLatLngFields(marker.getPosition());
        atualizarStreetView(marker.getPosition());

        marker.addListener('dragend', function() {
            updateLatLngFields(marker.getPosition());
            atualizarStreetView(marker.getPosition());
        });

        map.addListener('click', function(event) {
            marker.setPosition(event.latLng);
            updateLatLngFields(event.latLng);
            atualizarStreetView(event.latLng);
        });
    }

    function atualizarStreetView(position) {
        streetView.setPosition(position);
    }

    let buscarCidadeTimeout;
    function debouncedBuscarCidade() {
        clearTimeout(buscarCidadeTimeout);
        buscarCidadeTimeout = setTimeout(buscarCidade, 500);
    }

    function buscarCidade() {
        const endereco = document.getElementById('cidade').value;
        const feedback = document.getElementById('cidade-feedback');
        
        if (!endereco) {
            feedback.textContent = '';
            return;
        }
        
        geocoder.geocode({ address: endereco }, function (results, status) {
            if (status === 'OK') {
                const location = results[0].geometry.location;
                map.panTo(location);
                map.setZoom(16);
                marker.setPosition(location);
                document.getElementById('latitude').value = location.lat();
                document.getElementById('longitude').value = location.lng();
                feedback.textContent = 'Endereço encontrado: ' + results[0].formatted_address;
                feedback.style.color = '#198754';
                atualizarStreetView(location);
            } else {
                feedback.textContent = 'Local não encontrado.';
                feedback.style.color = '#dc3545';
            }
        });
    }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBzEzusC_k3oEoPnqynq2N4a0aA3arzH-c&callback=initMap&libraries=geometry"></script>
@endsection
