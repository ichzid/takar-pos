<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Coffee & Espresso'],
            ['name' => 'Signature Coffee'],
            ['name' => 'Non-Coffee'],
            ['name' => 'Tea & Refreshment'],
            ['name' => 'Mains'],
            ['name' => 'Pasta & Rice Bowl'],
            ['name' => 'Snack & Appetizer'],
            ['name' => 'Pastry'],
            ['name' => 'Dessert'],
        ];

        Category::query()->upsert($categories, ['name'], ['updated_at']);
    }
}
