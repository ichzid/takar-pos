<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
    ]);
});

Route::get('/dashboard', function () {
    $todayRevenue = (float) Order::query()
        ->whereDate('created_at', now()->toDateString())
        ->sum('total');

    $todayOrders = Order::query()
        ->whereDate('created_at', now()->toDateString())
        ->count();

    $weeklySales = collect(range(6, 0))
        ->map(function (int $offset) {
            $date = now()->subDays($offset);

            return [
                'label' => $date->format('d M'),
                'short' => $date->format('D'),
                'value' => (float) Order::query()
                    ->whereDate('created_at', $date->toDateString())
                    ->sum('total'),
            ];
        })
        ->values();

    $topProducts = OrderDetail::query()
        ->selectRaw('product_id, SUM(quantity) as qty')
        ->with('product:id,name')
        ->groupBy('product_id')
        ->orderByDesc('qty')
        ->limit(5)
        ->get()
        ->map(fn (OrderDetail $detail) => [
            'name' => $detail->product?->name ?? 'Produk',
            'qty' => (int) $detail->qty,
        ])
        ->values();

    $recentOrders = Order::query()
        ->withCount('details')
        ->latest()
        ->limit(5)
        ->get(['order_number', 'created_at', 'total', 'status'])
        ->map(fn (Order $order) => [
            'order_number' => $order->order_number,
            'time' => optional($order->created_at)->format('H:i') ?? '-',
            'items_count' => (int) $order->details_count,
            'total' => (float) $order->total,
            'status' => $order->status,
        ])
        ->values();

    return Inertia::render('Dashboard', [
        'stats' => [
            'today_revenue' => $todayRevenue,
            'today_orders' => $todayOrders,
            'total_products' => Product::query()->count(),
            'total_categories' => Category::query()->count(),
        ],
        'weeklySales' => $weeklySales,
        'topProducts' => $topProducts,
        'recentOrders' => $recentOrders,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::get('/orders/export', [OrderController::class, 'export'])->name('orders.export');
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('products', ProductController::class)->except(['show']);
        Route::resource('users', UserController::class)->except(['show']);
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

    Route::middleware('role:admin,cashier')->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::get('/pos/products', [PosController::class, 'products'])->name('pos.products');
        Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
        Route::get('/pos/receipt/{order}', [PosController::class, 'receipt'])->name('pos.receipt');
        Route::resource('orders', OrderController::class)->only(['index', 'show']);
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
