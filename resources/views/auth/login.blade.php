@php
    // 💡 DEFINIR LA RUTA DEL LOGO AQUI
    // ¡IMPORTANTE! Asegúrate de que esta URL sea correcta.
    $logo_url = '../../';
@endphp

{{-- Se pasa la variable $logo_url al layout de invitado --}}
<x-guest-layout :logo_url="$logo_url">
    {{-- Título de la tarjeta (Login / Iniciar Sesión) --}}
    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-6 text-center">
        Iniciar Sesión
    </h1>

    {{-- Muestra el mensaje de estado (ej: "Se envió el enlace para restablecer la contraseña") --}}
    <x-auth-session-status class="mb-4 text-center text-green-600 dark:text-green-400 font-medium" :status="session('status')" />

    {{-- Formulario de Inicio de Sesión --}}
    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        {{-- Campo Email --}}
        <div>
            <x-input-label for="email" :value="__('Correo Electrónico')" class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />
            <x-text-input 
                id="email" 
                class="block w-full py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100 transition duration-150" 
                type="email" 
                name="email" 
                :value="old('email')" 
                required 
                autofocus 
                autocomplete="username" 
                placeholder="tu@correo.com"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Campo Contraseña --}}
        <div>
            <x-input-label for="password" :value="__('Contraseña')" class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />
            <x-text-input 
                id="password" 
                class="block w-full py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100 transition duration-150"
                type="password"
                name="password"
                required 
                autocomplete="current-password"
                placeholder="••••••••"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Recordarme --}}
        <div class="block">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember" 
                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800 transition duration-150">
                <span class="ms-2 text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Recordarme') }}</span>
            </label>
        </div>

        {{-- Enlaces y Botón de Envío --}}
        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
            
            <div class="space-y-2">
                {{-- Enlace de Recuperar Contraseña --}}
                @if (Route::has('password.request'))
                    <a class="block text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 hover:underline transition duration-150" 
                        href="{{ route('password.request') }}">
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </a>
                @endif
                
                {{-- Enlace de Registro --}}
                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="block text-xs text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:underline transition duration-150">
                        {{ __('¿No tienes cuenta? Regístrate') }}
                    </a>
                @endif
            </div>

            {{-- Botón Principal de Login --}}
            <x-primary-button class="ms-3 px-6 py-2.5 text-base font-bold tracking-wide rounded-xl shadow-lg hover:shadow-xl transition duration-300 ease-in-out transform hover:scale-[1.01] bg-indigo-600 hover:bg-indigo-700">
                {{ __('Entrar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>