@extends('layouts.app')
@section('content')
@section('content')
<div id="form_container" style="max-width: 600px; margin: 24px auto 0 auto; padding: 28px 24px 22px 24px; background: #eafaf1; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.07);">
    <h2 style="margin-bottom: 22px; text-align: center; font-weight: 700; color: #198754; font-size: 2rem; letter-spacing: 0.5px;">Editar Gincana</h2>
    <form id="form-editar-gincana" method="POST" action="{{ route('gincana.update', $gincana->id) }}">
        @csrf
        @method('PUT')
        <!-- Nome da Gincana -->
        <div style="margin-bottom: 16px;">
            <label for="nome" style="display: block; font-weight: bold; margin-bottom: 6px;">Nome da Gincana</label>
            <input type="text" id="nome" name="nome" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;" value="{{ old('nome', $gincana->nome) }}">
        </div>
        <!-- Duração -->
        <div style="margin-bottom: 16px;">
            <label for="duracao" style="display: block; font-weight: bold; margin-bottom: 6px;">Duração (em horas)</label>
            <select id="duracao" name="duracao" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;">
                <option value="">Selecione a duração</option>
                <option value="24" {{ $gincana->duracao == 24 ? 'selected' : '' }}>24 horas</option>
                <option value="48" {{ $gincana->duracao == 48 ? 'selected' : '' }}>48 horas</option>
                <option value="72" {{ $gincana->duracao == 72 ? 'selected' : '' }}>72 horas</option>
            </select>
        </div>
        <!-- Mapa e Street View -->
        <div style="display: flex; gap: 16px; margin-bottom: 16px;">
            <div style="flex: 1;">
                <label style="display: block; font-weight: bold; margin-bottom: 6px;">Escolha o local da gincana</label>
                <div id="map-editar" style="height: 300px; width: 100%; border: 1px solid #ccc; border-radius: 4px;"></div>
                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $gincana->latitude) }}">
                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $gincana->longitude) }}">
            </div>
            <div style="flex: 1;">
                <label style="display: block; font-weight: bold; margin-bottom: 6px;">Street View</label>
                <div id="street-view-editar" style="height: 300px; width: 100%; border: 1px solid #ccc; border-radius: 4px;"></div>
            </div>
        </div>
        <!-- Campo de Cidade -->
        <div style="margin-bottom: 16px;">
            <label for="cidade" style="display: block; font-weight: bold; margin-bottom: 6px;">Cidade / Localização</label>
            <input type="text" id="cidade" name="cidade" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;" value="{{ old('cidade', $gincana->cidade ?? '') }}" placeholder="Digite uma cidade ou endereço" oninput="debouncedBuscarCidadeEditar()">
            <small id="cidade-feedback" style="color: #6c757d; font-size: 12px;"></small>
        </div>
        <!-- Contexto -->
        <div style="margin-bottom: 16px;">
            <label for="contexto" style="display: block; font-weight: bold; margin-bottom: 6px;">Contexto/Dica</label>
            <textarea id="contexto" name="contexto" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; min-height: 80px;" placeholder="Dê uma dica sobre o local ou conte uma história...">{{ old('contexto', $gincana->contexto) }}</textarea>
        </div>
        <!-- Privacidade -->
        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: bold; margin-bottom: 6px;">Privacidade</label>
            <div style="display: flex; gap: 16px;">
                <div>
                    <input type="radio" id="publica" name="privacidade" value="publica" {{ $gincana->privacidade == 'publica' ? 'checked' : '' }}>
                    <label for="publica">Pública (todos podem jogar)</label>
                </div>
                <div>
                    <input type="radio" id="privada" name="privacidade" value="privada" {{ $gincana->privacidade == 'privada' ? 'checked' : '' }}>
                    <label for="privada">Privada (apenas quem tem o link pode jogar)</label>
                </div>
            </div>
        </div>
        <!-- Botões -->
        <div style="display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; margin-top: 18px;">
            <button type="submit" style="padding: 10px 28px; background-color: #198754; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 1.08em;">
                Salvar Alterações
            </button>
            <a href="{{ route('gincana.index') }}" style="padding: 10px 28px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 4px; display: inline-block; font-weight: 600; font-size: 1.08em;">
                Cancelar
            </a>
        </div>

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
    </form>
</div>

<!-- Google Maps Script Editar -->
<script>
    let mapEditar, markerEditar, geocoderEditar, streetViewEditar;

    function initMapEditar() {
        const lat = parseFloat(document.getElementById('latitude').value) || -23.55052;
        const lng = parseFloat(document.getElementById('longitude').value) || -46.633308;
        const initialLocation = { lat: lat, lng: lng };

        mapEditar = new google.maps.Map(document.getElementById('map-editar'), {
            center: initialLocation,
            zoom: 12
        });

        geocoderEditar = new google.maps.Geocoder();

        streetViewEditar = new google.maps.StreetViewPanorama(
            document.getElementById('street-view-editar'), {
                position: initialLocation,
                pov: { heading: 165, pitch: 0 },
                zoom: 1
            }
        );

        markerEditar = new google.maps.Marker({
            position: initialLocation,
            map: mapEditar,
            draggable: true
        });

        function updateLatLngFieldsEditar(position) {
            document.getElementById('latitude').value = position.lat();
            document.getElementById('longitude').value = position.lng();
        }

        updateLatLngFieldsEditar(markerEditar.getPosition());
        atualizarStreetViewEditar(markerEditar.getPosition());

        markerEditar.addListener('dragend', function() {
            updateLatLngFieldsEditar(markerEditar.getPosition());
            atualizarStreetViewEditar(markerEditar.getPosition());
        });

        mapEditar.addListener('click', function(event) {
            markerEditar.setPosition(event.latLng);
            updateLatLngFieldsEditar(event.latLng);
            atualizarStreetViewEditar(event.latLng);
        });
    }

    function atualizarStreetViewEditar(position) {
        streetViewEditar.setPosition(position);
    }

    let buscarCidadeTimeoutEditar;
    function debouncedBuscarCidadeEditar() {
        clearTimeout(buscarCidadeTimeoutEditar);
        buscarCidadeTimeoutEditar = setTimeout(buscarCidadeEditar, 500);
    }

    function buscarCidadeEditar() {
        const endereco = document.getElementById('cidade').value;
        const feedback = document.getElementById('cidade-feedback');
        if (!endereco) {
            feedback.textContent = '';
            return;
        }
        geocoderEditar.geocode({ address: endereco }, function (results, status) {
            if (status === 'OK') {
                const location = results[0].geometry.location;
                mapEditar.panTo(location);
                mapEditar.setZoom(16);
                markerEditar.setPosition(location);
                document.getElementById('latitude').value = location.lat();
                document.getElementById('longitude').value = location.lng();
                feedback.textContent = 'Endereço encontrado: ' + results[0].formatted_address;
                feedback.style.color = '#198754';
                atualizarStreetViewEditar(location);
            } else {
                feedback.textContent = 'Local não encontrado.';
                feedback.style.color = '#dc3545';
            }
        });
    }

    window.initMapEditar = initMapEditar;
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBzEzusC_k3oEoPnqynq2N4a0aA3arzH-c&callback=initMapEditar&libraries=geometry"></script>
@endsection
