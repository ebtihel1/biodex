<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddToCartTest extends TestCase
{
    use RefreshDatabase;

    private function product(array $overrides = []): Product
    {
        return Product::create(array_merge([
            'name' => 'Recycled Bottle',
            'description' => 'A bottle made from recycled plastic.',
            'price' => 10.50,
            'stock_quantity' => 5,
            'waste_category_id' => 1,
            'recycling_process_id' => 1,
            'is_available' => true,
        ], $overrides));
    }

    public function test_guest_is_redirected_to_login_when_adding_to_cart(): void
    {
        $product = $this->product();

        $this->post(route('front.products.add-to-cart', $product))
            ->assertRedirect(route('login.form'));

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_authenticated_user_can_add_product_to_cart(): void
    {
        $user = User::factory()->create(['address' => 'Tunis']);

        $this->actingAs($user)
            ->from(route('front.products.show', $product = $this->product()))
            ->post(route('front.products.add-to-cart', $product))
            ->assertRedirect(route('front.products.show', $product));

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'total_amount' => 10.50,
            'status' => 'pending',
            'shipping_address' => 'Tunis',
            'payment_method' => 'cash',
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 4,
        ]);
    }

    public function test_out_of_stock_product_cannot_be_added_to_cart(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('front.products.add-to-cart', $this->product(['stock_quantity' => 0])))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('orders', 0);
    }
}