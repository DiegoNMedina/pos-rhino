<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-500 dark:text-gray-400">POS / Caja</div>
                <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">Punto de Venta</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $branch ? $branch->name : 'Sin sucursal' }} · {{ $register ? $register->name : 'Sin caja' }}
                </div>
            </div>
            <div class="text-right">
                <div class="text-xs text-gray-500 dark:text-gray-400">Peso</div>
                <div class="mt-1 text-lg font-semibold tabular-nums text-gray-900 dark:text-white" id="pos-weight">--</div>
            </div>
        </div>
    </x-slot>

    <div class="py-6" data-pos-page>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (! $branch || ! $register)
                <div class="cl-surface p-6 border-amber-200/70 bg-amber-50/60 text-amber-900 dark:border-amber-900/40 dark:bg-amber-950/20 dark:text-amber-200">
                    <div class="font-semibold">Falta configuración inicial</div>
                    <div class="text-sm mt-1 text-amber-800/90 dark:text-amber-200/90">Crea tu primera sucursal, tu caja y agrega productos para comenzar a vender.</div>
                </div>
            @endif

            <input type="hidden" id="pos-branch-id" value="{{ $branch ? $branch->id : 0 }}">
            <input type="hidden" id="pos-register-id" value="{{ $register ? $register->id : 0 }}">
            <input type="hidden" id="pos-ticket-url-template" value="{{ route('pos.ticket', 0) }}">
            <input type="hidden" id="pos-scale-poll-ms" value="{{ $scalePollMs ?? 1000 }}">
            <input type="hidden" id="pos-printer-mode" value="{{ $printerMode ?? 'browser' }}">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mt-6">
                <div class="lg:col-span-7">
                    <div class="cl-surface p-6">
                        <div class="flex items-center gap-2">
                            <input id="pos-search" type="text" class="w-full rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30 dark:placeholder:text-gray-500" placeholder="Código, código de barras o nombre" data-pos-disable-on-save>
                            <button id="pos-search-btn" type="button" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-600 to-indigo-500 text-white text-sm font-semibold shadow-sm hover:from-indigo-500 hover:to-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white transition disabled:opacity-60 disabled:cursor-not-allowed disabled:saturate-50 dark:focus:ring-offset-gray-950" data-pos-disable-on-save>Buscar</button>
                        </div>

                        <div class="mt-4 grid grid-cols-1 gap-2" id="pos-search-results"></div>

                        <div class="mt-6 flex items-center justify-between">
                            <div class="text-sm text-gray-600 dark:text-gray-300">Lectura de báscula</div>
                            <button id="pos-weight-btn" type="button" class="inline-flex items-center justify-center px-3 py-2 rounded-lg border border-gray-300/80 bg-white/60 text-sm font-semibold text-gray-900 shadow-sm hover:bg-white/80 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white transition disabled:opacity-60 disabled:cursor-not-allowed disabled:saturate-50 dark:border-gray-800/80 dark:bg-gray-950/20 dark:text-white dark:hover:bg-gray-900 dark:focus:ring-offset-gray-950" data-pos-disable-on-save>Actualizar peso</button>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="cl-surface p-6">
                        <div class="flex items-center justify-between">
                            <div class="text-gray-900 dark:text-white font-semibold tracking-tight">Carrito</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400" id="pos-last-sale"></div>
                        </div>

                        <div id="pos-cart-empty" class="mt-4 text-sm text-gray-500 dark:text-gray-400">Agrega productos para iniciar una venta.</div>

                        <div class="mt-4 overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500 border-b border-gray-200 dark:border-gray-800 dark:text-gray-400">
                                        <th class="py-2 pr-2">Producto</th>
                                        <th class="py-2 px-2 w-32">Cant.</th>
                                        <th class="py-2 px-2 w-32">Precio</th>
                                        <th class="py-2 px-2 w-32 text-right">Total</th>
                                        <th class="py-2 pl-2 w-24"></th>
                                    </tr>
                                </thead>
                                <tbody id="pos-cart-body"></tbody>
                            </table>
                        </div>

                        <div class="mt-6 border-t border-gray-200 dark:border-gray-800 pt-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-600 dark:text-gray-300">Subtotal</div>
                                <div class="text-lg font-semibold tabular-nums text-gray-900 dark:text-white" id="pos-subtotal">$0.00</div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="flex-1">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Pago</div>
                                    <div class="mt-1 flex items-center gap-3">
                                        <label class="inline-flex items-center gap-2 text-sm text-gray-800 dark:text-gray-200">
                                            <input type="radio" name="payment_method" value="cash" checked data-pos-disable-on-save>
                                            <span>Efectivo</span>
                                        </label>
                                        <label class="inline-flex items-center gap-2 text-sm text-gray-800 dark:text-gray-200">
                                            <input type="radio" name="payment_method" value="card" data-pos-disable-on-save>
                                            <span>Tarjeta</span>
                                        </label>
                                        <label class="inline-flex items-center gap-2 text-sm text-gray-800 dark:text-gray-200">
                                            <input type="radio" name="payment_method" value="mixed" data-pos-disable-on-save>
                                            <span>Mixto</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="w-40">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Recibido</div>
                                    <input id="pos-cash-received" type="number" step="0.01" min="0" class="mt-1 w-full rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 text-sm shadow-sm backdrop-blur placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30 dark:placeholder:text-gray-500" placeholder="0.00" data-pos-disable-on-save>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-600 dark:text-gray-300">Cambio</div>
                                <div class="text-lg font-semibold tabular-nums text-gray-900 dark:text-white" id="pos-change">$0.00</div>
                            </div>

                            <button id="pos-checkout-btn" type="button" class="w-full mt-2 inline-flex items-center justify-center px-4 py-3 rounded-lg bg-gradient-to-r from-emerald-600 to-teal-500 text-white font-semibold shadow-sm hover:from-emerald-500 hover:to-teal-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-white transition disabled:opacity-60 disabled:cursor-not-allowed disabled:saturate-50 dark:focus:ring-offset-gray-950" data-pos-disable-on-save>Cobrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="pos-sale-success-modal" class="fixed inset-0 z-50 hidden items-center justify-center px-4">
        <div class="absolute inset-0 bg-black/55 backdrop-blur-sm" data-close-modal></div>
        <div class="relative w-full max-w-md cl-surface p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Venta confirmada</div>
                    <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                        Venta <span id="pos-success-sale-id">—</span>
                    </div>
                </div>
                <button type="button" class="h-10 w-10 inline-flex items-center justify-center rounded-md text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition dark:hover:bg-gray-800 dark:hover:text-white" data-close-modal>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <div class="mt-5 space-y-2">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600 dark:text-gray-300">Total</div>
                    <div class="text-sm font-semibold tabular-nums text-gray-900 dark:text-white" id="pos-success-total">—</div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600 dark:text-gray-300">Cambio</div>
                    <div class="text-sm font-semibold tabular-nums text-gray-900 dark:text-white" id="pos-success-change">—</div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-2">
                <button type="button" class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300/80 bg-white/60 text-sm font-semibold text-gray-900 shadow-sm hover:bg-white/80 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white transition dark:bg-gray-950/20 dark:text-white dark:border-gray-800/80 dark:hover:bg-gray-900 dark:focus:ring-offset-gray-950" id="pos-success-print-btn">
                    Imprimir ticket
                </button>
                <button type="button" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-600 to-indigo-500 text-white text-sm font-semibold shadow-sm hover:from-indigo-500 hover:to-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white transition dark:focus:ring-offset-gray-950" data-close-modal>
                    Nueva venta
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
