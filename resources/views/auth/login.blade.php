<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Senha')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Lembre-me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Esqueceu sua senha?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Entrar') }}
            </x-primary-button>
        </div>
    </form>
     <div class="mt-6 flex flex-col items-center">
        <a href="{{ route('auth.google') }}" class="inline-flex items-center px-4 py-2 w-full max-w-md justify-center bg-red-600 text-white rounded-md shadow hover:bg-red-700 transition-all duration-200">
            <svg class="w-5 h-5 mr-2" viewBox="0 0 48 48"><g><path fill="#4285F4" d="M24 9.5c3.54 0 6.7 1.22 9.19 3.22l6.85-6.85C36.36 2.7 30.55 0 24 0 14.61 0 6.4 5.7 2.44 14.02l7.98 6.21C12.36 13.16 17.74 9.5 24 9.5z"/><path fill="#34A853" d="M46.1 24.5c0-1.64-.15-3.22-.44-4.75H24v9.02h12.44c-.54 2.92-2.18 5.39-4.65 7.06l7.2 5.59C43.98 37.36 46.1 31.36 46.1 24.5z"/><path fill="#FBBC05" d="M10.42 28.23c-1.13-3.36-1.13-6.97 0-10.33l-7.98-6.21C.64 16.36 0 20.09 0 24c0 3.91.64 7.64 2.44 12.31l7.98-6.21z"/><path fill="#EA4335" d="M24 48c6.55 0 12.36-2.16 16.85-5.89l-7.2-5.59c-2.01 1.35-4.59 2.15-7.65 2.15-6.26 0-11.64-3.66-13.58-8.73l-7.98 6.21C6.4 42.3 14.61 48 24 48z"/></g></svg>
            Entrar com Google
        </a>
    </div>
</x-guest-layout>
