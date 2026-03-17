<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categoryMap = Category::query()->pluck('id', 'name');

        $products = [
            ['sku' => 'COF-001', 'name' => 'Espresso Single', 'category' => 'Coffee & Espresso', 'sell_price' => 18000, 'buy_price' => 7000, 'stock' => 120],
            ['sku' => 'COF-002', 'name' => 'Espresso Doppio', 'category' => 'Coffee & Espresso', 'sell_price' => 22000, 'buy_price' => 9000, 'stock' => 110],
            ['sku' => 'COF-003', 'name' => 'Americano', 'category' => 'Coffee & Espresso', 'sell_price' => 24000, 'buy_price' => 9500, 'stock' => 105],
            ['sku' => 'COF-004', 'name' => 'Cappuccino', 'category' => 'Coffee & Espresso', 'sell_price' => 29000, 'buy_price' => 12000, 'stock' => 98],
            ['sku' => 'COF-005', 'name' => 'Cafe Latte', 'category' => 'Coffee & Espresso', 'sell_price' => 30000, 'buy_price' => 12500, 'stock' => 96],
            ['sku' => 'COF-006', 'name' => 'Flat White', 'category' => 'Coffee & Espresso', 'sell_price' => 30000, 'buy_price' => 12500, 'stock' => 88],

            ['sku' => 'SIG-001', 'name' => 'Aren Latte', 'category' => 'Signature Coffee', 'sell_price' => 34000, 'buy_price' => 14500, 'stock' => 94],
            ['sku' => 'SIG-002', 'name' => 'Caramel Macchiato', 'category' => 'Signature Coffee', 'sell_price' => 36000, 'buy_price' => 15500, 'stock' => 85],
            ['sku' => 'SIG-003', 'name' => 'Mocha Latte', 'category' => 'Signature Coffee', 'sell_price' => 35000, 'buy_price' => 15000, 'stock' => 82],
            ['sku' => 'SIG-004', 'name' => 'Pandan Latte', 'category' => 'Signature Coffee', 'sell_price' => 35000, 'buy_price' => 14800, 'stock' => 78],
            ['sku' => 'SIG-005', 'name' => 'Sea Salt Latte', 'category' => 'Signature Coffee', 'sell_price' => 37000, 'buy_price' => 16200, 'stock' => 72],

            ['sku' => 'NCO-001', 'name' => 'Chocolate Classic', 'category' => 'Non-Coffee', 'sell_price' => 32000, 'buy_price' => 14000, 'stock' => 84],
            ['sku' => 'NCO-002', 'name' => 'Matcha Latte', 'category' => 'Non-Coffee', 'sell_price' => 34000, 'buy_price' => 15500, 'stock' => 76],
            ['sku' => 'NCO-003', 'name' => 'Red Velvet Latte', 'category' => 'Non-Coffee', 'sell_price' => 33000, 'buy_price' => 14500, 'stock' => 68],
            ['sku' => 'NCO-004', 'name' => 'Taro Latte', 'category' => 'Non-Coffee', 'sell_price' => 33000, 'buy_price' => 14200, 'stock' => 66],
            ['sku' => 'NCO-005', 'name' => 'Hazelnut Milk', 'category' => 'Non-Coffee', 'sell_price' => 35000, 'buy_price' => 16000, 'stock' => 61],

            ['sku' => 'TEA-001', 'name' => 'Lemon Tea', 'category' => 'Tea & Refreshment', 'sell_price' => 23000, 'buy_price' => 8500, 'stock' => 103],
            ['sku' => 'TEA-002', 'name' => 'Peach Tea', 'category' => 'Tea & Refreshment', 'sell_price' => 25000, 'buy_price' => 9000, 'stock' => 99],
            ['sku' => 'TEA-003', 'name' => 'Lychee Tea', 'category' => 'Tea & Refreshment', 'sell_price' => 26000, 'buy_price' => 9400, 'stock' => 96],
            ['sku' => 'TEA-004', 'name' => 'Passion Fruit Soda', 'category' => 'Tea & Refreshment', 'sell_price' => 28000, 'buy_price' => 11200, 'stock' => 87],
            ['sku' => 'TEA-005', 'name' => 'Sparkling Yakult', 'category' => 'Tea & Refreshment', 'sell_price' => 30000, 'buy_price' => 13000, 'stock' => 79],
            ['sku' => 'TEA-006', 'name' => 'Mineral Water', 'category' => 'Tea & Refreshment', 'sell_price' => 10000, 'buy_price' => 3500, 'stock' => 150],

            ['sku' => 'MNS-001', 'name' => 'Chicken Katsu Curry', 'category' => 'Mains', 'sell_price' => 52000, 'buy_price' => 29000, 'stock' => 48],
            ['sku' => 'MNS-002', 'name' => 'Grilled Chicken Steak', 'category' => 'Mains', 'sell_price' => 58000, 'buy_price' => 34000, 'stock' => 42],
            ['sku' => 'MNS-003', 'name' => 'Dory Sambal Matah', 'category' => 'Mains', 'sell_price' => 56000, 'buy_price' => 33000, 'stock' => 37],
            ['sku' => 'MNS-004', 'name' => 'Beef Teriyaki Plate', 'category' => 'Mains', 'sell_price' => 65000, 'buy_price' => 39000, 'stock' => 30],

            ['sku' => 'PRB-001', 'name' => 'Spaghetti Aglio Olio', 'category' => 'Pasta & Rice Bowl', 'sell_price' => 44000, 'buy_price' => 23000, 'stock' => 52],
            ['sku' => 'PRB-002', 'name' => 'Spaghetti Carbonara', 'category' => 'Pasta & Rice Bowl', 'sell_price' => 48000, 'buy_price' => 27000, 'stock' => 47],
            ['sku' => 'PRB-003', 'name' => 'Spaghetti Bolognese', 'category' => 'Pasta & Rice Bowl', 'sell_price' => 47000, 'buy_price' => 26000, 'stock' => 44],
            ['sku' => 'PRB-004', 'name' => 'Beef Blackpepper Rice', 'category' => 'Pasta & Rice Bowl', 'sell_price' => 49000, 'buy_price' => 28000, 'stock' => 40],
            ['sku' => 'PRB-005', 'name' => 'Chicken Salted Egg Rice', 'category' => 'Pasta & Rice Bowl', 'sell_price' => 46000, 'buy_price' => 25000, 'stock' => 38],

            ['sku' => 'SNA-001', 'name' => 'Truffle Fries', 'category' => 'Snack & Appetizer', 'sell_price' => 32000, 'buy_price' => 15000, 'stock' => 73],
            ['sku' => 'SNA-002', 'name' => 'Chicken Wings BBQ', 'category' => 'Snack & Appetizer', 'sell_price' => 39000, 'buy_price' => 21000, 'stock' => 65],
            ['sku' => 'SNA-003', 'name' => 'Nachos Beef', 'category' => 'Snack & Appetizer', 'sell_price' => 36000, 'buy_price' => 18000, 'stock' => 59],
            ['sku' => 'SNA-004', 'name' => 'Onion Rings', 'category' => 'Snack & Appetizer', 'sell_price' => 30000, 'buy_price' => 14500, 'stock' => 62],
            ['sku' => 'SNA-005', 'name' => 'Crispy Tofu Bites', 'category' => 'Snack & Appetizer', 'sell_price' => 28000, 'buy_price' => 13000, 'stock' => 66],

            ['sku' => 'PAS-001', 'name' => 'Butter Croissant', 'category' => 'Pastry', 'sell_price' => 24000, 'buy_price' => 9500, 'stock' => 70],
            ['sku' => 'PAS-002', 'name' => 'Pain Au Chocolat', 'category' => 'Pastry', 'sell_price' => 27000, 'buy_price' => 12000, 'stock' => 58],
            ['sku' => 'PAS-003', 'name' => 'Banana Bread', 'category' => 'Pastry', 'sell_price' => 26000, 'buy_price' => 11000, 'stock' => 55],
            ['sku' => 'PAS-004', 'name' => 'Cheese Danish', 'category' => 'Pastry', 'sell_price' => 28000, 'buy_price' => 12500, 'stock' => 49],

            ['sku' => 'DSR-001', 'name' => 'Classic Tiramisu', 'category' => 'Dessert', 'sell_price' => 36000, 'buy_price' => 18000, 'stock' => 38],
            ['sku' => 'DSR-002', 'name' => 'Panna Cotta Berry', 'category' => 'Dessert', 'sell_price' => 34000, 'buy_price' => 16500, 'stock' => 36],
            ['sku' => 'DSR-003', 'name' => 'Fudgy Brownies', 'category' => 'Dessert', 'sell_price' => 30000, 'buy_price' => 14000, 'stock' => 42],
            ['sku' => 'DSR-004', 'name' => 'Burnt Cheesecake', 'category' => 'Dessert', 'sell_price' => 38000, 'buy_price' => 19000, 'stock' => 34],
        ];

        $now = now();

        $payload = collect($products)
            ->map(function (array $product) use ($categoryMap, $now) {
                return [
                    'sku' => $product['sku'],
                    'name' => $product['name'],
                    'category_id' => $categoryMap[$product['category']] ?? null,
                    'sell_price' => $product['sell_price'],
                    'buy_price' => $product['buy_price'],
                    'stock' => $product['stock'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })
            ->all();

        Product::query()->upsert(
            $payload,
            ['sku'],
            ['name', 'category_id', 'sell_price', 'buy_price', 'stock', 'updated_at'],
        );
    }
}
