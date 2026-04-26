<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Administración / Clientes</div>
                <h2 class="mt-1 font-semibold text-xl text-gray-900 dark:text-white leading-tight">Clientes</h2>
            </div>
            <a href="{{ route('admin.customers.create') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-600 to-indigo-500 font-semibold text-sm text-white shadow-sm hover:from-indigo-500 hover:to-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white transition dark:focus:ring-offset-gray-950">
                Agregar cliente
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="cl-surface p-6">
                @if (session('success'))
                    <div class="mb-4 cl-surface p-4 border border-emerald-200/70 bg-emerald-50/60 text-emerald-900 dark:border-emerald-900/40 dark:bg-emerald-950/20 dark:text-emerald-200">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-col sm:flex-row sm:items-center gap-2">
                    <input type="text" name="q" value="{{ $q }}" class="w-full rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30 dark:placeholder:text-gray-500" placeholder="Buscar por nombre, teléfono o email">
                    <x-primary-button type="submit">Buscar</x-primary-button>
                </form>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b border-gray-200 dark:border-gray-800 dark:text-gray-400">
                                <th class="py-2 pr-3">Nombre</th>
                                <th class="py-2 px-3">Teléfono</th>
                                <th class="py-2 px-3">Email</th>
                                <th class="py-2 px-3">RFC</th>
                                <th class="py-2 pl-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse ($customers as $customer)
                                <tr class="hover:bg-white/60 dark:hover:bg-gray-900/50 transition">
                                    <td class="py-3 pr-3 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                        {{ $customer->name }}
                                    </td>
                                    <td class="py-3 px-3 text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                        {{ $customer->phone ?: '—' }}
                                    </td>
                                    <td class="py-3 px-3 text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                        {{ $customer->email ?: '—' }}
                                    </td>
                                    <td class="py-3 px-3 text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                        {{ $customer->tax_id ?: '—' }}
                                    </td>
                                    <td class="py-3 pl-3 whitespace-nowrap text-right">
                                        <a href="{{ route('admin.customers.edit', $customer) }}" class="text-indigo-700 hover:text-indigo-800 dark:text-indigo-300 dark:hover:text-indigo-200 text-sm font-semibold">
                                            Editar
                                        </a>

                                        <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ml-3 text-red-600 hover:text-red-800 dark:text-red-300 dark:hover:text-red-200 text-sm font-semibold" onclick="return confirm('¿Eliminar cliente?');">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-gray-500 dark:text-gray-400">
                                        Sin clientes
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
