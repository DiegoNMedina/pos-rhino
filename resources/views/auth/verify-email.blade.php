<x-guest-layout>
    <div class="mb-6">
        <div class="text-xl font-semibold text-gray-900 dark:text-white">Verificar email</div>
        <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">
            Revisa tu bandeja de entrada y confirma tu email para comenzar a usar {{ config('app.name') }}.
        </div>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 px-4 py-3 rounded-md dark:text-emerald-200 dark:bg-emerald-950/20 dark:border-emerald-900/40">
            Se envió un nuevo enlace de verificación al email registrado.
        </div>
    @endif

    <div class="mt-6 space-y-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button class="w-full">
                    Reenviar verificación
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <x-secondary-button type="submit" class="w-full">
                Salir
            </x-secondary-button>
        </form>
    </div>
</x-guest-layout>
