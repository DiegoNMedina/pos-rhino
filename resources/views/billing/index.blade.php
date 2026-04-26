<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Cuenta / Facturación</div>
                <h2 class="mt-1 font-semibold text-xl text-gray-900 dark:text-white leading-tight">Facturación</h2>
            </div>
            <div class="flex flex-wrap gap-2">
                @if ($store->billing_method === 'stripe')
                    <form method="POST" action="{{ route('billing.portal') }}">
                        @csrf
                        <button type="submit" class="cl-btn cl-btn-primary">Administrar tarjeta</button>
                    </form>
                @endif
                @if ($store->billing_method === 'stripe' && $store->stripe_subscription_id && $store->subscription_status !== 'canceled')
                    <form method="POST" action="{{ route('billing.cancel') }}">
                        @csrf
                        <x-danger-button>Cancelar suscripción</x-danger-button>
                    </form>
                @endif
                <a href="{{ route('pricing') }}" class="cl-btn cl-btn-ghost">Ver planes</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="cl-surface p-4 border-emerald-200/70 bg-emerald-50/60 text-emerald-900 dark:border-emerald-900/40 dark:bg-emerald-950/20 dark:text-emerald-200">
                    <div class="font-semibold">{{ session('success') }}</div>
                </div>
            @endif
            @if (session('error'))
                <div class="cl-surface p-4 border-amber-200/70 bg-amber-50/60 text-amber-900 dark:border-amber-900/40 dark:bg-amber-950/20 dark:text-amber-200">
                    <div class="font-semibold">{{ session('error') }}</div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="cl-surface p-6">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Tienda</div>
                    <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $store->name }}</div>
                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $store->code }}</div>
                </div>
                <div class="cl-surface p-6">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Plan</div>
                    <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ strtoupper($store->plan) }}</div>
                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Método: {{ $store->billing_method ?? '—' }}</div>
                </div>
                <div class="cl-surface p-6">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Estatus</div>
                    <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $store->subscription_status }}</div>
                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Vence: {{ $store->subscription_ends_at ? $store->subscription_ends_at->format('Y-m-d') : '—' }}
                    </div>
                </div>
            </div>

            @if ($store->billing_method === 'stripe' && $store->stripe_subscription_id)
                <div class="cl-surface p-6">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Cambiar plan</div>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach (['starter' => 'Starter', 'pro' => 'Pro', 'enterprise' => 'Enterprise'] as $key => $label)
                            <form method="POST" action="{{ route('billing.plan', $key) }}" class="flex">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300/80 bg-white/60 text-sm font-semibold text-gray-900 shadow-sm hover:bg-white/80 transition dark:border-gray-800/80 dark:bg-gray-950/20 dark:text-white dark:hover:bg-gray-900 {{ $store->plan === $key ? 'opacity-60 cursor-not-allowed' : '' }}" {{ $store->plan === $key ? 'disabled' : '' }}>
                                    {{ $store->plan === $key ? $label.' (Actual)' : 'Cambiar a '.$label }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="cl-surface p-6">
                <div class="flex items-center justify-between gap-3">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Pagos recientes</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Últimos 25 eventos</div>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b border-gray-200 dark:border-gray-800 dark:text-gray-400">
                                <th class="py-2 pr-3">Fecha</th>
                                <th class="py-2 px-3">Estatus</th>
                                <th class="py-2 px-3">Monto</th>
                                <th class="py-2 px-3">Periodo</th>
                                <th class="py-2 pl-3 text-right">Referencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payments as $payment)
                                <tr class="border-b border-gray-100 dark:border-gray-900/60">
                                    <td class="py-3 pr-3 text-gray-700 dark:text-gray-200">
                                        {{ $payment->created_at ? $payment->created_at->format('Y-m-d H:i') : '—' }}
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $payment->event_type ?? '—' }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold border border-gray-200 bg-gray-50 text-gray-800 dark:border-gray-800 dark:bg-gray-950/30 dark:text-gray-200">
                                            {{ $payment->status ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-gray-900 dark:text-white">
                                        @if ($payment->amount !== null)
                                            {{ number_format((float) $payment->amount, 2) }} {{ $payment->currency ?? '' }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-gray-700 dark:text-gray-200">
                                        {{ $payment->period_start_at ? $payment->period_start_at->format('Y-m-d') : '—' }}
                                        <span class="text-gray-400">→</span>
                                        {{ $payment->period_end_at ? $payment->period_end_at->format('Y-m-d') : '—' }}
                                    </td>
                                    <td class="py-3 pl-3 text-right text-gray-700 dark:text-gray-200">
                                        {{ $payment->reference_id ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-gray-500 dark:text-gray-400">
                                        Aún no hay pagos registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
