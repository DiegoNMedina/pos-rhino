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
            @if (session('success'))
                <div class="cl-surface p-4 border border-emerald-200/70 bg-emerald-50/60 text-emerald-900 dark:border-emerald-900/40 dark:bg-emerald-950/20 dark:text-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="cl-surface p-4 border border-rose-200/70 bg-rose-50/60 text-rose-900 dark:border-rose-900/40 dark:bg-rose-950/20 dark:text-rose-200">
                    {{ session('error') }}
                </div>
            @endif

            <div class="cl-surface p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
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
                        <div class="text-xs text-gray-500 dark:text-gray-400">Cliente</div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $sale->customer ? $sale->customer->name : '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Pago</div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ ['cash' => 'Efectivo', 'card' => 'Tarjeta', 'mixed' => 'Mixto'][$sale->payment_method] ?? $sale->payment_method }}</div>
                    </div>
                </div>

                <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <div class="text-xs text-gray-500 dark:text-gray-400">Estatus</div>
                        @if ($sale->status === \App\Models\Sale::STATUS_CANCELLED)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950/40 dark:text-rose-200">Cancelada</span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-200">Completada</span>
                        @endif
                    </div>

                    @if ($sale->status !== \App\Models\Sale::STATUS_CANCELLED)
                        <form method="POST" action="{{ route('admin.sales.cancel', $sale) }}" class="flex flex-col sm:flex-row sm:items-center gap-2">
                            @csrf
                            <input type="text" name="reason" value="{{ old('reason') }}" class="w-full sm:w-96 rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur placeholder:text-gray-400 focus:border-rose-500 focus:ring-rose-500 dark:border-gray-800/80 dark:bg-gray-950/30 dark:placeholder:text-gray-500" placeholder="Motivo de cancelación (requerido)" maxlength="255" required />
                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gradient-to-r from-rose-600 to-rose-500 text-white text-sm font-semibold shadow-sm hover:from-rose-500 hover:to-rose-400 transition">
                                Cancelar venta
                            </button>
                        </form>
                        <x-input-error class="mt-2" :messages="$errors->get('reason')" />
                    @else
                        <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                            Cancelada: {{ $sale->cancelled_at ? $sale->cancelled_at->format('Y-m-d H:i') : '—' }} @if($sale->cancel_reason) — {{ $sale->cancel_reason }} @endif
                        </div>
                    @endif
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
