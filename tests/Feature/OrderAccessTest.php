<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderAccessTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrder(User $user, string $number): Order
    {
        return Order::create([
            'order_number' => $number,
            'user_id' => $user->id,
            'subtotal' => 10000,
            'tax_amount' => 1000,
            'total' => 11000,
            'paid_amount' => 20000,
            'change_amount' => 9000,
            'status' => 'paid',
        ]);
    }

    #[Test]
    public function cashier_can_access_orders_history_page(): void
    {
        $cashier = User::factory()->create(['role' => User::ROLE_CASHIER]);
        $this->makeOrder($cashier, 'ORD-CASHIER-001');

        $response = $this->actingAs($cashier)->get('/orders');

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('Orders/Index')
                ->where('canExport', false)
                ->has('orders.data', 1)
        );
    }

    #[Test]
    public function cashier_only_sees_their_own_orders(): void
    {
        $cashier = User::factory()->create(['role' => User::ROLE_CASHIER]);
        $otherCashier = User::factory()->create(['role' => User::ROLE_CASHIER]);

        $this->makeOrder($cashier, 'ORD-MY-001');
        $this->makeOrder($otherCashier, 'ORD-OTHER-001');

        $response = $this->actingAs($cashier)->get('/orders');

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('Orders/Index')
                ->has('orders.data', 1)
                ->where('orders.data.0.order_number', 'ORD-MY-001')
        );
    }

    #[Test]
    public function cashier_cannot_view_other_cashier_order_detail(): void
    {
        $cashier = User::factory()->create(['role' => User::ROLE_CASHIER]);
        $otherCashier = User::factory()->create(['role' => User::ROLE_CASHIER]);
        $otherOrder = $this->makeOrder($otherCashier, 'ORD-OTHER-002');

        $response = $this->actingAs($cashier)->get('/orders/'.$otherOrder->id);

        $response->assertForbidden();
    }

    #[Test]
    public function cashier_cannot_export_orders(): void
    {
        $cashier = User::factory()->create(['role' => User::ROLE_CASHIER]);

        $response = $this->actingAs($cashier)->get('/orders/export');

        $response->assertForbidden();
    }
}
