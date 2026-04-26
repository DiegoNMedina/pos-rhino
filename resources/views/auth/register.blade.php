<x-guest-layout>
    <div class="mb-6">
        <div class="text-xl font-semibold text-gray-900 dark:text-white">Crear cuenta</div>
        <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">Configura tu acceso para usar {{ config('app.name') }}.</div>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nombre" />
            <x-text-input id="name" class="mt-1 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" />

            <x-text-input id="password" class="mt-1 block w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmar contraseña" />

            <x-text-input id="password_confirmation" class="mt-1 block w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6 cl-surface p-4">
            <div class="text-sm font-semibold text-gray-900 dark:text-white">Tu plan</div>
            <div class="mt-3">
                <x-input-label for="plan" value="Plan" />
                <select id="plan" name="plan" class="mt-1 block w-full rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30">
                    <option value="starter" {{ old('plan', 'pro') === 'starter' ? 'selected' : '' }}>Starter</option>
                    <option value="pro" {{ old('plan', 'pro') === 'pro' ? 'selected' : '' }}>Pro</option>
                    <option value="enterprise" {{ old('plan', 'pro') === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                </select>
                <x-input-error :messages="$errors->get('plan')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label value="Método de pago" />
                <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <label class="flex items-center gap-3 rounded-lg border border-gray-200/80 bg-white/60 px-3 py-2 text-sm text-gray-900 shadow-sm backdrop-blur dark:border-gray-800/80 dark:bg-gray-950/20 dark:text-white">
                        <input type="radio" name="billing_method" value="stripe" class="text-indigo-600 focus:ring-indigo-500" {{ old('billing_method', 'stripe') === 'stripe' ? 'checked' : '' }}>
                        <span>Stripe (tarjeta)</span>
                    </label>
                    <label class="flex items-center gap-3 rounded-lg border border-gray-200/80 bg-white/60 px-3 py-2 text-sm text-gray-900 shadow-sm backdrop-blur dark:border-gray-800/80 dark:bg-gray-950/20 dark:text-white">
                        <input type="radio" name="billing_method" value="transfer" class="text-indigo-600 focus:ring-indigo-500" {{ old('billing_method', 'stripe') === 'transfer' ? 'checked' : '' }}>
                        <span>Transferencia</span>
                    </label>
                </div>
                <x-input-error :messages="$errors->get('billing_method')" class="mt-2" />
            </div>
        </div>

        <div class="mt-6 space-y-3">
            <x-primary-button class="w-full">
                Crear cuenta y pagar
            </x-primary-button>

            <a class="block w-full text-center text-sm font-semibold text-indigo-700 hover:text-indigo-800 dark:text-indigo-300 dark:hover:text-indigo-200" href="{{ route('login') }}">
                Ya tengo cuenta
            </a>
        </div>
    </form>
</x-guest-layout>
