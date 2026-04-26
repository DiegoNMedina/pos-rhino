<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
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
    <body class="font-sans text-gray-900 antialiased dark:text-gray-100">
        <div class="min-h-screen cl-bg relative overflow-hidden">
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 cl-grid opacity-60 z-0"></div>
            <div class="relative z-10">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                    <div class="hidden lg:block">
                        <a href="/" class="inline-flex items-center gap-3">
                            <x-application-logo class="h-10 w-10 text-indigo-600" />
                            <div class="leading-tight">
                                <div class="text-xl font-semibold text-gray-900 dark:text-white">{{ config('app.name') }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Punto de venta moderno para tu negocio</div>
                            </div>
                        </a>

                        <div class="mt-10">
                            <div class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">
                                Vende más rápido. Controla mejor.
                            </div>
                            <div class="mt-4 text-gray-600 leading-relaxed dark:text-gray-300">
                                Administra productos, registra ventas y revisa reportes desde un solo lugar.
                            </div>

                            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="cl-surface p-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Productos e inventario</div>
                                    <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">Altas rápidas, códigos, stock y estado.</div>
                                </div>
                                <div class="cl-surface p-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Ventas y reportes</div>
                                    <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">Historial, detalle y métricas por fecha.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="w-full max-w-md mx-auto">
                        <div class="lg:hidden mb-6 text-center">
                            <a href="/" class="inline-flex items-center gap-3">
                                <x-application-logo class="h-10 w-10 text-indigo-600" />
                                <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ config('app.name') }}</div>
                            </a>
                        </div>

                        <div class="cl-surface cl-surface-hover p-6 sm:p-8">
                            {{ $slot }}
                        </div>

                        <div class="mt-6 text-center text-xs text-gray-500 dark:text-gray-400">
                            © {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </body>
</html>
