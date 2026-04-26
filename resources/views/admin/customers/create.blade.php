<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Administración / Clientes</div>
                <h2 class="mt-1 font-semibold text-xl text-gray-900 dark:text-white leading-tight">Nuevo cliente</h2>
            </div>
            <a href="{{ route('admin.customers.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="cl-surface p-6">
                <form method="POST" action="{{ route('admin.customers.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="name" value="Nombre" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="phone" value="Teléfono" />
                            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone')" />
                            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="email" value="Email" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div>
                            <x-input-label for="tax_id" value="RFC" />
                            <x-text-input id="tax_id" name="tax_id" type="text" class="mt-1 block w-full" :value="old('tax_id')" />
                            <x-input-error class="mt-2" :messages="$errors->get('tax_id')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="address" value="Dirección" />
                        <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address')" />
                        <x-input-error class="mt-2" :messages="$errors->get('address')" />
                    </div>

                    <div>
                        <x-input-label for="notes" value="Notas" />
                        <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30 dark:placeholder:text-gray-500">{{ old('notes') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                    </div>

                    <div class="flex items-center gap-3">
                        <x-primary-button type="submit">Guardar</x-primary-button>
                        <a href="{{ route('admin.customers.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
