<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────
    //  Helper: buat user dengan role tertentu
    // ──────────────────────────────────────────────
    private function makeUser(string $role = User::ROLE_CASHIER): User
    {
        return User::factory()->create(['role' => $role]);
    }

    // ──────────────────────────────────────────────
    //  Helper: buat order sederhana
    // ──────────────────────────────────────────────
    private function makeOrder(User $user): Order
    {
        return Order::create([
            'order_number' => 'ORD-TEST-001',
            'user_id'      => $user->id,
            'subtotal'     => 10000,
            'tax_amount'   => 1000,
            'total'        => 11000,
            'paid_amount'  => 20000,
            'change_amount'=> 9000,
            'status'       => 'paid',
        ]);
    }

    // ════════════════════════════════════════════════
    //  1. Auth Guard
    // ════════════════════════════════════════════════

    /** @test */
    public function test_pos_index_requires_auth(): void
    {
        $response = $this->get('/pos');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function test_guest_cannot_access_pos_products_api(): void
    {
        // Guests are redirected to login (302) even on JSON endpoints
        // because Laravel auth middleware intercepts before the JSON check
        $response = $this->get('/pos/products');

        $response->assertRedirect('/login');
    }

    // ════════════════════════════════════════════════
    //  2. Role Access Control
    // ════════════════════════════════════════════════

    /** @test */
    public function test_pos_index_is_accessible_by_cashier(): void
    {
        $user = $this->makeUser(User::ROLE_CASHIER);

        $response = $this->actingAs($user)->get('/pos');

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page->component('Pos/Index')
        );
    }

    /** @test */
    public function test_pos_index_is_accessible_by_admin(): void
    {
        $user = $this->makeUser(User::ROLE_ADMIN);

        $response = $this->actingAs($user)->get('/pos');

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page->component('Pos/Index')
        );
    }

    /** @test */
    public function test_cashier_cannot_access_admin_only_routes(): void
    {
        // The role enum only allows 'admin' & 'cashier'.
        // We verify EnsureUserHasRole middleware by testing that a cashier
        // is blocked from admin-only routes (e.g. categories management).
        $user = $this->makeUser(User::ROLE_CASHIER);

        $response = $this->actingAs($user)->get('/categories');

        $response->assertStatus(403);
    }

    // ════════════════════════════════════════════════
    //  3. Inertia Props (tampilan halaman)
    // ════════════════════════════════════════════════

    /** @test */
    public function test_pos_index_passes_required_props(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->get('/pos');

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('Pos/Index')
                ->has('taxRate')
                ->has('products')
                ->has('categories')
        );
    }

    /** @test */
    public function test_pos_index_products_prop_contains_correct_fields(): void
    {
        $category = Category::create(['name' => 'Minuman']);
        Product::create([
            'sku'        => 'SKU-001',
            'name'       => 'Teh Manis',
            'category_id'=> $category->id,
            'sell_price' => 5000,
            'buy_price'  => 3000,
            'stock'      => 10,
        ]);

        $user = $this->makeUser();

        $response = $this->actingAs($user)->get('/pos');

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('Pos/Index')
                ->has('products', 1, fn ($prod) =>
                    $prod->where('name', 'Teh Manis')
                         ->has('id')
                         ->has('sku')
                         ->has('sell_price')
                         ->has('stock')
                         ->etc()
                )
        );
    }

    /** @test */
    public function test_pos_index_categories_prop_contains_correct_fields(): void
    {
        Category::create(['name' => 'Makanan']);
        Category::create(['name' => 'Minuman']);

        $user = $this->makeUser();

        $response = $this->actingAs($user)->get('/pos');

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('Pos/Index')
                ->has('categories', 2)
        );
    }

    // ════════════════════════════════════════════════
    //  4. Products API (pencarian)
    // ════════════════════════════════════════════════

    /** @test */
    public function test_pos_products_api_returns_json_list(): void
    {
        $category = Category::create(['name' => 'Snack']);
        Product::create([
            'sku'        => 'SKU-002',
            'name'       => 'Keripik',
            'category_id'=> $category->id,
            'sell_price' => 8000,
            'buy_price'  => 5000,
            'stock'      => 5,
        ]);

        $user = $this->makeUser();

        $response = $this->actingAs($user)->getJson('/pos/products');

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['name' => 'Keripik']);
    }

    /** @test */
    public function test_pos_products_api_filters_by_search_name(): void
    {
        $cat = Category::create(['name' => 'Snack']);
        Product::create([
            'sku' => 'A', 'name' => 'Keripik Singkong',
            'category_id' => $cat->id, 'sell_price' => 7000,
            'buy_price' => 4000, 'stock' => 10,
        ]);
        Product::create([
            'sku' => 'B', 'name' => 'Es Teh',
            'category_id' => $cat->id, 'sell_price' => 5000,
            'buy_price' => 2000, 'stock' => 20,
        ]);

        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->getJson('/pos/products?search=keripik');

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['name' => 'Keripik Singkong']);
    }

    /** @test */
    public function test_pos_products_api_filters_by_sku(): void
    {
        $cat = Category::create(['name' => 'Minuman']);
        Product::create([
            'sku' => 'KD-007', 'name' => 'Kopi Hitam',
            'category_id' => $cat->id, 'sell_price' => 6000,
            'buy_price' => 3000, 'stock' => 15,
        ]);
        Product::create([
            'sku' => 'ZZ-999', 'name' => 'Air Putih',
            'category_id' => $cat->id, 'sell_price' => 3000,
            'buy_price' => 1000, 'stock' => 30,
        ]);

        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->getJson('/pos/products?search=KD-007');

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['name' => 'Kopi Hitam']);
    }

    // ════════════════════════════════════════════════
    //  5. Receipt Page
    // ════════════════════════════════════════════════

    /** @test */
    public function test_receipt_page_is_accessible_after_checkout(): void
    {
        $user  = $this->makeUser();
        $order = $this->makeOrder($user);

        $response = $this->actingAs($user)
            ->get('/pos/receipt/' . $order->id);

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('Pos/Receipt')
                ->has('order')
                ->where('order.order_number', 'ORD-TEST-001')
        );
    }
}
