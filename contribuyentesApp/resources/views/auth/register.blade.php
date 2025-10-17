<x-guest-layout>
    {{-- Título de la tarjeta --}}
    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-6 text-center">
        Crear Cuenta
    </h1>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        {{-- Nombre --}}
        <div>
            <x-input-label for="name" :value="__('Nombre Completo')" class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />
            <x-text-input 
                id="name" 
                class="block w-full py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100 transition duration-150" 
                type="text" 
                name="name" 
                :value="old('name')" 
                required 
                autofocus 
                autocomplete="name" 
                placeholder="Ej: Juan Pérez"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- Correo Electrónico --}}
        <div>
            <x-input-label for="email" :value="__('Correo Electrónico')" class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />
            <x-text-input 
                id="email" 
                class="block w-full py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100 transition duration-150" 
                type="email" 
                name="email" 
                :value="old('email')" 
                required 
                autocomplete="username" 
                placeholder="tu@correo.com"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Contraseña --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña')" class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />
            <x-text-input 
                id="password" 
                class="block w-full py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100 transition duration-150"
                type="password"
                name="password"
                required 
                autocomplete="new-password"
                placeholder="Mínimo 8 caracteres"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirmar Contraseña --}}
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />
            <x-text-input 
                id="password_confirmation" 
                class="block w-full py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100 transition duration-150"
                type="password"
                name="password_confirmation" 
                required 
                autocomplete="new-password"
                placeholder="Repetir contraseña"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- Enlace y Botón de Envío --}}
        <div class="flex items-center justify-between mt-8 pt-4 border-t border-gray-200 dark:border-gray-700">
            
            {{-- Enlace de Login --}}
            <a class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition duration-150" 
                href="{{ route('login') }}">
                {{ __('¿Ya estás registrado?') }}
            </a>

            {{-- Botón Principal de Registro --}}
            <x-primary-button class="ms-4 px-6 py-2.5 text-base font-bold tracking-wide rounded-xl shadow-lg hover:shadow-xl transition duration-300 ease-in-out transform hover:scale-[1.01] bg-indigo-600 hover:bg-indigo-700">
                {{ __('Registrarse') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>