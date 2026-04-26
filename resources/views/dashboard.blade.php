<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Inicio / Resumen</div>
                <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">Inicio</div>
                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Resumen rápido de ventas, inventario y accesos.</div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('pos.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                    Ir al POS
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        customizeOpen: false,
        widgets: {
            resumen: true,
            tendencia: true,
            accesos: true,
            recientes: true,
            inventario: true,
        },
        init() {
            const raw = localStorage.getItem('cajalink.dashboard.widgets');
            if (!raw) return;
            try {
                const parsed = JSON.parse(raw);
                this.widgets = { ...this.widgets, ...parsed };
            } catch (e) {}
        },
        saveWidgets() {
            localStorage.setItem('cajalink.dashboard.widgets', JSON.stringify(this.widgets));
            this.customizeOpen = false;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600 dark:text-gray-300">
                    <span class="font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</span>
                    <span class="text-gray-400 dark:text-gray-500">·</span>
                    <span>{{ auth()->user()->email }}</span>
                </div>
                <button type="button" x-on:click="customizeOpen = true" class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300/80 bg-white/60 text-sm font-semibold text-gray-900 hover:bg-white/80 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white transition backdrop-blur dark:bg-gray-950/20 dark:text-white dark:border-gray-800/80 dark:hover:bg-gray-900 dark:focus:ring-offset-gray-950">
                    Personalizar
                </button>
            </div>

            <div x-show="customizeOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center px-4" style="display: none;">
                <div class="absolute inset-0 bg-black/55 backdrop-blur-sm" x-on:click="customizeOpen = false"></div>
                <div class="relative w-full max-w-lg cl-surface p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">Personalizar dashboard</div>
                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">Elige qué widgets quieres ver.</div>
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
                <div class="cl-surface cl-surface-hover p-5">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Ventas hoy</div>
                        <div class="h-9 w-9 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center dark:bg-indigo-950/40 dark:text-indigo-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M4 3a1 1 0 000 2h1v11a1 1 0 001 1h10a1 1 0 100-2H7V5h9a1 1 0 100-2H4z" />
                                <path d="M9 7a1 1 0 012 0v6a1 1 0 11-2 0V7zM12 9a1 1 0 112 0v4a1 1 0 11-2 0V9z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 text-2xl font-semibold text-gray-900 tabular-nums dark:text-white">{{ $todaySalesCount }}</div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Completadas</div>
                </div>

                <div class="cl-surface cl-surface-hover p-5">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Total hoy</div>
                        <div class="h-9 w-9 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center dark:bg-emerald-950/40 dark:text-emerald-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16Zm1-11a1 1 0 10-2 0v1H8a1 1 0 100 2h1v1H8a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1v-1h1a1 1 0 100-2h-1V7Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 text-2xl font-semibold text-gray-900 tabular-nums dark:text-white">${{ number_format((float) $todaySalesTotal, 2) }}</div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ingresos del día</div>
                </div>

                <div class="cl-surface cl-surface-hover p-5">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Productos activos</div>
                        <div class="h-9 w-9 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center dark:bg-indigo-950/40 dark:text-indigo-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2 5a2 2 0 012-2h3a2 2 0 012 2v1h2V5a2 2 0 012-2h3a2 2 0 012 2v3a2 2 0 01-2 2h-3a2 2 0 01-2-2V7H9v1a2 2 0 01-2 2H4a2 2 0 01-2-2V5z" />
                                <path d="M2 12a2 2 0 012-2h3a2 2 0 012 2v3a2 2 0 01-2 2H4a2 2 0 01-2-2v-3zM11 12a2 2 0 012-2h3a2 2 0 012 2v3a2 2 0 01-2 2h-3a2 2 0 01-2-2v-3z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 text-2xl font-semibold text-gray-900 tabular-nums dark:text-white">{{ $activeProductsCount }}</div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Listos para vender</div>
                </div>

                <div class="cl-surface cl-surface-hover p-5">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Alertas</div>
                        <div class="h-9 w-9 rounded-lg bg-amber-50 text-amber-800 flex items-center justify-center dark:bg-amber-950/40 dark:text-amber-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.721-1.36 3.486 0l6.516 11.59c.75 1.334-.213 2.99-1.742 2.99H3.483c-1.53 0-2.493-1.656-1.743-2.99l6.517-11.59ZM11 14a1 1 0 10-2 0 1 1 0 002 0Zm-1-8a1 1 0 00-1 1v4a1 1 0 102 0V7a1 1 0 00-1-1Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 text-2xl font-semibold text-gray-900 tabular-nums dark:text-white">{{ $lowStockProducts->count() }}</div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Productos con stock ≤ {{ $lowStockThreshold }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div x-show="widgets.tendencia" class="lg:col-span-2 cl-surface p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">Tendencia (últimos 7 días)</div>
                            <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Total vendido por día.</div>
                        </div>
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
                        <div class="relative">
                            <svg viewBox="0 0 {{ $chartW }} {{ $chartH }}" class="w-full h-44">
                                <defs>
                                    <linearGradient id="cajalinkLine" x1="0" x2="0" y1="0" y2="1">
                                        <stop offset="0%" stop-color="#6366F1" stop-opacity="0.30" />
                                        <stop offset="100%" stop-color="#6366F1" stop-opacity="0.02" />
                                    </linearGradient>
                                </defs>

                                <polyline points="{{ $poly }}" fill="none" stroke="#6366F1" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                <polygon points="{{ $poly }} {{ $chartW }},{{ $chartH }} 0,{{ $chartH }}" fill="url(#cajalinkLine)" />

                                @foreach ($points as $p)
                                    <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="5" fill="#6366F1">
                                        <title>{{ $p['row']['day'] }} · ${{ number_format((float) $p['row']['total_sum'], 2) }} · {{ (int) $p['row']['sale_count'] }} ventas</title>
                                    </circle>
                                @endforeach
                            </svg>
                        </div>

                        <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2">
                            @foreach ($weekly as $row)
                                <div class="p-3 rounded-lg border border-gray-200 bg-white/60 dark:bg-gray-950/20 dark:border-gray-800">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\CarbonImmutable::parse($row['day'])->isoFormat('ddd D') }}</div>
                                    <div class="mt-1 text-sm font-semibold tabular-nums text-gray-900 dark:text-white">${{ number_format((float) $row['total_sum'], 2) }}</div>
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ (int) $row['sale_count'] }} ventas</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div x-show="widgets.accesos" class="cl-surface p-6">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Accesos</div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Atajos para tareas frecuentes.</div>

                    <div class="mt-5 grid grid-cols-1 gap-3">
                        <a href="{{ route('pos.index') }}" class="group flex items-center justify-between gap-4 p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition dark:border-gray-800 dark:hover:bg-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center dark:bg-indigo-950/40 dark:text-indigo-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm3 1a1 1 0 000 2h1a1 1 0 100-2H5zm4 0a1 1 0 100 2h6a1 1 0 100-2H9zm-4 4a1 1 0 000 2h10a1 1 0 100-2H5z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Punto de venta</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Vender y cobrar</div>
                                </div>
                            </div>
                            <div class="text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-200 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </a>

                        <a href="{{ route('profile.edit') }}" class="group flex items-center justify-between gap-4 p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition dark:border-gray-800 dark:hover:bg-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-gray-100 text-gray-700 flex items-center justify-center dark:bg-gray-800 dark:text-gray-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 2a5 5 0 00-3.536 8.536A7 7 0 003 17a1 1 0 102 0 5 5 0 0110 0 1 1 0 102 0 7 7 0 00-3.464-6.464A5 5 0 0010 2zm3 5a3 3 0 11-6 0 3 3 0 016 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Mi perfil</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Datos y contraseña</div>
                                </div>
                            </div>
                            <div class="text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-200 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </a>

                        @can('manage-pos')
                            <a href="{{ route('admin.dashboard') }}" class="group flex items-center justify-between gap-4 p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition dark:border-gray-800 dark:hover:bg-gray-800">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center dark:bg-indigo-950/40 dark:text-indigo-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M2 11a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H3a1 1 0 01-1-1v-6zM11 3a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1h-6a1 1 0 01-1-1V3zM11 13a1 1 0 011-1h6a1 1 0 011 1v4a1 1 0 01-1 1h-6a1 1 0 01-1-1v-4zM2 3a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H3a1 1 0 01-1-1V3z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">Administración</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Productos, ventas, usuarios</div>
                                    </div>
                                </div>
                                <div class="text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-200 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </a>
                        @endcan
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
                        @can('manage-pos')
                            <a href="{{ route('admin.sales.index') }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-800 dark:text-indigo-300 dark:hover:text-indigo-200">
                                Ver todas
                            </a>
                        @endcan
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
                                        <td class="py-3 pr-3 font-semibold text-gray-900 dark:text-white whitespace-nowrap">#{{ $sale->id }}</td>
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

                <div x-show="widgets.inventario" class="cl-surface p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">Inventario</div>
                            <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Bajo stock (≤ {{ $lowStockThreshold }}).</div>
                        </div>
                        @can('manage-pos')
                            <a href="{{ route('admin.products.index') }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-800 dark:text-indigo-300 dark:hover:text-indigo-200">
                                Ver productos
                            </a>
                        @endcan
                    </div>

                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 border-b border-gray-200 dark:border-gray-800">
                                    <th class="py-2 pr-3">Producto</th>
                                    <th class="py-2 px-3">Tipo</th>
                                    <th class="py-2 pl-3 text-right">Stock</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse ($lowStockProducts as $product)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60 transition">
                                        <td class="py-3 pr-3 font-semibold text-gray-900 dark:text-white">{{ $product->name }}</td>
                                        <td class="py-3 px-3 text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $product->unit_type === 'weight' ? 'Peso' : 'Pieza' }}</td>
                                        <td class="py-3 pl-3 text-right tabular-nums whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-900 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-200 dark:border-amber-900/40">
                                                {{ number_format((float) $product->stock, 3) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-8 text-center text-gray-500 dark:text-gray-400">Sin alertas de stock</td>
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
