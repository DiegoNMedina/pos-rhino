<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            (() => {
                const stored = localStorage.getItem('theme');
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = stored ?? (prefersDark ? 'dark' : 'light');
                document.documentElement.classList.toggle('dark', theme === 'dark');
            })();
        </script>
    </head>
    <body class="font-sans antialiased text-gray-900 bg-gray-100 dark:bg-gray-950 dark:text-gray-100">
        <div class="min-h-screen bg-gradient-to-b from-white to-gray-100 dark:from-gray-950 dark:to-gray-900">
            <header class="border-b border-gray-100 bg-white/80 backdrop-blur dark:bg-gray-950/80 dark:border-gray-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                    <a href="/" class="inline-flex items-center gap-3">
                        <x-application-logo class="h-9 w-9 text-indigo-600" />
                        <div class="leading-tight">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ config('app.name') }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Caja • Inventario • Reportes</div>
                        </div>
                    </a>

                    @if (Route::has('login'))
                        <nav class="flex items-center gap-2">
                            @auth
                                <a href="{{ route('pricing') }}" class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    Precios
                                </a>
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    Inicio
                                </a>
                                <a href="{{ route('pos.index') }}" class="inline-flex items-center px-4 py-2 rounded-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700">
                                    Ir al POS
                                </a>
                            @else
                                <a href="{{ route('pricing') }}" class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    Precios
                                </a>
                                <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    Ingresar
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 rounded-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700">
                                        Crear cuenta
                                    </a>
                                @endif
                            @endauth
                        </nav>
                    @endif
                </div>
            </header>

            <main>
                <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                        <div>
                            <div class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold">
                                {{ config('app.name') }}
                            </div>
                            <h1 class="mt-4 text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900">
                                Punto de venta profesional para tu negocio
                            </h1>
                            <p class="mt-4 text-lg text-gray-600 leading-relaxed">
                                Vende rápido, administra productos y consulta reportes desde un panel claro y moderno.
                            </p>

                            <div class="mt-8 flex flex-col sm:flex-row gap-3">
                                <a href="{{ auth()->check() ? route('dashboard') : (Route::has('login') ? route('login') : '#') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-md bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                                    Comenzar
                                </a>
                                <a href="{{ route('pricing') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-md border border-gray-300 bg-white text-gray-900 font-semibold hover:bg-gray-50">
                                    Ver precios
                                </a>
                                <a href="{{ route('pos.index') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-md border border-gray-300 bg-white text-gray-900 font-semibold hover:bg-gray-50">
                                    Ver POS
                                </a>
                            </div>
                        </div>

                        <div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-2xl p-6 sm:p-8 dark:bg-gray-900 dark:ring-gray-800">
                            <div class="text-sm font-semibold text-gray-900">Incluye</div>
                            <div class="mt-4 grid grid-cols-1 gap-4">
                                <div class="flex items-start gap-3">
                                    <div class="h-9 w-9 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center font-semibold">P</div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">Productos</div>
                                        <div class="text-sm text-gray-600">Altas rápidas, códigos y control de stock.</div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="h-9 w-9 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center font-semibold">V</div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">Ventas</div>
                                        <div class="text-sm text-gray-600">Registro, historial y detalle por ticket.</div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="h-9 w-9 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center font-semibold">R</div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">Reportes</div>
                                        <div class="text-sm text-gray-600">Totales por día y top productos.</div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="h-9 w-9 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center font-semibold">U</div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">Usuarios y roles</div>
                                        <div class="text-sm text-gray-600">Administra accesos de forma simple.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white/70 border border-gray-200 rounded-xl p-6 dark:bg-gray-900/50 dark:border-gray-800">
                            <div class="text-sm font-semibold text-gray-900">Interfaz moderna</div>
                            <div class="mt-1 text-sm text-gray-600">Diseño claro, rápido y consistente.</div>
                        </div>
                        <div class="bg-white/70 border border-gray-200 rounded-xl p-6 dark:bg-gray-900/50 dark:border-gray-800">
                            <div class="text-sm font-semibold text-gray-900">Seguridad por rol</div>
                            <div class="mt-1 text-sm text-gray-600">Admin/Supervisor/Cajero.</div>
                        </div>
                        <div class="bg-white/70 border border-gray-200 rounded-xl p-6 dark:bg-gray-900/50 dark:border-gray-800">
                            <div class="text-sm font-semibold text-gray-900">Escalable</div>
                            <div class="mt-1 text-sm text-gray-600">Listo para crecer con tu negocio.</div>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="border-t border-gray-100 bg-white dark:bg-gray-950 dark:border-gray-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-sm text-gray-500 flex flex-col sm:flex-row items-center justify-between gap-2">
                    <div>© {{ date('Y') }} {{ config('app.name') }}</div>
                    <div>Hecho para ventas rápidas y control total.</div>
                </div>
            </footer>
        </div>
    </body>
</html>
