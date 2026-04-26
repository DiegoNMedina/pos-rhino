<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Branch;
use App\Models\Register;
use App\Models\Sale;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index()
    {
        $user = request()->user();
        $storeId = $user ? $user->store_id : null;

        $branch = Branch::query()
            ->where('is_active', true)
            ->when($storeId !== null, function ($builder) use ($storeId) {
                $builder->where('store_id', $storeId);
            })
            ->orderBy('id')
            ->first();
        $register = Register::query()
            ->where('is_active', true)
            ->when($branch, function ($q) use ($branch) {
                return $q->where('branch_id', $branch->id);
            })
            ->when($storeId !== null, function ($builder) use ($storeId) {
                $builder->where('store_id', $storeId);
            })
            ->orderBy('id')
            ->first();

        return view('pos.index', [
            'branch' => $branch,
            'register' => $register,
            'scalePollMs' => (int) (AppSetting::getValue('scale_poll_ms', '1000') ?? '1000'),
            'printerMode' => AppSetting::getValue('printer_mode', 'browser'),
        ]);
    }

    public function ticket(Request $request, Sale $sale)
    {
        $user = $request->user();
        abort_unless($user && ($user->can('manage-pos') || (int) $sale->user_id === (int) $user->id), 403);
        if ($user->store_id !== null) {
            abort_unless((int) $sale->store_id === (int) $user->store_id, 404);
        }

        $sale->load(['items', 'user', 'branch', 'register']);

        $printerPaperWidthMm = AppSetting::getValue('printer_paper_width_mm', '80');
        $paperWidth = in_array($printerPaperWidthMm, ['58', '80'], true) ? $printerPaperWidthMm : '80';

        return view('pos.ticket', [
            'sale' => $sale,
            'autoprint' => (bool) $request->boolean('autoprint', false),
            'paperWidthMm' => $paperWidth,
            'businessName' => AppSetting::getValue('business_name', config('app.name')),
            'businessAddress' => AppSetting::getValue('business_address', ''),
            'businessPhone' => AppSetting::getValue('business_phone', ''),
            'ticketFooter' => AppSetting::getValue('ticket_footer', 'Gracias por su compra.'),
        ]);
    }
}
