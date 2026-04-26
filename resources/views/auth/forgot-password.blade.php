<x-guest-layout>
    <div class="mb-6">
        <div class="text-xl font-semibold text-gray-900 dark:text-white">Recuperar contraseña</div>
        <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">Te enviaremos un enlace para restablecer tu contraseña.</div>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-6 space-y-3">
            <x-primary-button class="w-full">
                Enviar enlace
            </x-primary-button>

            <a href="{{ route('login') }}" class="block w-full text-center text-sm font-semibold text-indigo-700 hover:text-indigo-800 dark:text-indigo-300 dark:hover:text-indigo-200">
                Volver a ingresar
            </a>
        </div>
    </form>
</x-guest-layout>
