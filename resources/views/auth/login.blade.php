<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6">
        <div class="text-xl font-semibold text-gray-900 dark:text-white">Ingresar</div>
        <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">Accede a tu cuenta para entrar al POS y administración.</div>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" />

            <x-text-input id="password" class="mt-1 block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950/30" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-300">Recordarme</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white" href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        <div class="mt-6 space-y-3">
            <x-primary-button class="w-full">
                Ingresar
            </x-primary-button>

            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="block w-full text-center text-sm font-semibold text-indigo-700 hover:text-indigo-800 dark:text-indigo-300 dark:hover:text-indigo-200">
                    Crear cuenta
                </a>
            @endif
        </div>
    </form>
</x-guest-layout>
