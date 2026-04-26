<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Administración / Ventas</div>
                <div class="mt-1 font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                    Venta #{{ $sale->id }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $sale->created_at ? $sale->created_at->format('Y-m-d H:i') : '' }}
                </div>
            </div>
            <a href="{{ route('admin.sales.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="cl-surface p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Sucursal</div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $sale->branch ? $sale->branch->name : '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Caja</div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $sale->register ? $sale->register->name : '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Usuario</div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $sale->user ? $sale->user->name : '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Pago</div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ ['cash' => 'Efectivo', 'card' => 'Tarjeta', 'mixed' => 'Mixto'][$sale->payment_method] ?? $sale->payment_method }}</div>
                    </div>
                </div>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b border-gray-200 dark:border-gray-800 dark:text-gray-400">
                                <th class="py-2 pr-3">Producto</th>
                                <th class="py-2 px-3">Tipo</th>
                                <th class="py-2 px-3 text-right">Cant.</th>
                                <th class="py-2 px-3 text-right">Precio</th>
                                <th class="py-2 pl-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($sale->items as $item)
                                <tr class="hover:bg-white/60 dark:hover:bg-gray-900/50 transition">
                                    <td class="py-3 pr-3 font-medium text-gray-900 dark:text-white">
                                        {{ $item->name }}
                                    </td>
                                    <td class="py-3 px-3 text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                        {{ $item->unit_type === 'weight' ? 'Peso' : 'Pieza' }}
                                    </td>
                                    <td class="py-3 px-3 text-right tabular-nums whitespace-nowrap text-gray-900 dark:text-white">
                                        {{ number_format((float) $item->quantity, 3) }}
                                    </td>
                                    <td class="py-3 px-3 text-right tabular-nums whitespace-nowrap text-gray-900 dark:text-white">
                                        ${{ number_format((float) $item->unit_price, 2) }}
                                    </td>
                                    <td class="py-3 pl-3 text-right tabular-nums whitespace-nowrap text-gray-900 dark:text-white">
                                        ${{ number_format((float) $item->total, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 border-t border-gray-200 dark:border-gray-800 pt-4 flex items-center justify-end gap-8">
                    <div class="text-sm text-gray-600 dark:text-gray-300">Total</div>
                    <div class="text-xl font-semibold tabular-nums text-gray-900 dark:text-white">${{ number_format((float) $sale->total, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
