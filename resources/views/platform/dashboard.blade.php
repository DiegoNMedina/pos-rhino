<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Plataforma / Resumen</div>
                <h2 class="mt-1 font-semibold text-xl text-gray-900 dark:text-white leading-tight">Super Admin</h2>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('platform.stores.index') }}" class="cl-btn cl-btn-ghost">Tiendas</a>
                <a href="{{ route('platform.users.index') }}" class="cl-btn cl-btn-ghost">Usuarios</a>
                <a href="{{ route('platform.payments.index') }}" class="cl-btn cl-btn-ghost">Pagos</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="cl-surface p-6">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Tiendas</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ number_format($storesCount) }}</div>
                </div>
                <div class="cl-surface p-6">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Tiendas activas</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ number_format($activeStoresCount) }}</div>
                </div>
                <div class="cl-surface p-6">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Usuarios</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ number_format($usersCount) }}</div>
                </div>
            </div>

            <div class="cl-surface p-6">
                <div class="flex items-center justify-between gap-3">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Tiendas recientes</div>
                    <a href="{{ route('platform.stores.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-300 dark:hover:text-indigo-200">
                        Ver todas
                    </a>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b border-gray-200 dark:border-gray-800 dark:text-gray-400">
                                <th class="py-2 pr-3">Tienda</th>
                                <th class="py-2 px-3">Plan</th>
                                <th class="py-2 px-3">Estatus</th>
                                <th class="py-2 px-3">Vence</th>
                                <th class="py-2 pl-3 text-right"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stores as $store)
                                <tr class="border-b border-gray-100 dark:border-gray-900/60">
                                    <td class="py-3 pr-3">
                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $store->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $store->code }}</div>
                                    </td>
                                    <td class="py-3 px-3 text-gray-900 dark:text-white">{{ strtoupper($store->plan) }}</td>
                                    <td class="py-3 px-3">
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold border border-gray-200 bg-gray-50 text-gray-800 dark:border-gray-800 dark:bg-gray-950/30 dark:text-gray-200">
                                            {{ $store->subscription_status }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-gray-700 dark:text-gray-200">
                                        {{ $store->subscription_ends_at ? $store->subscription_ends_at->format('Y-m-d') : '—' }}
                                    </td>
                                    <td class="py-3 pl-3 text-right">
                                        <a href="{{ route('platform.stores.edit', $store) }}" class="inline-flex items-center justify-center px-3 py-2 rounded-lg border border-gray-300/80 bg-white/60 text-sm font-semibold text-gray-900 shadow-sm hover:bg-white/80 transition dark:border-gray-800/80 dark:bg-gray-950/20 dark:text-white dark:hover:bg-gray-900">
                                            Administrar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $stores->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
