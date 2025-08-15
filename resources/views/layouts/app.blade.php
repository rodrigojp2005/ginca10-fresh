<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Gincaneiros - Jogo de Localização'))</title>
    <meta name="theme-color" content="#2563eb" />
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="mobile-web-app-capable" content="yes">
  <link rel="apple-touch-icon" href="/images/gincaneiros_logo.png">
  <link rel="manifest" href="/manifest.webmanifest?v=2025-08-13-1" crossorigin="use-credentials">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet" />
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/css/game.css', 'resources/js/app.js', 'resources/js/game.js'])
    @stack('styles')
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBzEzusC_k3oEoPnqynq2N4a0aA3arzH-c&libraries=geometry&callback=initGame"></script>

    <script>window.LaravelIsAuthenticated = {{ Auth::check() ? 'true' : 'false' }};</script>
  <script>window.APP_VAPID_KEY = '{{ env('VAPID_PUBLIC_KEY') }}';</script>

</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen bg-gray-100 flex flex-col">
        @include('layouts.navigation')
        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset
        <!-- Page Content -->
        <main class="flex-1">
            @yield('content')
        </main>
        @include('layouts.footer')
    </div>
@yield('scripts')
<style>
  #install-btn {
    display: none;
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #2563eb; /* mesmo azul do theme-color */
    color: white;
    border: none;
    padding: 12px 18px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    cursor: pointer;
    z-index: 9999;
  }
</style>

<button id="install-btn">📲 Instalar Gincaneiros</button>

<script>
  let deferredPrompt;

  // Detecta Android
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    document.getElementById('install-btn').style.display = 'block';
  });

  document.getElementById('install-btn').addEventListener('click', async () => {
    document.getElementById('install-btn').style.display = 'none';
    deferredPrompt.prompt();
    const choiceResult = await deferredPrompt.userChoice;
    console.log(choiceResult.outcome);
    deferredPrompt = null;
  });

  // Detecta iOS e mostra instruções
  const isIOS = /iphone|ipad|ipod/.test(window.navigator.userAgent.toLowerCase());
  const isInStandalone = ('standalone' in window.navigator) && window.navigator.standalone;

  if (isIOS && !isInStandalone) {
    Swal.fire({
      icon: 'info',
      title: 'Instalar no iPhone',
      html: 'Para instalar, toque em <strong>Compartilhar</strong> e escolha <em>Adicionar à Tela de Início</em>.',
      confirmButtonText: 'Entendi',
      confirmButtonColor: '#2563eb'
    });
  }
</script>
</body>
</html>
