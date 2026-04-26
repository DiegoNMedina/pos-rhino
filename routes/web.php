<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SaleController as AdminSaleController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Billing\BillingController;
use App\Http\Controllers\Billing\StripeWebhookController;
use App\Http\Controllers\Platform\DashboardController as PlatformDashboardController;
use App\Http\Controllers\Platform\PaymentController as PlatformPaymentController;
use App\Http\Controllers\Platform\StoreController as PlatformStoreController;
use App\Http\Controllers\Platform\UserController as PlatformUserController;
use App\Http\Controllers\Pos\PosApiController;
use App\Http\Controllers\Pos\PosController;
use App\Http\Controllers\Pos\ScaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Support\AdminController as SupportAdminController;
use App\Http\Controllers\Support\ChatController as SupportChatController;
use App\Models\Product;
use App\Models\Sale;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/precios', function () {
    $user = request()->user();

    return view('pricing', [
        'store' => $user ? $user->store : null,
    ]);
})->name('pricing');

Route::get('/dashboard', function () {
    $user = request()->user();
    $storeId = $user ? $user->store_id : null;

    $today = CarbonImmutable::now()->startOfDay();
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
        ->limit(8)
        ->get();

    $activeProductsCount = Product::query()
        ->where('is_active', true)
        ->when($storeId !== null, function ($builder) use ($storeId) {
            $builder->where('store_id', $storeId);
        })
        ->count();

    return view('dashboard', [
        'todaySalesCount' => $todaySalesCount,
        'todaySalesTotal' => $todaySalesTotal,
        'weekly' => $weekly,
        'activeProductsCount' => $activeProductsCount,
        'lowStockThreshold' => $lowStockThreshold,
        'lowStockProducts' => $lowStockProducts,
        'recentSales' => $recentSales,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('billing')->name('billing.')->group(function () {
        Route::get('/', [BillingController::class, 'index'])->name('index');
        Route::post('/checkout/{plan}', [BillingController::class, 'checkout'])->name('checkout');
        Route::post('/plan/{plan}', [BillingController::class, 'changePlan'])->name('plan');
        Route::post('/cancelar', [BillingController::class, 'cancel'])->name('cancel');
        Route::post('/portal', [BillingController::class, 'portal'])->name('portal');
        Route::get('/success', [BillingController::class, 'success'])->name('success');
    });

    Route::prefix('soporte')->name('support.')->group(function () {
        Route::get('/', [SupportChatController::class, 'show'])->name('chat');
        Route::get('/mensajes', [SupportChatController::class, 'messages'])->name('messages');
        Route::post('/mensajes', [SupportChatController::class, 'storeMessage'])->name('messages.store');
    });

    Route::middleware('can:manage-support')->prefix('soporte/admin')->name('support.admin.')->group(function () {
        Route::get('/', [SupportAdminController::class, 'index'])->name('index');
        Route::get('/{conversation}', [SupportAdminController::class, 'show'])->name('show');
        Route::get('/{conversation}/mensajes', [SupportAdminController::class, 'messages'])->name('messages');
        Route::post('/{conversation}/mensajes', [SupportAdminController::class, 'storeMessage'])->name('messages.store');
    });

    Route::middleware('subscribed')->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::get('/pos/sales/{sale}/ticket', [PosController::class, 'ticket'])->name('pos.ticket');
        Route::prefix('/pos/api')->group(function () {
            Route::get('/products/search', [PosApiController::class, 'searchProducts'])->name('pos.api.products.search');
            Route::post('/sales', [PosApiController::class, 'storeSale'])->name('pos.api.sales.store');
            Route::get('/scale/weight', [ScaleController::class, 'weight'])->name('pos.api.scale.weight');
        });
    });
});

Route::middleware(['auth', 'can:manage-pos', 'subscribed'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::resource('products', AdminProductController::class)->except(['show']);
    Route::resource('users', AdminUserController::class)->except(['show', 'destroy']);
    Route::get('sales', [AdminSaleController::class, 'index'])->name('sales.index');
    Route::get('sales/{sale}', [AdminSaleController::class, 'show'])->name('sales.show');
    Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('settings', [AdminSettingsController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [AdminSettingsController::class, 'update'])->name('settings.update');
});

Route::post('/stripe/webhook', StripeWebhookController::class)
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->name('stripe.webhook');

Route::middleware(['auth', 'can:manage-platform'])->prefix('plataforma')->name('platform.')->group(function () {
    Route::get('/', [PlatformDashboardController::class, 'index'])->name('dashboard');
    Route::get('/tiendas', [PlatformStoreController::class, 'index'])->name('stores.index');
    Route::get('/tiendas/{store}/editar', [PlatformStoreController::class, 'edit'])->name('stores.edit');
    Route::put('/tiendas/{store}', [PlatformStoreController::class, 'update'])->name('stores.update');
    Route::post('/tiendas/{store}/portal', [PlatformStoreController::class, 'portal'])->name('stores.portal');
    Route::get('/usuarios', [PlatformUserController::class, 'index'])->name('users.index');
    Route::post('/usuarios/soporte', [PlatformUserController::class, 'storeSupport'])->name('users.support.store');
    Route::get('/pagos', [PlatformPaymentController::class, 'index'])->name('payments.index');
});

require __DIR__.'/auth.php';
