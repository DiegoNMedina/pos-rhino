<x-guest-layout>
    <div class="mb-6">
        <div class="text-xl font-semibold text-gray-900 dark:text-white">Confirmar contraseña</div>
        <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">Por seguridad, confirma tu contraseña para continuar.</div>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Contraseña" />

            <x-text-input id="password" class="mt-1 block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full">
                Continuar
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
