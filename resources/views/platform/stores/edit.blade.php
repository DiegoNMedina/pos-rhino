<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Plataforma / Tiendas</div>
            <h2 class="mt-1 font-semibold text-xl text-gray-900 dark:text-white leading-tight">Administrar tienda</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
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

            <form method="POST" action="{{ route('platform.stores.update', $store) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="cl-surface p-6">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Datos</div>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <x-input-label for="name" value="Nombre" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $store->name) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="code" value="Código" />
                            <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" value="{{ old('code', $store->code) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('code')" />
                        </div>

                        <div>
                            <x-input-label for="is_active" value="Activa" />
                            <select id="is_active" name="is_active" class="mt-1 block w-full rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30">
                                <option value="1" {{ (string) old('is_active', (int) $store->is_active) === '1' ? 'selected' : '' }}>Sí</option>
                                <option value="0" {{ (string) old('is_active', (int) $store->is_active) === '0' ? 'selected' : '' }}>No</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
                        </div>
                    </div>
                </div>

                <div class="cl-surface p-6">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Membresía</div>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="plan" value="Plan" />
                            <select id="plan" name="plan" class="mt-1 block w-full rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30">
                                <option value="starter" {{ old('plan', $store->plan) === 'starter' ? 'selected' : '' }}>Starter</option>
                                <option value="pro" {{ old('plan', $store->plan) === 'pro' ? 'selected' : '' }}>Pro</option>
                                <option value="enterprise" {{ old('plan', $store->plan) === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('plan')" />
                        </div>

                        <div>
                            <x-input-label for="subscription_status" value="Estatus" />
                            <select id="subscription_status" name="subscription_status" class="mt-1 block w-full rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30">
                                <option value="inactive" {{ old('subscription_status', $store->subscription_status) === 'inactive' ? 'selected' : '' }}>inactive</option>
                                <option value="trialing" {{ old('subscription_status', $store->subscription_status) === 'trialing' ? 'selected' : '' }}>trialing</option>
                                <option value="active" {{ old('subscription_status', $store->subscription_status) === 'active' ? 'selected' : '' }}>active</option>
                                <option value="past_due" {{ old('subscription_status', $store->subscription_status) === 'past_due' ? 'selected' : '' }}>past_due</option>
                                <option value="canceled" {{ old('subscription_status', $store->subscription_status) === 'canceled' ? 'selected' : '' }}>canceled</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('subscription_status')" />
                        </div>

                        <div>
                            <x-input-label for="billing_method" value="Método de pago" />
                            <select id="billing_method" name="billing_method" class="mt-1 block w-full rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30">
                                <option value="">—</option>
                                <option value="stripe" {{ old('billing_method', $store->billing_method) === 'stripe' ? 'selected' : '' }}>Stripe</option>
                                <option value="transfer" {{ old('billing_method', $store->billing_method) === 'transfer' ? 'selected' : '' }}>Transferencia</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('billing_method')" />
                        </div>

                        <div>
                            <x-input-label for="trial_ends_at" value="Fin de prueba" />
                            <x-text-input id="trial_ends_at" name="trial_ends_at" type="date" class="mt-1 block w-full" value="{{ old('trial_ends_at', $store->trial_ends_at ? $store->trial_ends_at->format('Y-m-d') : '') }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('trial_ends_at')" />
                        </div>

                        <div>
                            <x-input-label for="subscription_ends_at" value="Vence" />
                            <x-text-input id="subscription_ends_at" name="subscription_ends_at" type="date" class="mt-1 block w-full" value="{{ old('subscription_ends_at', $store->subscription_ends_at ? $store->subscription_ends_at->format('Y-m-d') : '') }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('subscription_ends_at')" />
                        </div>
                    </div>
                </div>

                <div class="cl-surface p-6">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Stripe (opcional)</div>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="stripe_customer_id" value="Customer ID" />
                            <x-text-input id="stripe_customer_id" name="stripe_customer_id" type="text" class="mt-1 block w-full" value="{{ old('stripe_customer_id', $store->stripe_customer_id) }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('stripe_customer_id')" />
                        </div>
                        <div>
                            <x-input-label for="stripe_subscription_id" value="Subscription ID" />
                            <x-text-input id="stripe_subscription_id" name="stripe_subscription_id" type="text" class="mt-1 block w-full" value="{{ old('stripe_subscription_id', $store->stripe_subscription_id) }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('stripe_subscription_id')" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2">
                    @if ($store->stripe_customer_id)
                        <form method="POST" action="{{ route('platform.stores.portal', $store) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gradient-to-r from-emerald-600 to-teal-500 text-white text-sm font-semibold shadow-sm hover:from-emerald-500 hover:to-teal-400 transition">
                                Administrar en Stripe
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('platform.stores.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300/80 bg-white/60 text-sm font-semibold text-gray-900 shadow-sm hover:bg-white/80 transition dark:border-gray-800/80 dark:bg-gray-950/20 dark:text-white dark:hover:bg-gray-900">
                        Volver
                    </a>
                    <x-primary-button>Guardar</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
