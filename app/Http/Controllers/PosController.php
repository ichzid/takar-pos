<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PosController extends Controller
{
    public function index()
    {
        $taxEnabled = filter_var(Setting::get('tax_enabled', '1'), FILTER_VALIDATE_BOOLEAN);
        $taxRate    = $taxEnabled ? (float) Setting::get('tax_rate', '10') / 100 : 0.0;

        return Inertia::render('Pos/Index', [
            'taxRate'    => $taxRate,
            'taxEnabled' => $taxEnabled,
            'categories' => Category::query()
                ->orderBy('name')
                ->get(['id', 'name']),
            'products' => Product::query()
                ->with('category:id,name')
                ->orderBy('name')
                ->get(['id', 'sku', 'name', 'category_id', 'sell_price', 'stock']),
        ]);
    }

    public function products(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $products = Product::query()
            ->with('category:id,name')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->limit(50)
            ->get(['id', 'sku', 'name', 'category_id', 'sell_price', 'stock']);

        return response()->json($products);
    }

    public function checkout(Request $request, CheckoutService $service)
    {
        $validated = $request->validate([
            'items'             => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'  => ['required', 'integer', 'min:1'],
            'tax_rate'          => ['nullable', 'numeric', 'min:0', 'max:1'],
            'paid_amount'       => ['required', 'numeric', 'min:0'],
            'discount_amount'   => ['nullable', 'numeric', 'min:0'],
            'customer_name'     => ['nullable', 'string', 'max:100'],
            'note'              => ['nullable', 'string', 'max:500'],
        ]);

        // Read tax from settings if not overridden
        $taxEnabled = filter_var(Setting::get('tax_enabled', '1'), FILTER_VALIDATE_BOOLEAN);
        $taxRate    = $taxEnabled ? (float) Setting::get('tax_rate', '10') / 100 : 0.0;

        if (array_key_exists('tax_rate', $validated) && $validated['tax_rate'] !== null) {
            $taxRate = (float) $validated['tax_rate'];
        }

        $order = $service->checkout(
            $request->user()->id,
            $validated['items'],
            $taxRate,
            (float) $validated['paid_amount'],
            (float) ($validated['discount_amount'] ?? 0),
            $validated['customer_name'] ?? null,
            $validated['note'] ?? null,
        );

        return redirect()
            ->route('pos.receipt', $order)
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    public function receipt(Order $order)
    {
        $order->load(['details.product', 'user']);

        return Inertia::render('Pos/Receipt', [
            'order' => $order,
        ]);
    }
}
