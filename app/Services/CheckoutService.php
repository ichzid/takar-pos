<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    /**
     * @param  array<int, array{product_id:int, quantity:int}>  $items
     */
    public function checkout(
        int $userId,
        array $items,
        float $taxRate,
        float $paidAmount,
        float $discountAmount = 0.0,
        ?string $customerName = null,
        ?string $note = null,
    ): Order {
        return DB::transaction(function () use ($userId, $items, $taxRate, $paidAmount, $discountAmount, $customerName, $note) {
            $productIds = collect($items)->pluck('product_id')->all();

            $products = Product::query()
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotal = 0.0;
            $lineItems = [];

            foreach ($items as $item) {
                $product = $products->get($item['product_id']);

                if (! $product) {
                    throw ValidationException::withMessages([
                        'items' => "Produk ID {$item['product_id']} tidak ditemukan.",
                    ]);
                }

                $quantity = (int) $item['quantity'];

                if ($quantity < 1) {
                    throw ValidationException::withMessages([
                        'items' => "Jumlah untuk {$product->name} tidak valid.",
                    ]);
                }

                if ($product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => "Stok tidak cukup untuk {$product->name}.",
                    ]);
                }

                $unitPrice = (float) $product->sell_price;
                $lineTotal = $unitPrice * $quantity;
                $subtotal += $lineTotal;

                $lineItems[] = [
                    'product'    => $product,
                    'unit_price' => $unitPrice,
                    'quantity'   => $quantity,
                    'line_total' => $lineTotal,
                ];
            }

            $discountAmount = max(0.0, min($discountAmount, $subtotal));
            $taxAmount      = round(($subtotal - $discountAmount) * $taxRate, 2);
            $total          = $subtotal - $discountAmount + $taxAmount;

            if ($paidAmount < $total) {
                throw ValidationException::withMessages([
                    'paid_amount' => 'Jumlah pembayaran kurang dari total.',
                ]);
            }

            $order = Order::create([
                'order_number'    => 'ORD-' . Str::upper(Str::random(10)),
                'user_id'         => $userId,
                'customer_name'   => $customerName ?: null,
                'note'            => $note ?: null,
                'discount_amount' => $discountAmount,
                'subtotal'        => $subtotal,
                'tax_amount'      => $taxAmount,
                'total'           => $total,
                'paid_amount'     => $paidAmount,
                'change_amount'   => $paidAmount - $total,
                'status'          => 'paid',
            ]);

            foreach ($lineItems as $line) {
                /** @var \App\Models\Product $product */
                $product = $line['product'];

                OrderDetail::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'unit_price' => $line['unit_price'],
                    'quantity'   => $line['quantity'],
                    'line_total' => $line['line_total'],
                ]);

                $product->decrement('stock', $line['quantity']);
            }

            return $order;
        });
    }
}
