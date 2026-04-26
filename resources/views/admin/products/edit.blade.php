<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Administración / Productos</div>
            <h2 class="mt-1 font-semibold text-xl text-gray-900 dark:text-white leading-tight">Editar producto</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="cl-surface p-6">
                <form method="POST" action="{{ route('admin.products.update', $product) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" value="Nombre" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $product->name) }}" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="code" value="Código" />
                            <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" value="{{ old('code', $product->code) }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('code')" />
                        </div>

                        <div>
                            <x-input-label for="barcode" value="Código de barras" />
                            <x-text-input id="barcode" name="barcode" type="text" class="mt-1 block w-full" value="{{ old('barcode', $product->barcode) }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('barcode')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="unit_type" value="Tipo" />
                            <select id="unit_type" name="unit_type" class="mt-1 block w-full rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30">
                                <option value="unit" {{ old('unit_type', $product->unit_type) === 'unit' ? 'selected' : '' }}>Pieza</option>
                                <option value="weight" {{ old('unit_type', $product->unit_type) === 'weight' ? 'selected' : '' }}>Peso</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('unit_type')" />
                        </div>

                        <div>
                            <x-input-label for="price" value="Precio" />
                            <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full" value="{{ old('price', $product->price) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('price')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="stock" value="Stock (opcional)" />
                            <x-text-input id="stock" name="stock" type="number" step="0.001" min="0" class="mt-1 block w-full" value="{{ old('stock', $product->stock) }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('stock')" />
                        </div>

                        <div class="flex items-end">
                            <label class="inline-flex items-center gap-2">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950/30" {{ old('is_active', $product->is_active ? '1' : '0') ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700 dark:text-gray-200">Activo</span>
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('admin.products.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">Volver</a>
                        <x-primary-button>Guardar</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
