// Scripts do jogo extraídos do welcome.blade.php
let map, streetView, currentLocation, userGuess;
let score = 1000;
let attempts = 5;
let round = 1;
let gameActive = true;
let locations = [];

// Locais padrão caso não haja dados no backend
const defaultLocations = [
    { lat: -12.9714, lng: -38.5014, name: "Salvador, BA" }
];

window.initGame = function() {
    // Usar locais do backend ou locais padrão
    // A variável window.gameLocations é definida no welcome.blade.php
    locations = window.gameLocations && window.gameLocations.length > 0 
                ? window.gameLocations 
                : defaultLocations;
    
    console.log('Gincaneiros.js: Locais carregados para o jogo:', locations);
    
    // Verificar se é a primeira visita (ou forçar tutorial)
    const showTutorial = !localStorage.getItem('gincaneiros_tutorial_seen') || window.location.search.includes('tutorial=1');
    
    if (showTutorial) {
        localStorage.setItem('gincaneiros_tutorial_seen', 'true');
        showGameTutorial().then(() => {
            initializeGameComponents();
        });
    } else {
        initializeGameComponents();
    }
}

// Função auxiliar para inicializar componentes do jogo
function initializeGameComponents() {
    initializeMap();
    initializeStreetView();
    setupEventListeners();
    startNewRound();
}

function initializeMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 4,
        center: { lat: -14.2350, lng: -51.9253 },
        mapTypeId: 'roadmap',
        zoomControl: true,
        streetViewControl: false,
        mapTypeControl: false,
        fullscreenControl: false,
        rotateControl: false,
        scaleControl: false,
        panControl: false,
        gestureHandling: 'greedy'
    });
    map.addListener('click', function(event) {
        if (gameActive) {
            userGuess = {
                lat: event.latLng.lat(),
                lng: event.latLng.lng()
            };
            if (window.userMarker) {
                window.userMarker.setMap(null);
            }
            window.userMarker = new google.maps.Marker({
                position: userGuess,
                map: map,
                title: 'Seu palpite',
                icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
            } );
            updateMapInterface(true);
        }
    });
}

function initializeStreetView() {
    const cameraOffset = 0.00025;
    let characterPosition = getRandomValidLocation();
    let cameraPosition = {
        lat: characterPosition.lat - cameraOffset,
        lng: characterPosition.lng
    };

    function calculateHeading(from, to) {
        const dLng = to.lng - from.lng;
        const dLat = to.lat - from.lat;
        const angle = Math.atan2(dLng, dLat) * 180 / Math.PI;
        return (angle + 360) % 360;
    }

    const heading = calculateHeading(cameraPosition, characterPosition);

    streetView = new google.maps.StreetViewPanorama(
        document.getElementById('streetview'),
        {
            position: cameraPosition,
            pov: { heading: heading, pitch: 0 },
            zoom: 1,
            disableDefaultUI: true,
            showRoadLabels: false,
            motionTracking: false
        }
    );
    
    if (window.characterMarker) {
        window.characterMarker.setMap(null);
    }

    window.characterMarker = new google.maps.Marker({
        position: characterPosition,
        map: streetView,
        icon: {
            url: "https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExeTRweGJoMHk1eG5nb2tyOHMyMHp1ZGlpYTFoZDZ6Ym9zZ3ZkYXB2MSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/bvQHYGOF8UOXqXSFir/giphy.gif",
            scaledSize: new google.maps.Size(60, 80 ),
            anchor: new google.maps.Point(30, 80)
        },
        visible: true
    });

    window.characterMarker.addListener('click', function() {
        showPostModal(currentLocation);
    });
    
    streetView.addListener('status_changed', function() {
        if (streetView.getStatus() !== 'OK') {
            console.warn('Street View não disponível, tentando outra localização...');
            const newPosition = getRandomValidLocation();
            streetView.setPosition(newPosition);
            if (window.characterMarker) {
                window.characterMarker.setPosition(newPosition);
            }
        }
    });
}

function getRandomValidLocation() {
    let validLocations = [];
    if (locations && locations.length > 0) {
        validLocations = locations.filter(loc => !loc.no_gincana);
    }
    if (validLocations.length === 0) {
        validLocations = defaultLocations;
    }
    const randomLocation = validLocations[Math.floor(Math.random() * validLocations.length)];
    console.log('Gincaneiros.js: Localização selecionada:', randomLocation.name || 'Local aleatório');
    return { lat: randomLocation.lat, lng: randomLocation.lng };
}

function setupEventListeners() {
    document.getElementById('showMapBtn').addEventListener('click', showMap);
    document.getElementById('closeMapBtn').addEventListener('click', hideMap);
    document.getElementById('confirmGuessBtn').addEventListener('click', confirmGuess);
    document.getElementById('continueBtn').addEventListener('click', hidePopup);
    document.getElementById('overlay').addEventListener('click', hidePopup);
}

function startNewRound() {
    if (locations.length === 0) {
        console.error('Nenhum local disponível para o jogo');
        return;
    }
    currentLocation = locations[Math.floor(Math.random() * locations.length)];
    console.log('Gincaneiros.js: Local atual da rodada:', currentLocation);
    
    setTimeout(() => {
        const cameraOffset = 0.00025;
        let characterPosition = { lat: currentLocation.lat, lng: currentLocation.lng };
        let cameraPosition = { lat: characterPosition.lat - cameraOffset, lng: characterPosition.lng };

        function calculateHeading(from, to) {
            const dLng = to.lng - from.lng;
            const dLat = to.lat - from.lat;
            const angle = Math.atan2(dLng, dLat) * 180 / Math.PI;
            return (angle + 360) % 360;
        }
        const heading = calculateHeading(cameraPosition, characterPosition);
        streetView.setPosition(cameraPosition);
        streetView.setPov({ heading: heading, pitch: 0 });
        if (window.characterMarker) {
            window.characterMarker.setPosition(characterPosition);
        }
    }, 500);
    
    if (window.userMarker) window.userMarker.setMap(null);
    if (window.correctMarker) window.correctMarker.setMap(null);
    userGuess = null;
    gameActive = true;
    updateUI();
    updateMapInterface(false);
}

function updateMapInterface(hasGuess) {
    const confirmBtn = document.getElementById('confirmGuessBtn');
    const instructions = document.getElementById('mapInstructions');
    if (!confirmBtn || !instructions) return;
    
    if (hasGuess) {
        confirmBtn.disabled = false;
        confirmBtn.classList.add('has-guess');
        confirmBtn.textContent = '🎯 Confirmar Palpite';
        instructions.classList.add('hidden');
    } else {
        confirmBtn.disabled = true;
        confirmBtn.classList.remove('has-guess');
        confirmBtn.textContent = '🎯 Clique no mapa primeiro';
        instructions.classList.remove('hidden');
    }
}

function showMap() {
    document.getElementById('mapSlider').classList.add('active');
    updateMapInterface(!!userGuess);
}

function hideMap() {
    document.getElementById('mapSlider').classList.remove('active');
}

function confirmGuess() {
    if (!userGuess) {
        Swal.fire({ icon: 'warning', title: 'Atenção!', text: 'Por favor, clique no mapa para fazer seu palpite!', confirmButtonColor: '#007bff' });
        return;
    }
    
    const distance = calculateDistance(currentLocation, userGuess);
    attempts--;
    let message = `Você está à ${distance.toFixed(2)} km do local, `;
    let title = `Siga nessa direção ...`;
    let icon = 'info';
    
    if (distance <= 10) {
        title = 'Parabéns! 🎉';
        icon = 'success';
        message += `\n\nVocê acertou! A localização era: ${currentLocation.name}`;
        message += `\n\nPontuação final: ${score} pontos`;
        saveScoreToDatabase(score, currentLocation);
        endRound(true);
        Swal.fire({ icon: icon, title: title, text: message, confirmButtonText: 'Novo Jogo', confirmButtonColor: '#007bff', allowOutsideClick: false }).then((result) => {
            if (result.isConfirmed) {
                hideMap();
                location.reload();
            }
        });
    } else {
        score = Math.max(0, score - 200);
        icon = 'error';
        if (attempts > 0) {
            const direction = getDirection(currentLocation, userGuess);
            let directionImg = '';
            switch (direction) {
                case 'Norte': directionImg = 'https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExNGt2MXQzdmY3eHFrYzNkcjNmcnhhbWl5emlzYjNibnR5ZmEwc2locyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/7aFMOMlY5HgQnCcK5n/giphy.gif'; break;
                case 'Sul': directionImg = 'https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExeWwxbW12a2ExZTByd2x4aGxxdjhrbjJxdmJhZDJkZ2x3aW12czY2aiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/jfxNsKqJLIc3Kw1nZz/giphy.gif'; break;
                case 'Leste': directionImg = 'https://media0.giphy.com/media/v1.Y2lkPTc5MGI3NjExZmlwZXAydHE4cmszOHlpdDBudnd4OTd6cW4ybjhrODVzMW5tMGQxZCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/A0Mmm3WcsQVrMlYjlY/giphy.gif'; break;
                case 'Oeste': directionImg = 'https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExeWd5aW5nbnpreWNlcXh6MHk1aDVtMWJkdTJoOW15bm1ycHcwb2swciZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/CGTFfpxHMpxCJk1eGR/giphy.gif'; break;
            }
            message += `\n\n vá mais para o ${direction}.`;
            message += `\n\n Falta(m ) ${attempts} tentativa(s).`;
            message += `\n\n Pontuação atual: ${score} pts.`;
            Swal.fire({ title: title, text: message.replace(/\n/g, ' '), imageUrl: directionImg, imageWidth: 150, imageHeight: 150, imageAlt: direction, confirmButtonColor: '#007bff', confirmButtonText: 'Continuar', allowOutsideClick: false });
        } else {
            title = 'Fim de Jogo';
            message += `\n\nSuas tentativas acabaram!`;
            message += `\n\nA localização era: ${currentLocation.name}`;
            message += `\n\nPontuação final: ${score} pontos`;
            saveScoreToDatabase(score, currentLocation);
            endRound(false);
            Swal.fire({ icon: icon, title: title, text: message, confirmButtonText: 'Novo Jogo', confirmButtonColor: '#007bff', allowOutsideClick: false }).then((result) => {
                if (result.isConfirmed) {
                    hideMap();
                    location.reload();
                }
            });
        }
    }
    updateUI();
}

function calculateDistance(pos1, pos2) {
    const R = 6371;
    const dLat = (pos2.lat - pos1.lat) * Math.PI / 180;
    const dLng = (pos2.lng - pos1.lng) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(pos1.lat * Math.PI / 180) * Math.cos(pos2.lat * Math.PI / 180) * Math.sin(dLng/2) * Math.sin(dLng/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

function getDirection(target, guess) {
    const dLat = target.lat - guess.lat;
    const dLng = target.lng - guess.lng;
    return Math.abs(dLat) > Math.abs(dLng) ? (dLat > 0 ? 'Norte' : 'Sul') : (dLng > 0 ? 'Leste' : 'Oeste');
}

function endRound(won) {
    gameActive = false;
    window.correctMarker = new google.maps.Marker({ position: currentLocation, map: map, title: 'Localização correta', icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png' } );
}

function updateUI() {
    // Apenas atualiza se os elementos existirem (para não dar erro para visitantes)
    if (document.getElementById('score')) document.getElementById('score').textContent = score;
    if (document.getElementById('attempts')) document.getElementById('attempts').textContent = attempts;
    if (document.getElementById('round')) document.getElementById('round').textContent = round;
}

function hidePopup() { console.log('hidePopup chamada'); }

// ########## INÍCIO DA CORREÇÃO ##########

// Listener de evento que espera a estrutura da página (DOM) estar pronta.
// Isso garante que a tag <script> no welcome.blade.php já definiu window.gameLocations.
document.addEventListener('DOMContentLoaded', () => {
    // Verificar se a variável global com os locais foi definida pelo Laravel.
    if (typeof window.gameLocations === 'undefined') {
        console.error('Gincaneiros.js: A variável window.gameLocations não foi encontrada. Verifique o welcome.blade.php');
        return; // Interrompe a execução se os dados não estiverem disponíveis.
    }

    // Verificar se há gincanas disponíveis
    if (window.gameLocations.length === 1 && window.gameLocations[0].no_gincana) {
        showNoGincanaAlert();
        return;
    }
    
    // Agora que temos os dados, esperamos a API do Google Maps carregar.
    // A API é carregada por uma tag <script> no layouts/app.blade.php.
    // A função initGame() agora é chamada globalmente pelo callback da API do Google.
    // Não precisamos mais do setInterval. A função initGame se torna o ponto de entrada.
});

// A função initGame já existe e é chamada pelo callback da API do Google Maps.
// O importante é que o script da API do Google seja carregado em todas as páginas.
// Verifique se a linha abaixo está no seu layouts/app.blade.php, fora de qualquer condicional.
// <script async src="https://maps.googleapis.com/maps/api/js?key=SUA_CHAVE_API&callback=initGame"></script>

// (O restante do seu código, como saveScoreToDatabase, showNoGincanaAlert, etc., permanece igual )
// ... (código omitido para brevidade) ...

// ########## FIM DA CORREÇÃO ##########
// Função para salvar pontuação no banco de dados
async function saveScoreToDatabase(pontuacao, location) {
    try {
        // Verificar se o usuário está logado (se existe um token CSRF)
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.log('Usuário não logado - pontuação não será salva');
            return;
        }

        const response = await fetch('/game/save-score', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                gincana_id: location.gincana_id || null, // ID da gincana se estiver jogando uma específica
                pontuacao: pontuacao,
                tempo_total_segundos: null, // Podemos implementar timer depois
                locais_visitados: 1,
                latitude: location.lat,
                longitude: location.lng
            })
        });

        const data = await response.json();
        
        if (data.success) {
            console.log('Pontuação salva com sucesso!', data);
        } else {
            console.error('Erro ao salvar pontuação:', data);
        }
    } catch (error) {
        console.error('Erro na requisição para salvar pontuação:', error);
    }
}

// Função para mostrar alerta quando não há gincanas disponíveis
function showNoGincanaAlert() {
    // Ocultar elementos do jogo
    const gameContainer = document.querySelector('.game-container');
    if (gameContainer) {
        gameContainer.style.display = 'none';
    }
    
    // Verificar se o usuário está autenticado
    const isAuthenticated = window.isAuthenticated || false;
    
    if (isAuthenticated) {
        // Usuário logado - mostrar opção de criar gincana
        Swal.fire({
            title: '🎯 Nenhuma Gincana Disponível',
            text: 'Não há gincanas públicas criadas ainda. Que tal ser o primeiro a criar uma?',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '🎮 Criar Minha Gincana',
            cancelButtonText: 'Cancelar',
            background: '#fff',
            allowOutsideClick: false,
            backdrop: `
                rgba(0,0,123,0.4)
                left top
                no-repeat
            `
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirecionar para a página de criação de gincana
                window.location.href = '/gincana/create';
            } else {
                // Se cancelar, mostrar container do jogo novamente
                if (gameContainer) {
                    gameContainer.style.display = 'block';
                }
            }
        });
    } else {
        // Visitante - informar sobre login
        Swal.fire({
            title: '🎯 Nenhuma Gincana Disponível',
            text: 'Não há gincanas públicas criadas ainda. Faça login para criar sua própria gincana!',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '🔐 Fazer Login',
            cancelButtonText: 'Ok',
            background: '#fff',
            allowOutsideClick: false,
            backdrop: `
                rgba(0,0,123,0.4)
                left top
                no-repeat
            `
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirecionar para a página de login
                window.location.href = '/login';
            } else {
                // Se cancelar, mostrar container do jogo novamente
                if (gameContainer) {
                    gameContainer.style.display = 'block';
                }
            }
        });
    }
}

// Função para mostrar tutorial explicativo do jogo
function showGameTutorial() {
    return Swal.fire({
        title: "Vc é bom de investigação?",
        text: " Vamos testar suas habilidades e ver se você consegue indentificar este local! 🕵️‍♂️",
        imageUrl: "https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExOG4wcTBrN2hpaDlhaGl0MHVnNDk5ZGo3ODNsa3V2YzI5c3R1aWdsNSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/l378tV67u1QQkSqFq/giphy.gif",
        imageWidth: 400,
        imageHeight: 200,
        imageAlt: "Custom image",
        confirmButtonText: "Vamos lá! 🚀"
    });
}

// Nova função para mostrar modal com comentários
function showPostModal(location) {
    const isAuthenticated = window.isAuthenticated || false;
    
    Swal.fire({
        title: location.name || 'Local Misterioso',
        html: `
            <div class="post-content" style="text-align: left;">
                <div class="post-inicial" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <h4 style="margin: 0 0 10px 0; color: #495057;">📍 Dica do Local:</h4>
                    <p style="margin: 0; color: #6c757d; font-style: italic;">"${location.contexto || 'Descubra onde estou!'}"</p>
                </div>
                
                <div class="comments-section">
                    <h4 style="margin: 0 0 15px 0; color: #495057;">💬 Comentários da Comunidade</h4>
                    <div id="comments-list" style="max-height: 300px; overflow-y: auto; margin-bottom: 15px;">
                        <div style="text-align: center; color: #6c757d;">
                            <i class="fas fa-spinner fa-spin"></i> Carregando comentários...
                        </div>
                    </div>
                    
                    ${isAuthenticated ? `
                        <div class="add-comment" style="border-top: 1px solid #dee2e6; padding-top: 15px;">
                            <textarea id="new-comment" placeholder="Compartilhe sua experiência sobre este local..." 
                                style="width: 100%; height: 80px; padding: 10px; border: 1px solid #ced4da; border-radius: 4px; resize: vertical; font-family: inherit;"></textarea>
                            <button onclick="addComment(${location.gincana_id || location.id})" 
                                style="margin-top: 10px; background: #007bff; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">
                                💬 Comentar
                            </button>
                        </div>
                    ` : `
                        <div style="text-align: center; padding: 15px; background: #e9ecef; border-radius: 4px;">
                            <p style="margin: 0; color: #6c757d;">
                                🔐 Faça login  para comentar e interagir com a comunidade!
                            </p>
                        </div>
                    `}
                </div>
            </div>
        `,
        width: 600,
        showCloseButton: true,
        showConfirmButton: false,
        didOpen: () => {
            // Só carrega comentários se o usuário estiver autenticado
            if (window.isAuthenticated) {
                loadComments(location.gincana_id || location.id);
            }
        }
    });
}

// Função para carregar comentários
async function loadComments(gincanaId) {
    try {
        console.log('Carregando comentários para gincana_id:', gincanaId);
        
        const response = await fetch(`/comentarios/${gincanaId}`);
        
        // Verificar se a resposta é OK
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Verificar se é JSON válido
        const textResponse = await response.text();
        console.log('Resposta do servidor:', textResponse.substring(0, 500)); // Log dos primeiros 500 caracteres
        
        let comentarios;
        try {
            comentarios = JSON.parse(textResponse);
        } catch (jsonError) {
            console.error('Erro ao parsear JSON:', jsonError);
            console.error('Resposta completa:', textResponse);
            throw new Error('Resposta não é um JSON válido');
        }
        
        const commentsList = document.getElementById('comments-list');
        if (!commentsList) return;
        
        if (comentarios.length === 0) {
            commentsList.innerHTML = `
                <div style="text-align: center; color: #6c757d; padding: 20px;">
                    🤔 Seja o primeiro a comentar sobre este local!
                </div>
            `;
            return;
        }
        
        commentsList.innerHTML = comentarios.map(comentario => `
            <div class="comment" style="border-bottom: 1px solid #eee; padding: 12px 0;">
                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                    <strong style="color: #495057; font-size: 14px;">${comentario.user.name}</strong>
                    <small style="color: #6c757d; margin-left: 10px;">${formatDate(comentario.created_at)}</small>
                </div>
                <p style="margin: 0; color: #495057; line-height: 1.4; font-size: 14px;">${comentario.conteudo}</p>
            </div>
        `).join('');
        
    } catch (error) {
        console.error('Erro ao carregar comentários:', error);
        document.getElementById('comments-list').innerHTML = `
            <div style="text-align: center; color: #dc3545;">
                ❌ Erro ao carregar comentários: ${error.message}
            </div>
        `;
    }
}

//Função para adicionar comentário
window.addComment = async function(gincanaId) {
    const textarea = document.getElementById('new-comment');
    const conteudo = textarea.value.trim();
    
    if (!conteudo) {
        Swal.fire({
            icon: 'warning',
            title: 'Atenção',
            text: 'Digite seu comentário primeiro!',
            confirmButtonColor: '#007bff'
        });
        return;
    }
    
    try {
        console.log('Enviando comentário para gincana_id:', gincanaId);
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        const response = await fetch('/comentarios', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                gincana_id: gincanaId,
                conteudo: conteudo
            })
        });
        
        // Verificar se a resposta é OK
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Verificar se é JSON válido
        const textResponse = await response.text();
        console.log('Resposta do servidor (comentário):', textResponse.substring(0, 500));
        
        let data;
        try {
            data = JSON.parse(textResponse);
        } catch (jsonError) {
            console.error('Erro ao parsear JSON:', jsonError);
            console.error('Resposta completa:', textResponse);
            throw new Error('Resposta não é um JSON válido');
        }
        
        if (data.success) {
            textarea.value = '';
            loadComments(gincanaId);
            
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Comentário adicionado!',
                showConfirmButton: false,
                timer: 2000
            });
        } else {
            throw new Error(data.message || 'Erro ao adicionar comentário');
        }
        
    } catch (error) {
        console.error('Erro ao adicionar comentário:', error);
        Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: `Não foi possível adicionar seu comentário: ${error.message}`,
            confirmButtonColor: '#dc3545'
        });
    }
}

// // Função para adicionar comentário
// window.addComment = async function(gincanaId) {
//     const textarea = document.getElementById('new-comment');
//     const conteudo = textarea.value.trim();
    
//     if (!conteudo) {
//         Swal.fire({
//             icon: 'warning',
//             title: 'Atenção',
//             text: 'Digite seu comentário primeiro!',
//             confirmButtonColor: '#007bff'
//         });
//         return;
//     }
    
//     try {
//         console.log('Enviando comentário para gincana_id:', gincanaId);
        
//         const csrfToken = document.querySelector('meta[name="csrf-token"]');
//         const response = await fetch('/comentarios', {
//             method: 'POST',
//             headers: {
//                 'Content-Type': 'application/json',
//                 'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
//                 'Accept': 'application/json'
//             },
//             body: JSON.stringify({
//                 gincana_id: gincanaId,
//                 conteudo: conteudo
//             })
//         });
        
//         // Verificar se a resposta é OK (status 2xx)
//         if (!response.ok) {
//             // Se a resposta não for OK, mas o comentário foi salvo,
//             // vamos tratar como sucesso no front-end.
//             // O erro de JSON será pego no catch abaixo.
//             throw new Error(`HTTP error! status: ${response.status}`);
//         }
        
//         const textResponse = await response.text();
//         const data = JSON.parse(textResponse); // Esta linha pode falhar e ir para o catch
        
//         if (data.success) {
//             // Bloco de sucesso original (quando o servidor retorna JSON)
//             textarea.value = '';
//             loadComments(gincanaId);
            
//             Swal.fire({
//                 toast: true,
//                 position: 'top-end',
//                 icon: 'success',
//                 title: 'Comentário adicionado!',
//                 showConfirmButton: false,
//                 timer: 2000
//             });
//         } else {
//             throw new Error(data.message || 'Erro ao adicionar comentário');
//         }
        
//     } catch (error) {
//         // ########## INÍCIO DA MODIFICAÇÃO ##########
//         // ESTE BLOCO AGORA VAI TRATAR O ERRO COMO SUCESSO
        
//         console.warn('Ocorreu um erro esperado no servidor após salvar o comentário. Tratando como sucesso no front-end.', error);
        
//         // 1. Limpar a caixa de texto
//         textarea.value = '';
        
//         // 2. Recarregar a lista de comentários para mostrar o novo
//         loadComments(gincanaId);
        
//         // 3. Mostrar o pop-up de SUCESSO
//         Swal.fire({
//             toast: true,
//             position: 'top-end',
//             icon: 'success',
//             title: 'Comentário adicionado!',
//             showConfirmButton: false,
//             timer: 2000
//         });
        
//         // ########## FIM DA MODIFICAÇÃO ##########
//     }
// }

// Função para formatar data
function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInHours = (now - date) / (1000 * 60 * 60);
    
    if (diffInHours < 1) {
        return 'Agora mesmo';
    } else if (diffInHours < 24) {
        return `${Math.floor(diffInHours)}h atrás`;
    } else {
        return date.toLocaleDateString('pt-BR');
    }
}
