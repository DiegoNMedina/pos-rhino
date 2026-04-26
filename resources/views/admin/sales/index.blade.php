<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Administración / Ventas</div>
                <h2 class="mt-1 font-semibold text-xl text-gray-900 dark:text-white leading-tight">Ventas</h2>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-600 to-indigo-500 font-semibold text-xs text-white uppercase tracking-widest hover:from-indigo-500 hover:to-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white transition dark:focus:ring-offset-gray-950">
                Reportes
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="cl-surface p-6">
                <form method="GET" action="{{ route('admin.sales.index') }}" class="flex flex-col sm:flex-row sm:items-center gap-2">
                    <input type="text" name="q" value="{{ $q }}" class="w-full rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30 dark:placeholder:text-gray-500" placeholder="Buscar por #venta, usuario o método de pago">
                    <x-primary-button type="submit">Buscar</x-primary-button>
                </form>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b border-gray-200 dark:border-gray-800 dark:text-gray-400">
                                <th class="py-2 pr-3">Venta</th>
                                <th class="py-2 px-3">Sucursal</th>
                                <th class="py-2 px-3">Caja</th>
                                <th class="py-2 px-3">Usuario</th>
                                <th class="py-2 px-3">Cliente</th>
                                <th class="py-2 px-3">Pago</th>
                                <th class="py-2 px-3">Estatus</th>
                                <th class="py-2 px-3 text-right">Total</th>
                                <th class="py-2 pl-3 text-right">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse ($sales as $sale)
                                <tr class="hover:bg-white/60 dark:hover:bg-gray-900/50 transition">
                                    <td class="py-3 pr-3 font-medium whitespace-nowrap">
                                        <a href="{{ route('admin.sales.show', $sale) }}" class="text-indigo-700 hover:text-indigo-800 dark:text-indigo-300 dark:hover:text-indigo-200">
                                            #{{ $sale->id }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-3 text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                        {{ $sale->branch ? $sale->branch->name : '—' }}
                                    </td>
                                    <td class="py-3 px-3 text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                        {{ $sale->register ? $sale->register->name : '—' }}
                                    </td>
                                    <td class="py-3 px-3 text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                        {{ $sale->user ? $sale->user->name : '—' }}
                                    </td>
                                    <td class="py-3 px-3 text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                        {{ $sale->customer ? $sale->customer->name : '—' }}
                                    </td>
                                    <td class="py-3 px-3 text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                        {{ ['cash' => 'Efectivo', 'card' => 'Tarjeta', 'mixed' => 'Mixto'][$sale->payment_method] ?? $sale->payment_method }}
                                    </td>
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        @if ($sale->status === \App\Models\Sale::STATUS_CANCELLED)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950/40 dark:text-rose-200">Cancelada</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-200">Completada</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-right tabular-nums whitespace-nowrap text-gray-900 dark:text-white">
                                        ${{ number_format((float) $sale->total, 2) }}
                                    </td>
                                    <td class="py-3 pl-3 text-right text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                        {{ $sale->created_at ? $sale->created_at->format('Y-m-d H:i') : '' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-10 text-center text-gray-500 dark:text-gray-400">
                                        Sin ventas
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $sales->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
