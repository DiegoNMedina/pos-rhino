<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Administración / Productos</div>
                <h2 class="mt-1 font-semibold text-xl text-gray-900 dark:text-white leading-tight">Productos</h2>
            </div>
            <a href="{{ route('admin.products.create') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-600 to-indigo-500 font-semibold text-sm text-white shadow-sm hover:from-indigo-500 hover:to-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white transition dark:focus:ring-offset-gray-950">
                Agregar producto
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="cl-surface p-6">
                <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-col sm:flex-row sm:items-center gap-2">
                    <input type="text" name="q" value="{{ $q }}" class="w-full rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30 dark:placeholder:text-gray-500" placeholder="Buscar por nombre, código o código de barras">
                    <x-primary-button type="submit">Buscar</x-primary-button>
                </form>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b border-gray-200 dark:border-gray-800 dark:text-gray-400">
                                <th class="py-2 pr-3">Nombre</th>
                                <th class="py-2 px-3">Tipo</th>
                                <th class="py-2 px-3">Código</th>
                                <th class="py-2 px-3">Barras</th>
                                <th class="py-2 px-3 text-right">Precio</th>
                                <th class="py-2 px-3 text-right">Stock</th>
                                <th class="py-2 pl-3 text-right">Estado</th>
                                <th class="py-2 pl-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse ($products as $product)
                                <tr class="hover:bg-white/60 dark:hover:bg-gray-900/50 transition">
                                    <td class="py-3 pr-3 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                        {{ $product->name }}
                                    </td>
                                    <td class="py-3 px-3 text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                        @if ($product->unit_type === 'weight')
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 dark:bg-indigo-950/30 dark:text-indigo-200 dark:border-indigo-900/40">Peso</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200 dark:bg-sky-950/30 dark:text-sky-200 dark:border-sky-900/40">Pieza</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                        {{ $product->code }}
                                    </td>
                                    <td class="py-3 px-3 text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                        {{ $product->barcode }}
                                    </td>
                                    <td class="py-3 px-3 text-right tabular-nums whitespace-nowrap text-gray-900 dark:text-white">
                                        ${{ number_format((float) $product->price, 2) }}
                                    </td>
                                    <td class="py-3 px-3 text-right tabular-nums whitespace-nowrap text-gray-900 dark:text-white">
                                        @if ($product->stock === null)
                                            —
                                        @else
                                            {{ $product->unit_type === 'weight' ? number_format((float) $product->stock, 3) : number_format((float) $product->stock, 0) }}
                                        @endif
                                    </td>
                                    <td class="py-3 pl-3 text-right whitespace-nowrap">
                                        @if ($product->is_active)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/20 dark:text-emerald-200 dark:border-emerald-900/40">Activo</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-800">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="py-3 pl-3 whitespace-nowrap text-right">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="text-indigo-700 hover:text-indigo-800 dark:text-indigo-300 dark:hover:text-indigo-200 text-sm font-semibold">
                                            Editar
                                        </a>

                                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ml-3 text-red-600 hover:text-red-800 dark:text-red-300 dark:hover:text-red-200 text-sm font-semibold disabled:opacity-50 disabled:pointer-events-none" {{ $product->is_active ? '' : 'disabled' }} onclick="return confirm('¿Desactivar producto?');">
                                                Desactivar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-10 text-center text-gray-500 dark:text-gray-400">
                                        Sin productos
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
