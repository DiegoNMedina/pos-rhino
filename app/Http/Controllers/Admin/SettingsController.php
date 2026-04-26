<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        $user = request()->user();
        if ($user && $user->store && ! $user->store->hasFeature('settings')) {
            return redirect()->route('pricing')->with('error', 'Tu plan no incluye configuraciones.');
        }

        return view('admin.settings.edit', [
            'settings' => [
                'business_name' => AppSetting::getValue('business_name', config('app.name')),
                'business_address' => AppSetting::getValue('business_address', ''),
                'business_phone' => AppSetting::getValue('business_phone', ''),
                'ticket_footer' => AppSetting::getValue('ticket_footer', 'Gracias por su compra.'),
                'printer_mode' => AppSetting::getValue('printer_mode', 'browser'),
                'printer_paper_width_mm' => AppSetting::getValue('printer_paper_width_mm', '80'),
                'scale_poll_ms' => AppSetting::getValue('scale_poll_ms', '1000'),
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        if ($user && $user->store && ! $user->store->hasFeature('settings')) {
            return redirect()->route('pricing')->with('error', 'Tu plan no incluye configuraciones.');
        }

        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:120'],
            'business_address' => ['nullable', 'string', 'max:255'],
            'business_phone' => ['nullable', 'string', 'max:50'],
            'ticket_footer' => ['nullable', 'string', 'max:255'],
            'printer_mode' => ['required', 'in:browser,escpos'],
            'printer_paper_width_mm' => ['required', 'in:58,80'],
            'scale_poll_ms' => ['required', 'integer', 'min:250', 'max:10000'],
        ]);

        AppSetting::setValue('business_name', $validated['business_name']);
        AppSetting::setValue('business_address', $validated['business_address'] ?? '');
        AppSetting::setValue('business_phone', $validated['business_phone'] ?? '');
        AppSetting::setValue('ticket_footer', $validated['ticket_footer'] ?? '');
        AppSetting::setValue('printer_mode', $validated['printer_mode']);
        AppSetting::setValue('printer_paper_width_mm', $validated['printer_paper_width_mm']);
        AppSetting::setValue('scale_poll_ms', (string) $validated['scale_poll_ms']);

        return back()->with('status', 'Configuración guardada.');
    }
}
