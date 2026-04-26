<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Administración / Reportes</div>
                <h2 class="mt-1 font-semibold text-xl text-gray-900 dark:text-white leading-tight">Reportes</h2>
            </div>
            <a href="{{ route('admin.sales.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                Ver ventas
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="cl-surface p-6">
                <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-col sm:flex-row sm:items-end gap-4">
                    <div class="w-full sm:w-56">
                        <x-input-label for="from" value="Desde" />
                        <input id="from" name="from" type="date" value="{{ $from }}" class="mt-1 w-full rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30">
                    </div>
                    <div class="w-full sm:w-56">
                        <x-input-label for="to" value="Hasta" />
                        <input id="to" name="to" type="date" value="{{ $to }}" class="mt-1 w-full rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30">
                    </div>
                    <div>
                        <x-primary-button type="submit">Aplicar</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="cl-surface p-6">
                    <div class="text-gray-900 dark:text-white font-semibold">Ventas por día</div>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 border-b border-gray-200 dark:border-gray-800 dark:text-gray-400">
                                    <th class="py-2 pr-3">Día</th>
                                    <th class="py-2 px-3 text-right">Ventas</th>
                                    <th class="py-2 pl-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse ($salesByDay as $row)
                                    <tr class="hover:bg-white/60 dark:hover:bg-gray-900/50 transition">
                                        <td class="py-3 pr-3 text-gray-900 dark:text-white whitespace-nowrap">{{ $row->day }}</td>
                                        <td class="py-3 px-3 text-right tabular-nums whitespace-nowrap text-gray-900 dark:text-white">{{ $row->sale_count }}</td>
                                        <td class="py-3 pl-3 text-right tabular-nums whitespace-nowrap text-gray-900 dark:text-white">${{ number_format((float) $row->total_sum, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-10 text-center text-gray-500 dark:text-gray-400">Sin datos</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="cl-surface p-6">
                    <div class="text-gray-900 dark:text-white font-semibold">Top productos (monto)</div>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 border-b border-gray-200 dark:border-gray-800 dark:text-gray-400">
                                    <th class="py-2 pr-3">Producto</th>
                                    <th class="py-2 px-3 text-right">Cantidad</th>
                                    <th class="py-2 pl-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse ($topProducts as $row)
                                    <tr class="hover:bg-white/60 dark:hover:bg-gray-900/50 transition">
                                        <td class="py-3 pr-3 text-gray-900 dark:text-white">{{ $row->name }}</td>
                                        <td class="py-3 px-3 text-right tabular-nums whitespace-nowrap text-gray-900 dark:text-white">{{ number_format((float) $row->qty_sum, 3) }}</td>
                                        <td class="py-3 pl-3 text-right tabular-nums whitespace-nowrap text-gray-900 dark:text-white">${{ number_format((float) $row->total_sum, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-10 text-center text-gray-500 dark:text-gray-400">Sin datos</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
