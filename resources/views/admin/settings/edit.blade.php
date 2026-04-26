<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Administración / Configuración</div>
            <h2 class="mt-1 font-semibold text-xl text-gray-900 dark:text-white leading-tight">Configuración</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="cl-surface p-4 border-emerald-200/70 bg-emerald-50/60 text-emerald-900 dark:border-emerald-900/40 dark:bg-emerald-950/20 dark:text-emerald-200">
                    <div class="font-semibold">{{ session('status') }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="cl-surface p-6">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Ticket</div>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <x-input-label for="business_name" value="Nombre del negocio" />
                            <x-text-input id="business_name" name="business_name" type="text" class="mt-1 block w-full" value="{{ old('business_name', $settings['business_name']) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('business_name')" />
                        </div>

                        <div class="sm:col-span-2">
                            <x-input-label for="business_address" value="Dirección (opcional)" />
                            <x-text-input id="business_address" name="business_address" type="text" class="mt-1 block w-full" value="{{ old('business_address', $settings['business_address']) }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('business_address')" />
                        </div>

                        <div>
                            <x-input-label for="business_phone" value="Teléfono (opcional)" />
                            <x-text-input id="business_phone" name="business_phone" type="text" class="mt-1 block w-full" value="{{ old('business_phone', $settings['business_phone']) }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('business_phone')" />
                        </div>

                        <div class="sm:col-span-2">
                            <x-input-label for="ticket_footer" value="Pie del ticket (opcional)" />
                            <textarea id="ticket_footer" name="ticket_footer" rows="3" class="mt-1 block w-full rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30 dark:placeholder:text-gray-500 dark:text-white">{{ old('ticket_footer', $settings['ticket_footer']) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('ticket_footer')" />
                        </div>
                    </div>
                </div>

                <div class="cl-surface p-6">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Impresora térmica</div>
                    <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">Modo navegador imprime con la impresora predeterminada del sistema.</div>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="printer_mode" value="Modo de impresión" />
                            <select id="printer_mode" name="printer_mode" class="mt-1 block w-full rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30">
                                <option value="browser" {{ old('printer_mode', $settings['printer_mode']) === 'browser' ? 'selected' : '' }}>Navegador</option>
                                <option value="escpos" {{ old('printer_mode', $settings['printer_mode']) === 'escpos' ? 'selected' : '' }}>ESC/POS (puente local)</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('printer_mode')" />
                        </div>

                        <div>
                            <x-input-label for="printer_paper_width_mm" value="Ancho de papel" />
                            <select id="printer_paper_width_mm" name="printer_paper_width_mm" class="mt-1 block w-full rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-800/80 dark:bg-gray-950/30">
                                <option value="80" {{ old('printer_paper_width_mm', $settings['printer_paper_width_mm']) === '80' ? 'selected' : '' }}>80 mm</option>
                                <option value="58" {{ old('printer_paper_width_mm', $settings['printer_paper_width_mm']) === '58' ? 'selected' : '' }}>58 mm</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('printer_paper_width_mm')" />
                        </div>
                    </div>
                </div>

                <div class="cl-surface p-6">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Báscula Rhino</div>
                    <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">Intervalo para actualizar la lectura de peso.</div>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="scale_poll_ms" value="Intervalo (ms)" />
                            <x-text-input id="scale_poll_ms" name="scale_poll_ms" type="number" min="250" max="10000" step="50" class="mt-1 block w-full" value="{{ old('scale_poll_ms', $settings['scale_poll_ms']) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('scale_poll_ms')" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <x-primary-button>Guardar cambios</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
