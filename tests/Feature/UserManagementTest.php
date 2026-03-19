<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_access_users_index_page(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get('/users');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('Users/Index'));
    }

    #[Test]
    public function cashier_cannot_access_users_management_page(): void
    {
        $cashier = User::factory()->create(['role' => User::ROLE_CASHIER]);

        $response = $this->actingAs($cashier)->get('/users');

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_can_create_a_cashier_user(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'Kasir Baru',
            'email' => 'kasir.baru@example.com',
            'role' => User::ROLE_CASHIER,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'name' => 'Kasir Baru',
            'email' => 'kasir.baru@example.com',
            'role' => User::ROLE_CASHIER,
        ]);
    }

    #[Test]
    public function admin_cannot_delete_their_own_account_from_users_menu(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->from('/users')
            ->delete('/users/' . $admin->id);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    #[Test]
    public function admin_cannot_delete_user_that_has_orders(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $cashier = User::factory()->create(['role' => User::ROLE_CASHIER]);

        Order::create([
            'order_number' => 'ORD-USER-001',
            'user_id' => $cashier->id,
            'subtotal' => 10000,
            'tax_amount' => 1000,
            'total' => 11000,
            'paid_amount' => 20000,
            'change_amount' => 9000,
            'status' => 'paid',
        ]);

        $response = $this->actingAs($admin)
            ->from('/users')
            ->delete('/users/' . $cashier->id);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', ['id' => $cashier->id]);
    }
}
