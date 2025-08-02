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
    locations = window.gameLocations && window.gameLocations.length > 0 
                ? window.gameLocations 
                : defaultLocations;
    
    console.log('Locais carregados:', locations);
    
    // Verificar se é a primeira visita (ou forçar tutorial)
    const showTutorial = !localStorage.getItem('gincaneiros_tutorial_seen') || window.location.search.includes('tutorial=1');
    
    if (showTutorial) {
        // Marcar tutorial como visto
        localStorage.setItem('gincaneiros_tutorial_seen', 'true');
        
        // Mostrar tutorial explicativo primeiro
        showGameTutorial().then(() => {
            initializeGameComponents();
        });
    } else {
        // Ir direto para o jogo
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
        navigationControl: false
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
            });
            
            // Atualizar interface quando há palpite
            updateMapInterface(true);
        }
    });
}

function initializeStreetView() {
    // Obter uma posição aleatória dos locais disponíveis
    let initialPosition = getRandomValidLocation();
    
    streetView = new google.maps.StreetViewPanorama(
        document.getElementById('streetview'),
        {
            position: initialPosition,
            pov: { 
                heading: Math.random() * 360, // Direção aleatória
                pitch: Math.random() * 20 - 10 // Pitch entre -10 e 10 graus
            },
            zoom: 1,
            disableDefaultUI: true,
            showRoadLabels: false
        }
    );
    
    // Remove o marcador anterior se existir
    if (window.characterMarker) {
        window.characterMarker.setMap(null);
    }

    // Adiciona a figurinha como marcador no panorama (igual ao exemplo)
    window.characterMarker = new google.maps.Marker({
        position: initialPosition,
        map: streetView,  // Usar streetView como no exemplo
        icon: {
            url: "https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExeTRweGJoMHk1eG5nb2tyOHMyMHp1ZGlpYTFoZDZ6Ym9zZ3ZkYXB2MSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/bvQHYGOF8UOXqXSFir/giphy.gif",
            scaledSize: new google.maps.Size(60, 80),
            anchor: new google.maps.Point(30, 80) // Importante: anchor para posicionamento correto
        },
        visible: true
    });

    window.characterMarker.addListener('click', function() {
        showPostModal(currentLocation);
    });
    
    // Adicionar listener para detectar erros de Street View
    streetView.addListener('status_changed', function() {
        if (streetView.getStatus() !== 'OK') {
            console.warn('Street View não disponível para esta localização, tentando outra...');
            // Tentar uma localização alternativa
            const newPosition = getRandomValidLocation();
            streetView.setPosition(newPosition);
            
            // Atualizar posição da figurinha também
            if (window.characterMarker) {
                window.characterMarker.setPosition(newPosition);
            }
        }
    });
}

// Função para obter uma localização válida aleatória
function getRandomValidLocation() {
    let validLocations = [];
    
    if (locations && locations.length > 0) {
        // Filtrar apenas locais válidos (não o marcador no_gincana)
        validLocations = locations.filter(loc => !loc.no_gincana);
    }
    
    // Se não há locais válidos, usar os padrões
    if (validLocations.length === 0) {
        validLocations = defaultLocations;
    }
    
    const randomLocation = validLocations[Math.floor(Math.random() * validLocations.length)];
    console.log('Localização selecionada:', randomLocation.name || 'Local aleatório');
    
    return {
        lat: randomLocation.lat,
        lng: randomLocation.lng
    };
}

function setupEventListeners() {
    document.getElementById('showMapBtn').addEventListener('click', showMap);
    document.getElementById('closeMapBtn').addEventListener('click', hideMap);
    
    // Adicionar listener apenas se o botão existir
    const cancelBtn = document.getElementById('cancelGuessBtn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', hideMap);
    }
    
    document.getElementById('confirmGuessBtn').addEventListener('click', confirmGuess);
    document.getElementById('continueBtn').addEventListener('click', hidePopup);
    document.getElementById('overlay').addEventListener('click', hidePopup);
}

function startNewRound() {
    // Selecionar um local aleatório dos locais disponíveis
    if (locations.length === 0) {
        console.error('Nenhum local disponível para o jogo');
        return;
    }
    
    currentLocation = locations[Math.floor(Math.random() * locations.length)];
    console.log('Local atual:', currentLocation);
    
    // Adicionar delay para evitar muitas requisições seguidas
    setTimeout(() => {
        streetView.setPosition(currentLocation);
        
        // Atualizar posição da figurinha também
        if (window.characterMarker) {
            window.characterMarker.setPosition(currentLocation);
        }
    }, 500); // Delay de 500ms
    
    if (window.userMarker) {
        window.userMarker.setMap(null);
    }
    if (window.correctMarker) {
        window.correctMarker.setMap(null);
    }
    userGuess = null;
    gameActive = true;
    updateUI();
    
    // Reset interface do mapa
    updateMapInterface(false);
}

function updateMapInterface(hasGuess) {
    const confirmBtn = document.getElementById('confirmGuessBtn');
    const instructions = document.getElementById('mapInstructions');
    
    // Verificar se os elementos existem antes de tentar manipulá-los
    if (!confirmBtn || !instructions) {
        return;
    }
    
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
    // Reset interface state
    updateMapInterface(!!userGuess);
}

function hideMap() {
    document.getElementById('mapSlider').classList.remove('active');
}

function closeMapSlider() {
    document.getElementById('mapSlider').classList.remove('active');
}
function confirmGuess() {
    if (!userGuess) {
        Swal.fire({
            icon: 'warning',
            title: 'Atenção!',
            text: 'Por favor, clique no mapa para fazer seu palpite!',
            confirmButtonColor: '#007bff'
        });
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
        
        // Salvar pontuação no banco de dados
        saveScoreToDatabase(score, currentLocation);
        
        endRound(true);
        
        // Mostrar SweetAlert com botão "Novo Jogo" e reload da página
        Swal.fire({
            icon: icon,
            title: title,
            text: message,
            confirmButtonText: 'Novo Jogo',
            confirmButtonColor: '#007bff',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                closeMapSlider();
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
                case 'Norte':
                    directionImg = 'https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExNGt2MXQzdmY3eHFrYzNkcjNmcnhhbWl5emlzYjNibnR5ZmEwc2locyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/7aFMOMlY5HgQnCcK5n/giphy.gif'; // seta norte
                    break;
                case 'Sul':
                    directionImg = 'https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExeWwxbW12a2ExZTByd2x4aGxxdjhrbjJxdmJhZDJkZ2x3aW12czY2aiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/jfxNsKqJLIc3Kw1nZz/giphy.gif'; // seta sul
                    break;
                case 'Leste':
                    directionImg = 'https://media0.giphy.com/media/v1.Y2lkPTc5MGI3NjExZmlwZXAydHE4cmszOHlpdDBudnd4OTd6cW4ybjhrODVzMW5tMGQxZCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/A0Mmm3WcsQVrMlYjlY/giphy.gif'; // seta leste
                    break;
                case 'Oeste':
                    directionImg = 'https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExeWd5aW5nbnpreWNlcXh6MHk1aDVtMWJkdTJoOW15bm1ycHcwb2swciZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/CGTFfpxHMpxCJk1eGR/giphy.gif'; // seta oeste
                    break;
            }
            message += `\n\n vá mais para o ${direction}.`;
            message += `\n\n Falta(m) ${attempts} tentativa(s). Dê zoom para facilitar a localização.`;
            message += `\n\n Sua pontuação atual está em ${score} pts, cada erro perde 200 pts.`;
            Swal.fire({
                title: title,
                text: message.replace(/\n/g, ' '),
                imageUrl: directionImg,
                imageWidth: 150,
                imageHeight: 150,
                imageAlt: direction,
                confirmButtonColor: '#007bff',
                confirmButtonText: 'Continuar',
                allowOutsideClick: false
            });
        } else {
            title = 'Fim de Jogo';
            message += `\n\nSuas tentativas acabaram!`;
            message += `\n\nA localização era: ${currentLocation.name}`;
            message += `\n\nPontuação final: ${score} pontos`;
            
            // Salvar pontuação no banco de dados
            saveScoreToDatabase(score, currentLocation);
            
            endRound(false);
            
            // Mostrar SweetAlert com botão "Novo Jogo" e reload da página
            Swal.fire({
                icon: icon,
                title: title,
                text: message,
                confirmButtonText: 'Novo Jogo',
                confirmButtonColor: '#007bff',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    closeMapSlider();
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
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
             Math.cos(pos1.lat * Math.PI / 180) * Math.cos(pos2.lat * Math.PI / 180) *
             Math.sin(dLng/2) * Math.sin(dLng/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}
function getDirection(target, guess) {
    const dLat = target.lat - guess.lat;
    const dLng = target.lng - guess.lng;
    if (Math.abs(dLat) > Math.abs(dLng)) {
        return dLat > 0 ? 'Norte' : 'Sul';
    } else {
        return dLng > 0 ? 'Leste' : 'Oeste';
    }
}
function endRound(won) {
    gameActive = false;
    window.correctMarker = new google.maps.Marker({
        position: currentLocation,
        map: map,
        title: 'Localização correta',
        icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
    });
}
function newGame() {
    score = 1000;
    attempts = 5;
    round++;
    gameActive = true;
    hidePopup();
    startNewRound();
}
window.showHelpMessage = function() {
    const messages = [
        "Ajude-me a encontrar onde estou no mapa! 🗺️",
        "Estou perdido! Você pode me ajudar a descobrir minha localização? 🤔",
        "Olhe ao redor e tente descobrir onde estou! 🔍",
        "Use as pistas visuais para me encontrar no mapa! 👀",
        "Preciso da sua ajuda para descobrir onde estou! 🆘"
    ];
    const randomMessage = messages[Math.floor(Math.random() * messages.length)];
    Swal.fire({
        icon: 'question',
        title: 'Preciso de Ajuda! 🤔',
        text: randomMessage,
        confirmButtonText: 'Vou te ajudar!',
        confirmButtonColor: '#28a745',
        background: '#fff',
        iconColor: '#ffc107'
    });
}
function hidePopup() {
    console.log('hidePopup chamada');
}
function updateUI() {
    document.getElementById('score').textContent = score;
    document.getElementById('attempts').textContent = attempts;
    document.getElementById('round').textContent = round;
}
// window.onload = function() {
//     if (typeof google !== 'undefined') {
//         initGame();
//     }
// };

window.addEventListener("load", () => {
    // Verificar primeiro se há gincanas disponíveis
    const gameLocations = window.gameLocations || [];
    
    if (gameLocations.length === 1 && gameLocations[0].no_gincana) {
        // Se não há gincanas, mostrar alerta diretamente
        showNoGincanaAlert();
        return;
    }
    
    // Se há gincanas, aguardar carregamento do Google Maps
    const waitForGoogle = setInterval(() => {
        if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
            clearInterval(waitForGoogle);
            
            // Adicionar tratamento de erro global para Google Maps
            window.gm_authFailure = function() {
                console.error('Falha na autenticação da Google Maps API');
                Swal.fire({
                    icon: 'error',
                    title: 'Erro na API do Google Maps',
                    text: 'Problema de autenticação ou limite de requisições. Tente novamente em alguns minutos.',
                    confirmButtonColor: '#d33'
                });
            };
            
            initGame();
        }
    }, 100);
});

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
        title: "Sweet!",
        text: "Modal with a custom image.",
        imageUrl: "https://unsplash.it/400/200",
        imageWidth: 400,
        imageHeight: 200,
        imageAlt: "Custom image"
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
            loadComments(location.gincana_id || location.id);
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

// Função para adicionar comentário
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
