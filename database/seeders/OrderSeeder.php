<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $cashiers = User::query()
            ->where('role', User::ROLE_CASHIER)
            ->get(['id']);

        $products = Product::query()
            ->with('category:id,name')
            ->get(['id', 'name', 'category_id', 'sell_price']);

        if ($cashiers->isEmpty() || $products->isEmpty()) {
            return;
        }

        OrderDetail::query()->delete();
        Order::query()->delete();

        $productByCategory = $products
            ->groupBy(fn (Product $product) => $product->category?->name ?? 'Uncategorized');

        $weightedCategories = $this->buildWeightedCategoryPool();
        $faker = fake('id_ID');

        for ($counter = 1; $counter <= 240; $counter++) {
            $createdAt = $this->generateServiceTime($faker);
            $itemCount = $createdAt->format('H') >= 17
                ? $faker->numberBetween(2, 6)
                : $faker->numberBetween(1, 4);

            $picked = collect();

            while ($picked->count() < $itemCount) {
                $categoryName = $faker->randomElement($weightedCategories);
                $pool = $productByCategory->get($categoryName, collect());

                if ($pool->isEmpty()) {
                    $pool = $products;
                }

                $candidate = $pool->random();

                if ($picked->contains(fn (array $row) => $row['product']->id === $candidate->id)) {
                    continue;
                }

                $qty = str_contains($categoryName, 'Coffee')
                    || str_contains($categoryName, 'Tea')
                    || $categoryName === 'Non-Coffee'
                    ? $faker->numberBetween(1, 2)
                    : $faker->numberBetween(1, 3);

                $picked->push([
                    'product' => $candidate,
                    'quantity' => $qty,
                ]);
            }

            $subtotal = $picked->sum(function (array $item) {
                return ((float) $item['product']->sell_price) * $item['quantity'];
            });

            $tax = round($subtotal * 0.10, 2);
            $total = $subtotal + $tax;
            $paid = $this->calculatePaidAmount($total, $faker);

            $order = new Order([
                'order_number' => sprintf('RC-%s-%04d', $createdAt->format('ymd'), $counter),
                'user_id' => $cashiers->random()->id,
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'total' => $total,
                'paid_amount' => $paid,
                'change_amount' => $paid - $total,
                'status' => 'paid',
            ]);
            $order->created_at = $createdAt;
            $order->updated_at = $createdAt;
            $order->save();

            $detailRows = $picked
                ->map(function (array $item) use ($order, $createdAt) {
                    $unitPrice = (float) $item['product']->sell_price;
                    $qty = $item['quantity'];

                    return [
                        'order_id' => $order->id,
                        'product_id' => $item['product']->id,
                        'unit_price' => $unitPrice,
                        'quantity' => $qty,
                        'line_total' => $unitPrice * $qty,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];
                })
                ->all();

            OrderDetail::query()->insert($detailRows);
        }
    }

    /**
     * @return array<int, string>
     */
    private function buildWeightedCategoryPool(): array
    {
        $weights = [
            'Coffee & Espresso' => 28,
            'Signature Coffee' => 24,
            'Non-Coffee' => 14,
            'Tea & Refreshment' => 12,
            'Mains' => 7,
            'Pasta & Rice Bowl' => 6,
            'Snack & Appetizer' => 5,
            'Pastry' => 3,
            'Dessert' => 1,
        ];

        $pool = [];

        foreach ($weights as $category => $weight) {
            for ($i = 0; $i < $weight; $i++) {
                $pool[] = $category;
            }
        }

        return $pool;
    }

    private function generateServiceTime(\Faker\Generator $faker): \DateTime
    {
        $date = $faker->dateTimeBetween('-45 days', 'now');
        $hour = $faker->numberBetween(8, 22);
        $minute = $faker->randomElement([0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55]);
        $second = $faker->numberBetween(0, 59);

        $date->setTime($hour, $minute, $second);

        return $date;
    }

    private function calculatePaidAmount(float $total, \Faker\Generator $faker): float
    {
        $cashSteps = [0, 1000, 2000, 5000, 10000, 20000];
        $extra = $faker->randomElement($cashSteps);

        return $total + $extra;
    }
}
