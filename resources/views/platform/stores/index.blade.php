<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Plataforma / Tiendas</div>
            <h2 class="mt-1 font-semibold text-xl text-gray-900 dark:text-white leading-tight">Tiendas</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="cl-surface p-6">
                <form class="flex flex-col sm:flex-row gap-3" method="GET" action="{{ route('platform.stores.index') }}">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre o código" class="flex-1 rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30 dark:placeholder:text-gray-500" />
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-600 to-indigo-500 text-white text-sm font-semibold shadow-sm hover:from-indigo-500 hover:to-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white transition dark:focus:ring-offset-gray-950">
                        Buscar
                    </button>
                </form>
            </div>

            <div class="cl-surface p-6">
                <div class="overflow-x-auto">
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
                            @forelse ($stores as $store)
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
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-gray-500 dark:text-gray-400">No hay tiendas.</td>
                                </tr>
                            @endforelse
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
