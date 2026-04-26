<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $this->parseDate($request->query('from')) ?? CarbonImmutable::now()->startOfMonth();
        $to = $this->parseDate($request->query('to')) ?? CarbonImmutable::now()->endOfDay();
        $user = $request->user();
        $storeId = $user ? $user->store_id : null;

        if ($to->lt($from)) {
            [$from, $to] = [$to, $from];
        }

        $salesByDay = Sale::query()
            ->selectRaw('date(created_at) as day, count(*) as sale_count, sum(total) as total_sum')
            ->when($storeId !== null, function ($builder) use ($storeId) {
                $builder->where('store_id', $storeId);
            })
            ->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->groupBy(DB::raw('date(created_at)'))
            ->orderBy('day')
            ->get();

        $topProducts = SaleItem::query()
            ->selectRaw('product_id, name, sum(quantity) as qty_sum, sum(total) as total_sum')
            ->whereHas('sale', function ($q) use ($from, $to, $storeId) {
                if ($storeId !== null) {
                    $q->where('store_id', $storeId);
                }
                $q->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()]);
            })
            ->groupBy('product_id', 'name')
            ->orderByDesc('total_sum')
            ->limit(10)
            ->get();

        return view('admin.reports.index', [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'salesByDay' => $salesByDay,
            'topProducts' => $topProducts,
        ]);
    }

    private function parseDate($value): ?CarbonImmutable
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return CarbonImmutable::parse($value)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
