@if (Route::has('login'))
    <nav class="flex items-center justify-between p-4 bg-white shadow-md z-50 relative">
        <!-- Logo -->
        <div class="flex items-center">
            <a href="{{ url('/') }}" class="text-xl font-bold text-blue-600 hover:underline">
            Gincaneiros
            </a>
        </div>
        
        <!-- Lado direito: Saudação mobile + Menu button -->
        <div class="md:hidden flex items-center gap-2">
            @auth
                <!-- Saudação compacta para mobile -->
                <span class="text-gray-600 text-xs">
                    👋 Olá <strong class="text-gray-800">{{ Auth::user()->name }}</strong>
                </span>
            @endauth
            <button id="mobile-menu-btn" class="text-gray-600 hover:text-gray-800 p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

        <!-- Desktop menu -->
        <div class="hidden md:flex items-center gap-6">
            @auth
                <!-- Menu principal para usuários autenticados -->
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-800 hover:bg-gray-100 px-3 py-2 rounded-md transition-all duration-200 {{ request()->routeIs('home') ? 'text-gray-900 bg-gray-100 font-medium' : '' }}">
                    Jogar
                </a>
                
                <!-- Dropdown Gincanas -->
                <div class="relative group">
                    <button class="text-gray-600 hover:text-gray-800 hover:bg-gray-100 px-3 py-2 rounded-md transition-all duration-200 flex items-center gap-1 {{ request()->routeIs('gincana.*') ? 'text-gray-900 bg-gray-100 font-medium' : '' }}">
                        Gincanas
                        <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="absolute top-full left-0 mt-1 w-48 bg-white border border-gray-200 rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <a href="{{ route('gincana.create') }}" class="block px-4 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition-all duration-200 {{ request()->routeIs('gincana.create') ? 'text-gray-900 bg-gray-100' : '' }}">
                            Criar Gincana
                        </a>
                        <a href="{{ route('gincana.index') }}" class="block px-4 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition-all duration-200 {{ request()->routeIs('gincana.index') ? 'text-gray-900 bg-gray-100' : '' }}">
                            Gincanas que Criei
                        </a>
                        <a href="{{ route('gincana.jogadas') }}" class="block px-4 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition-all duration-200 {{ request()->routeIs('gincana.jogadas') ? 'text-gray-900 bg-gray-100' : '' }}">
                            Gincanas que Joguei
                        </a>
                        <a href="{{ route('gincana.disponiveis') }}" class="block px-4 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition-all duration-200 {{ request()->routeIs('gincana.disponiveis') ? 'text-gray-900 bg-gray-100' : '' }}">
                            Gincanas Disponíveis
                        </a>
                    </div>
                </div>

                <!-- Dropdown Rankings -->
                <div class="relative group">
                    <button class="text-gray-600 hover:text-gray-800 hover:bg-gray-100 px-3 py-2 rounded-md transition-all duration-200 flex items-center gap-1 {{ request()->routeIs('ranking.*') ? 'text-gray-900 bg-gray-100 font-medium' : '' }}">
                        🏆 Rankings
                        <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="absolute top-full left-0 mt-1 w-48 bg-white border border-gray-200 rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <a href="{{ route('ranking.geral') }}" class="block px-4 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition-all duration-200 {{ request()->routeIs('ranking.geral') ? 'text-gray-900 bg-gray-100' : '' }}">
                            🌟 Ranking Geral
                        </a>
                        <a href="{{ route('ranking.index') }}" class="block px-4 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition-all duration-200 {{ request()->routeIs('ranking.index') ? 'text-gray-900 bg-gray-100' : '' }}">
                            📊 Por Gincana
                        </a>
                    </div>
                </div>

                <!-- Links informativos -->
                <a href="#" onclick="event.preventDefault(); mostrarComoJogar()" class="text-gray-600 hover:text-gray-800 hover:bg-gray-100 px-3 py-2 rounded-md transition-all duration-200">
                    Como Jogar
                </a>
                <a href="#" onclick="event.preventDefault(); mostrarSobreJogo()" class="text-gray-600 hover:text-gray-800 hover:bg-gray-100 px-3 py-2 rounded-md transition-all duration-200">
                    Sobre
                </a>
                
                <!-- Saudação do usuário - próxima ao menu -->
                <span class="text-gray-600 text-sm border-l border-gray-300 pl-4 ml-2">
                    👋 Olá, <strong class="text-gray-800">{{ Auth::user()->name }}</strong>
                </span>
                
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="text-red-600 hover:text-red-800 hover:bg-red-50 px-3 py-2 rounded-md transition-all duration-200">
                        Sair
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-800 hover:bg-gray-100 px-3 py-2 rounded-md transition-all duration-200">Entrar</a>
                <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-all duration-200">
                    Registrar
                </a>
            @endauth
        </div>

        <!-- Mobile menu overlay -->
        <div id="mobile-menu" class="md:hidden fixed inset-0 bg-black bg-opacity-50 hidden" style="z-index: 99999 !important;">
            <div class="fixed top-0 right-0 h-full w-64 bg-white shadow-lg transform translate-x-full transition-transform duration-300" id="mobile-menu-panel" style="z-index: 100000 !important;">
                <div class="p-4 border-b border-gray-200">
                    <button id="mobile-menu-close" class="float-right text-gray-600 hover:text-gray-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <h3 class="text-lg font-semibold text-blue-600">Menu</h3>
                </div>
                
                @auth
                <div class="p-4 space-y-4">
                    <a href="{{ route('home') }}" class="block text-gray-600 hover:text-gray-800 hover:bg-gray-100 px-3 py-2 rounded-md transition-all duration-200 {{ request()->routeIs('home') ? 'text-gray-900 bg-gray-100 font-medium' : '' }}">
                        Jogar
                    </a>
                    
                    <!-- Gincanas section -->
                    <div class="space-y-2">
                        <p class="text-sm font-medium text-gray-500 px-3">Gincanas</p>
                        <a href="{{ route('gincana.create') }}" class="block text-gray-600 hover:text-gray-800 hover:bg-gray-100 px-6 py-2 rounded-md transition-all duration-200 {{ request()->routeIs('gincana.create') ? 'text-gray-900 bg-gray-100' : '' }}">
                            Criar Gincana
                        </a>
                        <a href="{{ route('gincana.index') }}" class="block text-gray-600 hover:text-gray-800 hover:bg-gray-100 px-6 py-2 rounded-md transition-all duration-200 {{ request()->routeIs('gincana.index') ? 'text-gray-900 bg-gray-100' : '' }}">
                            Gincanas que Criei
                        </a>
                        <a href="{{ route('gincana.jogadas') }}" class="block text-gray-600 hover:text-gray-800 hover:bg-gray-100 px-6 py-2 rounded-md transition-all duration-200 {{ request()->routeIs('gincana.jogadas') ? 'text-gray-900 bg-gray-100' : '' }}">
                            Gincanas que Joguei
                        </a>
                        <a href="{{ route('gincana.disponiveis') }}" class="block text-gray-600 hover:text-gray-800 hover:bg-gray-100 px-6 py-2 rounded-md transition-all duration-200 {{ request()->routeIs('gincana.disponiveis') ? 'text-gray-900 bg-gray-100' : '' }}">
                            Gincanas Disponíveis
                        </a>
                    </div>

                    <!-- Rankings section -->
                    <div class="space-y-2">
                        <p class="text-sm font-medium text-gray-500 px-3">🏆 Rankings</p>
                        <a href="{{ route('ranking.geral') }}" class="block text-gray-600 hover:text-gray-800 hover:bg-gray-100 px-6 py-2 rounded-md transition-all duration-200 {{ request()->routeIs('ranking.geral') ? 'text-gray-900 bg-gray-100' : '' }}">
                            🌟 Ranking Geral
                        </a>
                        <a href="{{ route('ranking.index') }}" class="block text-gray-600 hover:text-gray-800 hover:bg-gray-100 px-6 py-2 rounded-md transition-all duration-200 {{ request()->routeIs('ranking.index') ? 'text-gray-900 bg-gray-100' : '' }}">
                            📊 Por Gincana
                        </a>
                    </div>

                    <a href="#" onclick="event.preventDefault(); mostrarComoJogar()" class="block text-gray-600 hover:text-gray-800 hover:bg-gray-100 px-3 py-2 rounded-md transition-all duration-200">
                        Como Jogar
                    </a>
                    <a href="#" onclick="event.preventDefault(); mostrarSobreJogo()" class="block text-gray-600 hover:text-gray-800 hover:bg-gray-100 px-3 py-2 rounded-md transition-all duration-200">
                        Sobre
                    </a>
                    
                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <form method="POST" action="{{ route('logout') }}" class="mt-2">
                            @csrf
                            <button type="submit" class="block w-full text-left text-red-600 hover:text-red-800 hover:bg-red-50 px-3 py-2 rounded-md transition-all duration-200">
                                Sair
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <div class="p-4 space-y-4">
                    <a href="#" onclick="event.preventDefault(); mostrarComoJogar()" class="block text-gray-600 hover:text-gray-800 hover:bg-gray-100 px-3 py-2 rounded-md transition-all duration-200">
                        Como Jogar
                    </a>
                    <a href="#" onclick="event.preventDefault(); mostrarSobreJogo()" class="block text-gray-600 hover:text-gray-800 hover:bg-gray-100 px-3 py-2 rounded-md transition-all duration-200">
                        Sobre
                    </a>
                    
                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <a href="{{ route('login') }}" class="block text-gray-600 hover:text-gray-800 hover:bg-gray-100 px-3 py-2 rounded-md transition-all duration-200">
                            Entrar
                        </a>
                        <a href="{{ route('register') }}" class="block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-all duration-200 text-center">
                            Registrar
                        </a>
                    </div>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Scripts globais para todos os usuários -->
    <script>
    // Funções SweetAlert - funcionam para todos os usuários
    function mostrarComoJogar() {
        Swal.fire({
            title: 'Como Jogar',
            html: `
                <div class="text-left">
                    <h4 class="font-bold mb-2">🎯 Objetivo do Jogo:</h4>
                    <p class="mb-3">Descubra onde você está no mundo usando apenas as imagens do Street View!</p>
                    
                    <h4 class="font-bold mb-2">🎮 Como Jogar</h4>
                    <ul class="list-disc list-inside mb-3 space-y-1">
                        <li>Observe atentamente a imagem do Street View</li>
                        <li>Procure por pistas: placas, arquitetura, vegetação, idioma</li>
                        <li>Clique em "Ver Mapa" para fazer seu palpite</li>
                        <li>Marque no mapa onde você acha que está</li>
                        <li>Confirme seu palpite e veja sua pontuação!</li>
                    </ul>
                    
                    <h4 class="font-bold mb-2">⭐ Pontuação</h4>
                    <p class="mb-3">Quanto mais próximo do local real, maior sua pontuação!</p>
                    
                    <h4 class="font-bold mb-2">🏆 Dicas</h4>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Observe o lado da estrada (direita/esquerda)</li>
                        <li>Preste atenção nas placas e sinalizações</li>
                        <li>Note o estilo arquitetônico dos prédios</li>
                        <li>Observe a vegetação e clima</li>
                    </ul>
                </div>
            `,
            icon: 'info',
            confirmButtonText: 'Entendi!',
            confirmButtonColor: '#2563eb',
            width: '600px'
        });
    }

    function mostrarSobreJogo() {
        Swal.fire({
            title: 'Sobre o Gincaneiros',
            html: `
                <div class="text-left">
                    <h4 class="font-bold mb-2">🌍 O que é o Gincaneiros?</h4>
                    <p class="mb-3">Gincaneiros é um jogo de geolocalização baseado no conceito do GeoGuessr, onde você precisa adivinhar sua localização no mundo usando apenas imagens do Google Street View.</p>
                    
                    <h4 class="font-bold mb-2">🎯 Funcionalidades</h4>
                    <ul class="list-disc list-inside mb-3 space-y-1">
                        <li><strong>Jogar:</strong> Desafie-se com localizações aleatórias</li>
                        <li><strong>Criar Gincanas:</strong> Crie seus próprios desafios personalizados</li>
                        <li><strong>Gincanas Personalizadas:</strong> Jogue gincanas criadas por outros usuários</li>
                        <li><strong>Sistema de Pontuação:</strong> Acompanhe seu progresso e melhore suas habilidades</li>
                    </ul>
                    
                    <h4 class="font-bold mb-2">🚀 Desenvolvido com</h4>
                    <p class="mb-3">Laravel, JavaScript, Google Maps API e muito amor pela geografia!</p>
                    
                    <h4 class="font-bold mb-2">📞 Contato</h4>
                    <p>Desenvolvido para diversão e aprendizado geográfico!</p>
                </div>
            `,
            icon: 'question',
            confirmButtonText: 'Legal!',
            confirmButtonColor: '#2563eb',
            width: '600px'
        });
    }
    </script>

    <script>
    // Mobile menu functionality - funciona para todos os usuários
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuPanel = document.getElementById('mobile-menu-panel');
        const mobileMenuClose = document.getElementById('mobile-menu-close');

        function openMobileMenu() {
            mobileMenu?.classList.remove('hidden');
            setTimeout(() => {
                mobileMenuPanel?.classList.remove('translate-x-full');
            }, 10);
        }

        function closeMobileMenu() {
            mobileMenuPanel?.classList.add('translate-x-full');
            setTimeout(() => {
                mobileMenu?.classList.add('hidden');
            }, 300);
        }

        mobileMenuBtn?.addEventListener('click', openMobileMenu);
        mobileMenuClose?.addEventListener('click', closeMobileMenu);
        mobileMenu?.addEventListener('click', function(e) {
            if (e.target === mobileMenu) {
                closeMobileMenu();
            }
        });

        // Close mobile menu on window resize to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                closeMobileMenu();
            }
        });
    });
    </script>
@endif
