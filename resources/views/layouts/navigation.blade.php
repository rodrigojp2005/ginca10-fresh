@if (Route::has('login'))
    <nav class="flex items-center justify-between p-2 md:p-4 bg-white shadow-md z-50 relative">
        <!-- Logo / Brand à esquerda -->
        <div class="flex items-center min-w-0">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-lg md:text-xl text-gray-800 hover:text-blue-600 whitespace-nowrap">
                <img src="/images/gincaneiros_logo.png" alt="Gincaneiros" class="h-7 w-7 md:h-8 md:w-8 object-contain" loading="lazy" />
                <span class="tracking-tight">Gincaneiros</span>
            </a>
        </div>

        <!-- Ações à direita (mobile + desktop) -->
        <div class="flex items-center gap-2 md:gap-4 ml-4">
            @auth
                <!-- Sino de notificações (sempre visível) -->
                <div class="relative">
                    <button id="notif-bell" class="relative p-2 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-400" aria-label="Notificações">
                        <img id="notif-bell-gif" src="https://media2.giphy.com/media/v1.Y2lkPTc5MGI3NjExajB1cXY3OWE4aHFrZncydjlvb3ZyNjEyeWxhZ2c3Mzd2anl3MDRnNSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/Jc3dFKDbucGhyIm90X/giphy.gif" alt="Notificações" class="h-7 w-7 object-contain" loading="lazy" />
                        <span id="notif-badge" class="hidden absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-red-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center"></span>
                    </button>
                    <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 max-h-[70vh] overflow-y-auto bg-white border border-gray-200 shadow-lg rounded-lg text-sm z-50">
                        <div class="flex items-center justify-between px-3 py-2 border-b">
                            <span class="font-semibold">Notificações</span>
                            <button id="notif-mark-all" class="text-xs text-blue-600 hover:underline">Marcar todas</button>
                        </div>
                        <ul id="notif-list" class="divide-y divide-gray-100">
                            <li class="p-3 text-gray-500 text-xs">Carregando...</li>
                        </ul>
                        <div class="p-2 text-center">
                            <button id="notif-reload" class="text-xs text-gray-500 hover:text-gray-700">Atualizar</button>
                        </div>
                    </div>
                </div>

                <!-- Avatar Emoji / Menu usuário -->
                <div class="relative">
                    <button id="user-menu-btn" class="p-2 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-400 text-xl" aria-haspopup="true" aria-expanded="false">🙂</button>
                    <div id="user-menu-dropdown" class="hidden absolute right-0 mt-2 w-auto min-w-[18rem] sm:min-w-[20rem] max-w-[90vw] bg-white border border-gray-200 shadow-lg rounded-lg text-sm z-50">
                        <div class="px-4 py-3 border-b">
                            <div class="text-xs text-gray-500">Olá</div>
                            <div class="font-semibold whitespace-normal break-words">{{ Auth::user()->name }}</div>
                        </div>
                        <ul class="py-1">
                            <li><a href="{{ route('profile.edit') ?? '#' }}" class="block px-4 py-2 hover:bg-gray-50">Perfil</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-50">Sair</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Links informativos (recolhidos) -->
                <a href="#" onclick="event.preventDefault(); mostrarComoJogar()" class="hidden md:inline-block px-3 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md">Como Jogar</a>
                <a href="#" onclick="event.preventDefault(); mostrarSobreJogo()" class="hidden md:inline-block px-3 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md">Sobre</a>
            @else
                <div class="hidden md:flex items-center gap-2">
                    <a href="#" onclick="event.preventDefault(); mostrarComoJogar()" class="px-3 py-1 text-sm text-gray-600">Como Jogar</a>
                    <a href="#" onclick="event.preventDefault(); mostrarSobreJogo()" class="px-3 py-1 text-sm text-gray-600">Sobre</a>
                    <a href="{{ route('login') }}" class="px-3 py-1 text-sm font-medium text-blue-600 hover:underline">Entrar</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-3 py-1 text-sm text-gray-600">Registrar</a>
                    @endif
                </div>
            @endauth

            <!-- Botão menu lateral antigo (mantido) -->
            <button id="mobile-menu-btn" class="md:hidden p-2 rounded hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-400" aria-label="Menu">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
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
                    <p class="mb-3">Adivinhe onde está apenas pela imagem do Street View.</p>

                    <h4 class="font-bold mb-2">🎮 Como Jogar</h4>
                    <ul class="list-disc list-inside mb-3 space-y-1">
                        <li>Analise a imagem e busque por pistas</li>
                        <li>Clique em Start e marque o local no mapa</li>
                        <li>Confirme o Palpite, você tem 5 tentativas</li>
                        <li>Caso erre, leia a dica da distância e direção!</li>
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
                    <p class="mb-3">É uma brincadeira de desafios para testar a memória afetiva dos seus amigos e parentes ou ainda mostrar para eles lugares que você já visitou ou quer visitar.</p>             
                    <h4 class="font-bold mb-2">🎯 Crie sua Gincana</h4>
                    <ul class="list-disc list-inside mb-3 space-y-1">
                        <li><strong>Infância:</strong> Desafie alguém se lembrar de determinado local</li>
                        <li><strong>Viagens:</strong> Desafie alguém adivinhar o local que você quer conhecer</li>
                    </ul>
                    <h4 class="font-bold mb-2">🔍 Procure gicanas de amigos ou aleatórias.</h4>
                    <h4 class="font-bold mb-2"><br>📞 Contato (zap): 53 981056952</h4>
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
    // Interação sino + avatar + polling
    document.addEventListener('DOMContentLoaded', function() {
        const notifBtn = document.getElementById('notif-bell');
        const notifDropdown = document.getElementById('notif-dropdown');
    const notifList = document.getElementById('notif-list');
    const notifBadge = document.getElementById('notif-badge');
    const notifBellGif = document.getElementById('notif-bell-gif');
        const markAllBtn = document.getElementById('notif-mark-all');
        const reloadBtn = document.getElementById('notif-reload');
        const userBtn = document.getElementById('user-menu-btn');
        const userDropdown = document.getElementById('user-menu-dropdown');
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuPanel = document.getElementById('mobile-menu-panel');
        const mobileMenuClose = document.getElementById('mobile-menu-close');

        function hide(el){ if(el && !el.classList.contains('hidden')) el.classList.add('hidden'); }
        function toggle(el){ el.classList.toggle('hidden'); }

                async function fetchNotifs(){
            if(!window.LaravelIsAuthenticated) return;
            try {
                const r = await fetch('/notifications');
                if(!r.ok) return;
                const data = await r.json();
                                // novo formato: { unread_groups, gincanas: [...] }
                                const groups = data.gincanas || [];
                                const unreadGroups = data.unread_groups || 0;
                                if(unreadGroups>0){
                                        notifBadge.textContent = unreadGroups>99?'99+':unreadGroups;
                    notifBadge.classList.remove('hidden');
                } else {
                    notifBadge.classList.add('hidden');
                }
                notifList.innerHTML = '';
                                if(groups.length===0){
                    notifList.innerHTML = '<li class="p-3 text-center text-xs text-gray-400">Sem notificações</li>';
                } else {
                                        groups.forEach(n => {
                        const li = document.createElement('li');
                                                li.className = 'p-3 hover:bg-gray-50 cursor-pointer flex items-start justify-between gap-2';
                                                const countBadge = n.unread_count>0 ? `<span class="ml-2 inline-flex items-center justify-center min-w-[20px] h-[20px] px-1 rounded-full bg-red-600 text-white text-[11px] font-bold">${n.unread_count>99?'99+':n.unread_count}</span>` : '';
                                                li.innerHTML = `
                                                    <div>
                                                        <div class='font-medium text-gray-800 mb-0.5'>${n.gincana_nome||('Gincana #' + n.gincana_id)}</div>
                                                        <div class='text-gray-600 text-sm'>${(n.last_author_name||'Alguém')}: ${(n.last_preview||'').substring(0,120)}</div>
                                                        <div class='text-xs text-gray-400 mt-1'>Atualizado: ${(n.updated_at? new Date(n.updated_at).toLocaleString() : '')}</div>
                                                    </div>
                                                    <div class='flex items-center'>${countBadge}</div>`;
                                                li.addEventListener('click', async () => {
                                                        if(n.gincana_id){
                                                                // zera só essa gincana e navega
                                                                try {
                                                                    await fetch('/notifications/read', {method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}, body: JSON.stringify({gincana_id:n.gincana_id})});
                                                                } catch(e){}
                                                                window.location.href = '/gincana/' + n.gincana_id;
                                                                return;
                                                        }
                                                });
                        notifList.appendChild(li);
                    });
                }
            } catch(e){ /* silencia */ }
        }

        markAllBtn?.addEventListener('click', async (e)=>{
            e.preventDefault();
            await fetch('/notifications/read',{method:'POST', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}});
            fetchNotifs();
        });
        reloadBtn?.addEventListener('click', (e)=>{ e.preventDefault(); fetchNotifs(); });

        document.addEventListener('click', (e) => {
            // Sino
            if(notifBtn && notifBtn.contains(e.target)) {
                toggle(notifDropdown);
                if(!notifDropdown.classList.contains('hidden')) fetchNotifs();
            } else if(notifDropdown && !notifDropdown.contains(e.target) && !notifBtn.contains(e.target)) {
                hide(notifDropdown);
            }
            // User menu
            if(userBtn && userBtn.contains(e.target)) {
                toggle(userDropdown);
            } else if(userDropdown && !userDropdown.contains(e.target) && !userBtn.contains(e.target)) {
                hide(userDropdown);
            }
        });

                if(window.LaravelIsAuthenticated){
            fetchNotifs();
            setInterval(fetchNotifs, 15000);
            document.addEventListener('visibilitychange', () => { if(!document.hidden) fetchNotifs(); });
                        if (navigator.serviceWorker && navigator.serviceWorker.addEventListener) {
                            navigator.serviceWorker.addEventListener('message', (event) => {
                                if(event?.data?.type === 'NOTIFICATIONS_UPDATED') {
                                    fetchNotifs();
                                }
                            });
                        }
        }

        function openMobileMenu(){ mobileMenu?.classList.remove('hidden'); setTimeout(()=> mobileMenuPanel?.classList.remove('translate-x-full'), 10); }
        function closeMobileMenu(){ mobileMenuPanel?.classList.add('translate-x-full'); setTimeout(()=> mobileMenu?.classList.add('hidden'), 300); }
        mobileMenuBtn?.addEventListener('click', openMobileMenu);
        mobileMenuClose?.addEventListener('click', closeMobileMenu);
        mobileMenu?.addEventListener('click', (e)=>{ if(e.target===mobileMenu) closeMobileMenu(); });
        window.addEventListener('resize', ()=>{ if(window.innerWidth>=768) closeMobileMenu(); });
    });
    </script>
@endif
