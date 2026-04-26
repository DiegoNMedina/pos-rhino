<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Administración / Panel</div>
                <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">Panel de administración</div>
                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Indicadores clave, ventas recientes y accesos a módulos.</div>
            </div>
            <a href="{{ route('pos.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                Ir al POS
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        customizeOpen: false,
        widgets: {
            resumen: true,
            tendencia: true,
            accesos: true,
            recientes: true,
            top: true,
            inventario: true,
        },
        init() {
            const raw = localStorage.getItem('cajalink.admin.widgets');
            if (!raw) return;
            try {
                const parsed = JSON.parse(raw);
                this.widgets = { ...this.widgets, ...parsed };
            } catch (e) {}
        },
        saveWidgets() {
            localStorage.setItem('cajalink.admin.widgets', JSON.stringify(this.widgets));
            this.customizeOpen = false;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600 dark:text-gray-300">
                    <span class="font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</span>
                    <span class="text-gray-400 dark:text-gray-500">·</span>
                    <span>Rol: {{ auth()->user()->role }}</span>
                </div>
                <button type="button" x-on:click="customizeOpen = true" class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300/80 bg-white/60 text-sm font-semibold text-gray-900 hover:bg-white/80 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white transition backdrop-blur dark:bg-gray-950/20 dark:text-white dark:border-gray-800/80 dark:hover:bg-gray-900 dark:focus:ring-offset-gray-950">
                    Personalizar
                </button>
            </div>

            <div x-show="customizeOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center px-4" style="display: none;">
                <div class="absolute inset-0 bg-black/40" x-on:click="customizeOpen = false"></div>
                <div class="relative w-full max-w-lg cl-surface p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">Personalizar panel</div>
                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">Selecciona los widgets del panel.</div>
                        </div>
                        <button type="button" x-on:click="customizeOpen = false" class="h-10 w-10 inline-flex items-center justify-center rounded-md text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition dark:hover:bg-gray-800 dark:hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200/70 bg-white/50 hover:bg-white/70 transition backdrop-blur dark:border-gray-800/70 dark:bg-gray-950/20 dark:hover:bg-gray-900/60">
                            <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" x-model="widgets.resumen">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">Resumen</div>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200/70 bg-white/50 hover:bg-white/70 transition backdrop-blur dark:border-gray-800/70 dark:bg-gray-950/20 dark:hover:bg-gray-900/60">
                            <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" x-model="widgets.tendencia">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">Tendencia</div>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200/70 bg-white/50 hover:bg-white/70 transition backdrop-blur dark:border-gray-800/70 dark:bg-gray-950/20 dark:hover:bg-gray-900/60">
                            <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" x-model="widgets.accesos">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">Accesos</div>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200/70 bg-white/50 hover:bg-white/70 transition backdrop-blur dark:border-gray-800/70 dark:bg-gray-950/20 dark:hover:bg-gray-900/60">
                            <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" x-model="widgets.recientes">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">Ventas recientes</div>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200/70 bg-white/50 hover:bg-white/70 transition backdrop-blur dark:border-gray-800/70 dark:bg-gray-950/20 dark:hover:bg-gray-900/60">
                            <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" x-model="widgets.top">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">Top productos</div>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200/70 bg-white/50 hover:bg-white/70 transition backdrop-blur dark:border-gray-800/70 dark:bg-gray-950/20 dark:hover:bg-gray-900/60">
                            <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" x-model="widgets.inventario">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">Inventario</div>
                        </label>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-2">
                        <x-secondary-button type="button" x-on:click="customizeOpen = false">Cancelar</x-secondary-button>
                        <x-primary-button type="button" x-on:click="saveWidgets()">Guardar</x-primary-button>
                    </div>
                </div>
            </div>

            <div x-show="widgets.resumen" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('admin.sales.index') }}" class="block cl-surface cl-surface-hover p-5">
                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Ventas hoy</div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900 tabular-nums dark:text-white">{{ $todaySalesCount }}</div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">${{ number_format((float) $todaySalesTotal, 2) }}</div>
                </a>

                <a href="{{ route('admin.reports.index') }}" class="block cl-surface cl-surface-hover p-5">
                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Mes actual</div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900 tabular-nums dark:text-white">{{ $monthSalesCount }}</div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">${{ number_format((float) $monthSalesTotal, 2) }}</div>
                </a>

                <a href="{{ route('admin.products.index') }}" class="block cl-surface cl-surface-hover p-5">
                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Productos</div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900 tabular-nums dark:text-white">{{ $activeProductCount }}</div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Total: {{ $productCount }}</div>
                </a>

                <a href="{{ route('admin.users.index') }}" class="block cl-surface cl-surface-hover p-5">
                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Usuarios</div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900 tabular-nums dark:text-white">{{ $userCount }}</div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Activos en el sistema</div>
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div x-show="widgets.tendencia" class="lg:col-span-2 cl-surface p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">Tendencia (7 días)</div>
                            <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Total vendido por día.</div>
                        </div>
                        <a href="{{ route('admin.reports.index') }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-800 dark:text-indigo-300 dark:hover:text-indigo-200">Ver reportes</a>
                    </div>

                    @php
                        $maxTotal = max(1, (int) $weekly->max('total_sum'));
                        $chartW = 520;
                        $chartH = 140;
                        $points = $weekly->values()->map(function ($row, $i) use ($chartW, $chartH, $maxTotal) {
                            $x = (int) round(($chartW / 6) * $i);
                            $y = (int) round($chartH - ((($row['total_sum'] ?? 0) / $maxTotal) * $chartH));
                            return ['x' => $x, 'y' => $y, 'row' => $row];
                        });
                        $poly = $points->map(fn ($p) => $p['x'].','.$p['y'])->implode(' ');
                    @endphp

                    <div class="mt-6">
                        <svg viewBox="0 0 {{ $chartW }} {{ $chartH }}" class="w-full h-44">
                            <defs>
                                <linearGradient id="cajalinkAdminLine" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="#6366F1" stop-opacity="0.30" />
                                    <stop offset="100%" stop-color="#6366F1" stop-opacity="0.02" />
                                </linearGradient>
                            </defs>
                            <polyline points="{{ $poly }}" fill="none" stroke="#6366F1" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                            <polygon points="{{ $poly }} {{ $chartW }},{{ $chartH }} 0,{{ $chartH }}" fill="url(#cajalinkAdminLine)" />
                            @foreach ($points as $p)
                                <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="5" fill="#6366F1">
                                    <title>{{ $p['row']['day'] }} · ${{ number_format((float) $p['row']['total_sum'], 2) }} · {{ (int) $p['row']['sale_count'] }} ventas</title>
                                </circle>
                            @endforeach
                        </svg>
                    </div>
                </div>

                <div x-show="widgets.accesos" class="cl-surface p-6">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Accesos rápidos</div>
                    <div class="mt-5 grid grid-cols-1 gap-3">
                        <a href="{{ route('admin.products.create') }}" class="flex items-center justify-between gap-4 p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition dark:border-gray-800 dark:hover:bg-gray-800">
                            <div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">Nuevo producto</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Alta rápida de inventario</div>
                            </div>
                            <span class="text-gray-400">+</span>
                        </a>
                        <a href="{{ route('admin.users.create') }}" class="flex items-center justify-between gap-4 p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition dark:border-gray-800 dark:hover:bg-gray-800">
                            <div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">Nuevo usuario</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Roles y accesos</div>
                            </div>
                            <span class="text-gray-400">+</span>
                        </a>
                        <a href="{{ route('admin.sales.index') }}" class="flex items-center justify-between gap-4 p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition dark:border-gray-800 dark:hover:bg-gray-800">
                            <div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">Ventas</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Tickets y totales</div>
                            </div>
                            <span class="text-gray-400">→</span>
                        </a>
                        <a href="{{ route('admin.reports.index') }}" class="flex items-center justify-between gap-4 p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition dark:border-gray-800 dark:hover:bg-gray-800">
                            <div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">Reportes</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Métricas por fecha</div>
                            </div>
                            <span class="text-gray-400">→</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div x-show="widgets.recientes" class="cl-surface p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">Ventas recientes</div>
                            <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Últimas ventas completadas.</div>
                        </div>
                        <a href="{{ route('admin.sales.index') }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-800 dark:text-indigo-300 dark:hover:text-indigo-200">Ver todas</a>
                    </div>

                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 border-b border-gray-200 dark:border-gray-800">
                                    <th class="py-2 pr-3">Venta</th>
                                    <th class="py-2 px-3">Usuario</th>
                                    <th class="py-2 px-3 text-right">Total</th>
                                    <th class="py-2 pl-3 text-right">Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse ($recentSales as $sale)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60 transition">
                                        <td class="py-3 pr-3 font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                            <a href="{{ route('admin.sales.show', $sale) }}" class="text-indigo-700 hover:text-indigo-800 dark:text-indigo-300 dark:hover:text-indigo-200">
                                                #{{ $sale->id }}
                                            </a>
                                        </td>
                                        <td class="py-3 px-3 text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $sale->user ? $sale->user->name : '—' }}</td>
                                        <td class="py-3 px-3 text-right tabular-nums whitespace-nowrap text-gray-900 dark:text-white">${{ number_format((float) $sale->total, 2) }}</td>
                                        <td class="py-3 pl-3 text-right text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $sale->created_at ? $sale->created_at->format('Y-m-d H:i') : '' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-gray-500 dark:text-gray-400">Sin ventas</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-6">
                    <div x-show="widgets.top" class="cl-surface p-6">
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">Top productos (7 días)</div>
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500 border-b border-gray-200 dark:border-gray-800">
                                        <th class="py-2 pr-3">Producto</th>
                                        <th class="py-2 px-3 text-right">Cant.</th>
                                        <th class="py-2 pl-3 text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @forelse ($topProducts as $row)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60 transition">
                                            <td class="py-3 pr-3 font-semibold text-gray-900 dark:text-white">{{ $row->name }}</td>
                                            <td class="py-3 px-3 text-right tabular-nums whitespace-nowrap text-gray-700 dark:text-gray-300">{{ number_format((float) $row->qty_sum, 3) }}</td>
                                            <td class="py-3 pl-3 text-right tabular-nums whitespace-nowrap text-gray-900 dark:text-white">${{ number_format((float) $row->total_sum, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="py-8 text-center text-gray-500 dark:text-gray-400">Sin datos</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div x-show="widgets.inventario" class="cl-surface p-6">
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">Bajo stock</div>
                            <a href="{{ route('admin.products.index') }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-800 dark:text-indigo-300 dark:hover:text-indigo-200">Ver productos</a>
                        </div>
                        <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">Stock ≤ {{ $lowStockThreshold }}.</div>

                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500 border-b border-gray-200 dark:border-gray-800">
                                        <th class="py-2 pr-3">Producto</th>
                                        <th class="py-2 pl-3 text-right">Stock</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @forelse ($lowStockProducts as $product)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60 transition">
                                            <td class="py-3 pr-3 font-semibold text-gray-900 dark:text-white">{{ $product->name }}</td>
                                            <td class="py-3 pl-3 text-right tabular-nums whitespace-nowrap">
                                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-900 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-200 dark:border-amber-900/40">
                                                    {{ number_format((float) $product->stock, 3) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="py-8 text-center text-gray-500 dark:text-gray-400">Sin alertas</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
