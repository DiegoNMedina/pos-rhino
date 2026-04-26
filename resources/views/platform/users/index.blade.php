<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Plataforma / Usuarios</div>
            <h2 class="mt-1 font-semibold text-xl text-gray-900 dark:text-white leading-tight">Usuarios</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="cl-surface p-4 border-emerald-200/70 bg-emerald-50/60 text-emerald-900 dark:border-emerald-900/40 dark:bg-emerald-950/20 dark:text-emerald-200">
                    <div class="font-semibold">{{ session('success') }}</div>
                </div>
            @endif

            <div class="cl-surface p-6">
                <form class="flex flex-col sm:flex-row gap-3" method="GET" action="{{ route('platform.users.index') }}">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre, email o rol" class="flex-1 rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30 dark:placeholder:text-gray-500" />
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-600 to-indigo-500 text-white text-sm font-semibold shadow-sm hover:from-indigo-500 hover:to-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white transition dark:focus:ring-offset-gray-950">
                        Buscar
                    </button>
                </form>
            </div>

            <div class="cl-surface p-6">
                <div class="text-sm font-semibold text-gray-900 dark:text-white">Crear usuario de soporte</div>
                <form class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3" method="POST" action="{{ route('platform.users.support.store') }}">
                    @csrf
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Nombre" class="rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30 dark:placeholder:text-gray-500" required />
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" class="rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30 dark:placeholder:text-gray-500" required />
                    <input type="password" name="password" placeholder="Contraseña (mín. 8)" class="rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30 dark:placeholder:text-gray-500" required />
                    <div class="sm:col-span-3 flex items-center justify-end">
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gradient-to-r from-emerald-600 to-teal-500 text-white text-sm font-semibold shadow-sm hover:from-emerald-500 hover:to-teal-400 transition">
                            Crear
                        </button>
                    </div>
                </form>
                @if ($errors->any())
                    <div class="mt-3 text-sm text-amber-900 dark:text-amber-200">
                        {{ $errors->first() }}
                    </div>
                @endif
            </div>

            <div class="cl-surface p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b border-gray-200 dark:border-gray-800 dark:text-gray-400">
                                <th class="py-2 pr-3">Usuario</th>
                                <th class="py-2 px-3">Rol</th>
                                <th class="py-2 px-3">Tienda</th>
                                <th class="py-2 pl-3 text-right">ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr class="border-b border-gray-100 dark:border-gray-900/60">
                                    <td class="py-3 pr-3">
                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                    </td>
                                    <td class="py-3 px-3 text-gray-900 dark:text-white">{{ $user->role }}</td>
                                    <td class="py-3 px-3 text-gray-700 dark:text-gray-200">
                                        {{ $user->store ? $user->store->name : '—' }}
                                    </td>
                                    <td class="py-3 pl-3 text-right text-gray-700 dark:text-gray-200">{{ $user->id }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-10 text-center text-gray-500 dark:text-gray-400">No hay usuarios.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
