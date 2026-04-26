<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Services\Hardware\ScaleService;
use Illuminate\Http\Request;

class ScaleController extends Controller
{
    public function weight(Request $request, ScaleService $scale)
    {
        $user = $request->user();
        if (! $user || ! $user->store || ! $user->store->hasFeature('scale')) {
            return response()->json(['enabled' => false, 'weight' => null], 403);
        }

        $raw = (string) $request->query('raw', '');
        $mode = (string) config('pos.scale.mode', 'mock');
        $enabled = (bool) config('pos.scale.enabled', true);

        if (! $enabled) {
            return response()->json(['enabled' => false, 'weight' => null]);
        }

        if ($raw !== '') {
            return response()->json([
                'enabled' => true,
                'raw' => $raw,
                'weight' => $scale->parseWeightKg($raw),
            ]);
        }

        if ($mode === 'mock') {
            return response()->json([
                'enabled' => true,
                'raw' => null,
                'weight' => $scale->mockWeightKg(),
            ]);
        }

        return response()->json([
            'enabled' => true,
            'raw' => null,
            'weight' => null,
        ]);
    }
}
