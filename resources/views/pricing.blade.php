<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Precios · {{ config('app.name') }}</title>

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
                            <div class="text-xs text-gray-500 dark:text-gray-400">Membresías</div>
                        </div>
                    </a>

                    <nav class="flex items-center gap-2">
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-900/40">
                                Inicio
                            </a>
                            <a href="{{ route('pos.index') }}" class="inline-flex items-center px-4 py-2 rounded-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700">
                                Ir al POS
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-900/40">
                                Ingresar
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 rounded-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700">
                                    Crear cuenta
                                </a>
                            @endif
                        @endauth
                    </nav>
                </div>
            </header>

            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                @if (session('error'))
                    <div class="mb-6 cl-surface p-4 border-amber-200/70 bg-amber-50/60 text-amber-900 dark:border-amber-900/40 dark:bg-amber-950/20 dark:text-amber-200">
                        <div class="font-semibold">{{ session('error') }}</div>
                    </div>
                @endif

                @php
                    $hasActiveSubscription = $store && in_array($store->subscription_status, ['active', 'trialing'], true);
                @endphp

                @if ($hasActiveSubscription)
                    <div class="mb-6 cl-surface p-4 border-emerald-200/70 bg-emerald-50/60 text-emerald-900 dark:border-emerald-900/40 dark:bg-emerald-950/20 dark:text-emerald-200">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="font-semibold">Membresía activa</div>
                                <div class="text-sm mt-1 text-emerald-900/90 dark:text-emerald-200/90">
                                    Tu plan actual es <span class="font-semibold">{{ strtoupper($store->plan) }}</span>.
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('billing.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-emerald-300/70 bg-white/60 text-sm font-semibold text-emerald-900 shadow-sm hover:bg-white/80 transition dark:border-emerald-900/40 dark:bg-gray-950/20 dark:text-emerald-200 dark:hover:bg-gray-900">
                                    Ver facturación
                                </a>
                                @if ($store->billing_method === 'stripe')
                                    <form method="POST" action="{{ route('billing.portal') }}">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gradient-to-r from-emerald-600 to-teal-500 text-white text-sm font-semibold shadow-sm hover:from-emerald-500 hover:to-teal-400 transition">
                                            Administrar en Stripe
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <div class="text-center">
                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold dark:bg-indigo-950/30 dark:text-indigo-200">
                        Precios
                    </div>
                    <h1 class="mt-4 text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white">
                        Elige tu plan de CajaLink
                    </h1>
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-300">
                        Puedes pagar con Stripe o por transferencia.
                    </p>
                </div>

                <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div id="plan-starter" class="cl-surface p-6 {{ request('plan') === 'starter' ? 'ring-1 ring-indigo-300 dark:ring-indigo-700' : '' }} {{ $hasActiveSubscription && $store->plan === 'starter' ? 'ring-2 ring-emerald-300 dark:ring-emerald-800' : '' }}">
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">Starter</div>
                        <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">$299<span class="text-sm font-medium text-gray-500 dark:text-gray-400">/mes</span></div>
                        <ul class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                            <li>POS + productos</li>
                            <li>Tickets e historial de ventas</li>
                            <li>Reportes básicos</li>
                        </ul>
                        <div class="mt-6">
                            @auth
                                @if (auth()->user()->store_id)
                                    @if ($hasActiveSubscription && $store->plan === 'starter')
                                        <a href="{{ route('billing.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold shadow-sm hover:bg-emerald-700 transition">
                                            Plan actual
                                        </a>
                                    @else
                                        <form method="POST" action="{{ route('billing.checkout', 'starter') }}">
                                            @csrf
                                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-600 to-indigo-500 text-white text-sm font-semibold shadow-sm hover:from-indigo-500 hover:to-indigo-400 transition">
                                                Pagar con Stripe
                                            </button>
                                        </form>
                                    @endif
                                    <a href="#transferencia" class="mt-2 w-full inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-900 font-semibold hover:bg-gray-50 transition dark:bg-gray-950/20 dark:text-white dark:border-gray-800 dark:hover:bg-gray-900">
                                        Transferencia
                                    </a>
                                @else
                                    <a href="{{ route('dashboard') }}" class="w-full inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-900 font-semibold hover:bg-gray-50 transition dark:bg-gray-950/20 dark:text-white dark:border-gray-800 dark:hover:bg-gray-900">
                                        Asignar tienda
                                    </a>
                                @endif
                            @else
                                <a href="{{ Route::has('register') ? route('register') : '#' }}" class="w-full inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-600 to-indigo-500 text-white text-sm font-semibold shadow-sm hover:from-indigo-500 hover:to-indigo-400 transition">
                                    Crear cuenta
                                </a>
                            @endauth
                        </div>
                    </div>

                    <div id="plan-pro" class="cl-surface p-6 ring-1 ring-indigo-200 dark:ring-indigo-900/50 {{ request('plan') === 'pro' ? 'ring-2 ring-indigo-400 dark:ring-indigo-600' : '' }} {{ $hasActiveSubscription && $store->plan === 'pro' ? 'ring-2 ring-emerald-300 dark:ring-emerald-800' : '' }}">
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">Pro</div>
                        <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">$499<span class="text-sm font-medium text-gray-500 dark:text-gray-400">/mes</span></div>
                        <ul class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                            <li>Todo en Starter</li>
                            <li>Usuarios por roles</li>
                            <li>Configuración de ticket / impresora / báscula</li>
                            <li>Lectura de báscula (Rhino)</li>
                            <li>Reportes avanzados</li>
                        </ul>
                        <div class="mt-6">
                            @auth
                                @if (auth()->user()->store_id)
                                    @if ($hasActiveSubscription && $store->plan === 'pro')
                                        <a href="{{ route('billing.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold shadow-sm hover:bg-emerald-700 transition">
                                            Plan actual
                                        </a>
                                    @else
                                        <form method="POST" action="{{ route('billing.checkout', 'pro') }}">
                                            @csrf
                                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gradient-to-r from-emerald-600 to-teal-500 text-white text-sm font-semibold shadow-sm hover:from-emerald-500 hover:to-teal-400 transition">
                                                Pagar con Stripe
                                            </button>
                                        </form>
                                    @endif
                                    <a href="#transferencia" class="mt-2 w-full inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-900 font-semibold hover:bg-gray-50 transition dark:bg-gray-950/20 dark:text-white dark:border-gray-800 dark:hover:bg-gray-900">
                                        Transferencia
                                    </a>
                                @else
                                    <a href="{{ route('dashboard') }}" class="w-full inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-900 font-semibold hover:bg-gray-50 transition dark:bg-gray-950/20 dark:text-white dark:border-gray-800 dark:hover:bg-gray-900">
                                        Asignar tienda
                                    </a>
                                @endif
                            @else
                                <a href="{{ Route::has('register') ? route('register') : '#' }}" class="w-full inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gradient-to-r from-emerald-600 to-teal-500 text-white text-sm font-semibold shadow-sm hover:from-emerald-500 hover:to-teal-400 transition">
                                    Crear cuenta
                                </a>
                            @endauth
                        </div>
                    </div>

                    <div id="plan-enterprise" class="cl-surface p-6 {{ request('plan') === 'enterprise' ? 'ring-1 ring-indigo-300 dark:ring-indigo-700' : '' }} {{ $hasActiveSubscription && $store->plan === 'enterprise' ? 'ring-2 ring-emerald-300 dark:ring-emerald-800' : '' }}">
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">Enterprise</div>
                        <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">A medida</div>
                        <ul class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                            <li>Soporte prioritario</li>
                            <li>Integraciones</li>
                            <li>Acuerdos y onboarding</li>
                        </ul>
                        <div class="mt-6">
                            @auth
                                @if (auth()->user()->store_id)
                                    @if ($hasActiveSubscription && $store->plan === 'enterprise')
                                        <a href="{{ route('billing.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold shadow-sm hover:bg-emerald-700 transition">
                                            Plan actual
                                        </a>
                                    @else
                                        <form method="POST" action="{{ route('billing.checkout', 'enterprise') }}">
                                            @csrf
                                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-900 font-semibold hover:bg-gray-50 transition dark:bg-gray-950/20 dark:text-white dark:border-gray-800 dark:hover:bg-gray-900">
                                                Cotizar / Stripe
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <a href="{{ route('dashboard') }}" class="w-full inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-900 font-semibold hover:bg-gray-50 transition dark:bg-gray-950/20 dark:text-white dark:border-gray-800 dark:hover:bg-gray-900">
                                        Asignar tienda
                                    </a>
                                @endif
                            @else
                                <a href="{{ Route::has('login') ? route('login') : '#' }}" class="w-full inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-900 font-semibold hover:bg-gray-50 transition dark:bg-gray-950/20 dark:text-white dark:border-gray-800 dark:hover:bg-gray-900">
                                    Ingresar
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>

                <div id="transferencia" class="mt-10 cl-surface p-6">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Métodos de pago</div>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                        Stripe (tarjeta) o Transferencia (validación manual por Super Admin).
                    </div>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="rounded-xl border border-gray-200/80 bg-white/60 p-4 dark:border-gray-800/80 dark:bg-gray-950/20">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">Stripe</div>
                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                Al pagar, tu tienda se activa automáticamente mediante webhook.
                            </div>
                        </div>
                        <div class="rounded-xl border border-gray-200/80 bg-white/60 p-4 dark:border-gray-800/80 dark:bg-gray-950/20">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">Transferencia</div>
                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                Envía el comprobante al Super Admin para que active tu tienda manualmente.
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="border-t border-gray-100 bg-white dark:bg-gray-950 dark:border-gray-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-sm text-gray-500 flex flex-col sm:flex-row items-center justify-between gap-2">
                    <div>© {{ date('Y') }} {{ config('app.name') }}</div>
                    <div>Planes y membresías.</div>
                </div>
            </footer>
        </div>
    </body>
</html>
