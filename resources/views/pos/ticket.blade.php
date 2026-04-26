<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Ticket #{{ $sale->id }}</title>
        <style>
            :root { --paper-width: {{ (int) $paperWidthMm }}mm; }
            @page { size: var(--paper-width) auto; margin: 6mm; }
            html, body { padding: 0; margin: 0; }
            body { width: var(--paper-width); font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; font-size: 12px; color: #111827; }
            .center { text-align: center; }
            .muted { color: #6b7280; }
            .row { display: flex; justify-content: space-between; gap: 8px; }
            .hr { border-top: 1px dashed #9ca3af; margin: 10px 0; }
            .items { width: 100%; border-collapse: collapse; }
            .items td { padding: 6px 0; vertical-align: top; }
            .right { text-align: right; }
            .bold { font-weight: 700; }
        </style>
    </head>
    <body>
        <div class="center bold" style="font-size: 14px;">{{ $businessName }}</div>
        @if ($businessAddress)
            <div class="center muted">{{ $businessAddress }}</div>
        @endif
        @if ($businessPhone)
            <div class="center muted">Tel: {{ $businessPhone }}</div>
        @endif

        <div class="hr"></div>

        <div class="row">
            <div>Venta</div>
            <div class="bold">#{{ $sale->id }}</div>
        </div>
        <div class="row">
            <div>Fecha</div>
            <div>{{ $sale->created_at ? $sale->created_at->format('Y-m-d H:i') : '' }}</div>
        </div>
        <div class="row">
            <div>Caja</div>
            <div>{{ $sale->register ? $sale->register->name : '—' }}</div>
        </div>
        <div class="row">
            <div>Cajero</div>
            <div>{{ $sale->user ? $sale->user->name : '—' }}</div>
        </div>

        <div class="hr"></div>

        <table class="items">
            <tbody>
                @foreach ($sale->items as $item)
                    <tr>
                        <td>
                            <div class="bold">{{ $item->name }}</div>
                            <div class="muted">
                                {{ $item->unit_type === 'weight' ? 'Peso' : 'Pieza' }}
                                · {{ number_format((float) $item->quantity, $item->unit_type === 'weight' ? 3 : 0) }}
                                × ${{ number_format((float) $item->unit_price, 2) }}
                            </div>
                        </td>
                        <td class="right bold">${{ number_format((float) $item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="hr"></div>

        <div class="row">
            <div>Subtotal</div>
            <div class="bold">${{ number_format((float) $sale->subtotal, 2) }}</div>
        </div>
        <div class="row">
            <div>Total</div>
            <div class="bold">${{ number_format((float) $sale->total, 2) }}</div>
        </div>
        <div class="row">
            <div>Recibido</div>
            <div class="bold">${{ number_format((float) ($sale->cash_received ?? 0), 2) }}</div>
        </div>
        <div class="row">
            <div>Cambio</div>
            <div class="bold">${{ number_format((float) ($sale->change_due ?? 0), 2) }}</div>
        </div>

        @if ($ticketFooter)
            <div class="hr"></div>
            <div class="center">{{ $ticketFooter }}</div>
        @endif

        @if ($autoprint)
            <script>
                window.addEventListener('load', () => {
                    window.print();
                    window.onafterprint = () => window.close();
                });
            </script>
        @endif
    </body>
</html>
