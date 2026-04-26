<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-white">
            Información del perfil
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
            Actualiza tu información y cómo te verán en el sistema.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="shrink-0">
                @php($avatarUrl = $user->avatar_path ? asset('storage/'.$user->avatar_path) : null)
                <div class="h-16 w-16 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-900 flex items-center justify-center">
                    @if ($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="Avatar" class="h-full w-full object-cover" />
                    @else
                        <div class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            {{ strtoupper(mb_substr((string) $user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex-1">
                <x-input-label for="avatar" value="Foto de perfil" />
                <input id="avatar" name="avatar" type="file" accept="image/png,image/jpeg,image/webp" class="mt-1 block w-full text-sm text-gray-700 dark:text-gray-200 file:mr-4 file:rounded-lg file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-gray-800 hover:file:bg-gray-200 dark:file:bg-gray-900 dark:file:text-gray-200 dark:hover:file:bg-gray-800" />
                <x-input-error class="mt-2" :messages="$errors->get('avatar')" />

                @if ($user->avatar_path)
                    <label class="mt-3 inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                        <input type="checkbox" name="remove_avatar" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900" />
                        Quitar foto actual
                    </label>
                @endif
            </div>
        </div>

        <div>
            <x-input-label for="name" value="Nombre" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 cl-surface px-4 py-3 border-amber-200/70 bg-amber-50/60 text-amber-900 dark:border-amber-900/40 dark:bg-amber-950/20 dark:text-amber-200">
                    <div class="text-sm font-medium">Tu email aún no está verificado.</div>
                    <button form="send-verification" class="mt-2 text-sm font-semibold text-amber-900 hover:text-amber-950 dark:text-amber-200 dark:hover:text-amber-100">
                        Reenviar correo de verificación
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <div class="mt-2 text-sm font-medium text-emerald-700 dark:text-emerald-200">
                            Se envió un nuevo enlace de verificación a tu email.
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="phone" value="Teléfono" />
                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" autocomplete="tel" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>

            <div>
                <x-input-label for="postal_code" value="Código postal" />
                <x-text-input id="postal_code" name="postal_code" type="text" class="mt-1 block w-full" :value="old('postal_code', $user->postal_code)" autocomplete="postal-code" />
                <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
            </div>
        </div>

        <div>
            <x-input-label for="address" value="Dirección" />
            <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $user->address)" autocomplete="street-address" />
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <x-input-label for="city" value="Ciudad" />
                <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $user->city)" />
                <x-input-error class="mt-2" :messages="$errors->get('city')" />
            </div>

            <div>
                <x-input-label for="state" value="Estado" />
                <x-text-input id="state" name="state" type="text" class="mt-1 block w-full" :value="old('state', $user->state)" />
                <x-input-error class="mt-2" :messages="$errors->get('state')" />
            </div>

            <div>
                <x-input-label for="country" value="País" />
                <x-text-input id="country" name="country" type="text" class="mt-1 block w-full" :value="old('country', $user->country)" />
                <x-input-error class="mt-2" :messages="$errors->get('country')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Guardar cambios</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-300"
                >Guardado.</p>
            @endif
        </div>
    </form>
</section>
