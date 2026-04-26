<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Soporte / Conversaciones</div>
            <h2 class="mt-1 font-semibold text-xl text-gray-900 dark:text-white leading-tight">Bandeja de soporte</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (!empty($supportUnavailable))
                <div class="cl-surface p-6 border-amber-200/70 bg-amber-50/60 text-amber-900 dark:border-amber-900/40 dark:bg-amber-950/20 dark:text-amber-200">
                    <div class="font-semibold">El módulo de soporte no está disponible por el momento.</div>
                </div>
            @endif
            <div class="cl-surface p-6">
                <form class="flex flex-col sm:flex-row gap-3" method="GET" action="{{ route('support.admin.index') }}">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por tienda o código" class="flex-1 rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30 dark:placeholder:text-gray-500" />
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-600 to-indigo-500 text-white text-sm font-semibold shadow-sm hover:from-indigo-500 hover:to-indigo-400 transition">
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
                                <th class="py-2 px-3">Estatus</th>
                                <th class="py-2 pl-3 text-right">Último mensaje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($conversations as $conversation)
                                <tr class="border-b border-gray-100 dark:border-gray-900/60">
                                    <td class="py-3 pr-3">
                                        <a href="{{ route('support.admin.show', $conversation) }}" class="font-semibold text-gray-900 hover:text-indigo-700 dark:text-white dark:hover:text-indigo-300">
                                            {{ $conversation->store ? $conversation->store->name : '—' }}
                                        </a>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $conversation->store ? $conversation->store->code : '' }}</div>
                                    </td>
                                    <td class="py-3 px-3 text-gray-900 dark:text-white">{{ $conversation->status }}</td>
                                    <td class="py-3 pl-3 text-right text-gray-700 dark:text-gray-200">
                                        {{ $conversation->last_message_at ? $conversation->last_message_at->format('Y-m-d H:i') : '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-10 text-center text-gray-500 dark:text-gray-400">No hay conversaciones.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $conversations->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
