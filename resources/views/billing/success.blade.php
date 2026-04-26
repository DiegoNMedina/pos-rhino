<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Membresía
            </h2>
            <a href="{{ route('pricing') }}" class="cl-btn cl-btn-ghost">Ver planes</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="cl-surface p-6 sm:p-8">
                <div class="flex items-start gap-4">
                    <div class="h-12 w-12 rounded-2xl bg-emerald-500/15 border border-emerald-500/25 flex items-center justify-center">
                        <svg class="h-6 w-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pago recibido</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                            Si pagaste con Stripe, la activación puede tardar unos segundos en reflejarse.
                        </p>
                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white/50 dark:bg-gray-900/40 p-4">
                                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tienda</div>
                                <div class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $store->name }}</div>
                            </div>
                            <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white/50 dark:bg-gray-900/40 p-4">
                                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Estatus</div>
                                <div class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $store->subscription_status }}</div>
                            </div>
                        </div>
                        <div class="mt-6 flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('pos.index') }}" class="cl-btn cl-btn-primary">Ir al POS</a>
                            <a href="{{ route('dashboard') }}" class="cl-btn cl-btn-ghost">Ir al inicio</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

