<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Carbon\CarbonImmutable;

class AdminController extends Controller
{
    public function index()
    {
        $user = request()->user();
        $storeId = $user ? $user->store_id : null;

        $today = CarbonImmutable::now()->startOfDay();
        $monthStart = CarbonImmutable::now()->startOfMonth();
        $last7Start = $today->subDays(6);
        $last7End = $today->endOfDay();

        $weeklyRows = Sale::query()
            ->selectRaw('date(created_at) as day, count(*) as sale_count, sum(total) as total_sum')
            ->where('status', Sale::STATUS_COMPLETED)
            ->when($storeId !== null, function ($builder) use ($storeId) {
                $builder->where('store_id', $storeId);
            })
            ->whereBetween('created_at', [$last7Start, $last7End])
            ->groupByRaw('date(created_at)')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $weekly = collect(range(0, 6))->map(function (int $offset) use ($last7Start, $weeklyRows) {
            $day = $last7Start->addDays($offset)->toDateString();
            $row = $weeklyRows->get($day);

            return [
                'day' => $day,
                'sale_count' => (int) ($row->sale_count ?? 0),
                'total_sum' => (float) ($row->total_sum ?? 0),
            ];
        });

        $todaySalesCount = (int) Sale::query()
            ->where('status', Sale::STATUS_COMPLETED)
            ->when($storeId !== null, function ($builder) use ($storeId) {
                $builder->where('store_id', $storeId);
            })
            ->whereBetween('created_at', [$today, $today->endOfDay()])
            ->count();

        $todaySalesTotal = (float) Sale::query()
            ->where('status', Sale::STATUS_COMPLETED)
            ->when($storeId !== null, function ($builder) use ($storeId) {
                $builder->where('store_id', $storeId);
            })
            ->whereBetween('created_at', [$today, $today->endOfDay()])
            ->sum('total');

        $monthSalesCount = (int) Sale::query()
            ->where('status', Sale::STATUS_COMPLETED)
            ->when($storeId !== null, function ($builder) use ($storeId) {
                $builder->where('store_id', $storeId);
            })
            ->whereBetween('created_at', [$monthStart, $today->endOfDay()])
            ->count();

        $monthSalesTotal = (float) Sale::query()
            ->where('status', Sale::STATUS_COMPLETED)
            ->when($storeId !== null, function ($builder) use ($storeId) {
                $builder->where('store_id', $storeId);
            })
            ->whereBetween('created_at', [$monthStart, $today->endOfDay()])
            ->sum('total');

        $lowStockThreshold = 5;
        $lowStockProducts = Product::query()
            ->whereNotNull('stock')
            ->when($storeId !== null, function ($builder) use ($storeId) {
                $builder->where('store_id', $storeId);
            })
            ->where('stock', '<=', $lowStockThreshold)
            ->orderBy('stock')
            ->limit(8)
            ->get();

        $recentSales = Sale::query()
            ->with(['user', 'branch', 'register'])
            ->where('status', Sale::STATUS_COMPLETED)
            ->when($storeId !== null, function ($builder) use ($storeId) {
                $builder->where('store_id', $storeId);
            })
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $topProducts = SaleItem::query()
            ->selectRaw('name, sum(quantity) as qty_sum, sum(total) as total_sum')
            ->whereHas('sale', function ($q) use ($last7Start, $last7End, $storeId) {
                if ($storeId !== null) {
                    $q->where('store_id', $storeId);
                }
                $q->where('status', Sale::STATUS_COMPLETED)->whereBetween('created_at', [$last7Start, $last7End]);
            })
            ->groupBy('name')
            ->orderByDesc('total_sum')
            ->limit(6)
            ->get();

        return view('admin.dashboard', [
            'productCount' => Product::query()->when($storeId !== null, fn ($b) => $b->where('store_id', $storeId))->count(),
            'activeProductCount' => Product::query()->where('is_active', true)->when($storeId !== null, fn ($b) => $b->where('store_id', $storeId))->count(),
            'userCount' => User::query()->when($storeId !== null, fn ($b) => $b->where('store_id', $storeId))->count(),
            'saleCount' => Sale::query()->when($storeId !== null, fn ($b) => $b->where('store_id', $storeId))->count(),
            'todaySalesCount' => $todaySalesCount,
            'todaySalesTotal' => $todaySalesTotal,
            'monthSalesCount' => $monthSalesCount,
            'monthSalesTotal' => $monthSalesTotal,
            'weekly' => $weekly,
            'lowStockThreshold' => $lowStockThreshold,
            'lowStockProducts' => $lowStockProducts,
            'recentSales' => $recentSales,
            'topProducts' => $topProducts,
        ]);
    }
}
