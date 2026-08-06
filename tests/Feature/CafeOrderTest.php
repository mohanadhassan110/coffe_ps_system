<?php

namespace Tests\Feature;

use App\Models\CafeOrder;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\CafeOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * اختبارات شاملة لطلبات الكافيه المستقلة (طاولات وتيك أواي)
 * Café Tables & Takeaway Orders — Full Lifecycle Tests
 */
class CafeOrderTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Product $espresso;
    protected Product $water;
    protected CafeOrderService $cafeOrderService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'كاشير اختبار',
            'username' => 'testcashier',
            'password' => 'password',
            'role'     => 'admin',
        ]);

        $hotDrinks = Category::create(['name' => 'مشروبات ساخنة']);
        $coldDrinks = Category::create(['name' => 'مشروبات باردة']);

        $this->espresso = Product::create([
            'category_id'     => $hotDrinks->id,
            'name'            => 'كابتشينو',
            'purchase_price'  => 5.00,
            'sale_price'      => 20.00,
            'stock_quantity'  => 30,
            'min_stock_alert' => 5,
        ]);

        $this->water = Product::create([
            'category_id'     => $coldDrinks->id,
            'name'            => 'مياه معدنية',
            'purchase_price'  => 3.00,
            'sale_price'      => 8.00,
            'stock_quantity'  => 100,
            'min_stock_alert' => 10,
        ]);

        $this->cafeOrderService = app(CafeOrderService::class);
    }

    // ═══════════════════════════════════════════
    // 1. Creating Table & Takeaway Orders
    // ═══════════════════════════════════════════

    public function test_create_table_order(): void
    {
        $this->actingAs($this->admin);

        $order = $this->cafeOrderService->createOrder([
            'order_type'   => 'table',
            'table_number' => 'طاولة 1',
            'client_name'  => 'عميل طاولة',
        ]);

        $this->assertNotNull($order);
        $this->assertEquals('open', $order->status);
        $this->assertEquals('table', $order->order_type);
        $this->assertEquals('طاولة 1', $order->table_number);
        $this->assertEquals('عميل طاولة', $order->client_name);
        $this->assertEquals(0, (float) $order->total_amount);
    }

    public function test_create_takeaway_order(): void
    {
        $this->actingAs($this->admin);

        $order = $this->cafeOrderService->createOrder([
            'order_type'  => 'takeaway',
            'client_name' => 'عميل تيك أواي',
        ]);

        $this->assertEquals('takeaway', $order->order_type);
        $this->assertNull($order->table_number);
        $this->assertEquals('open', $order->status);
    }

    // ═══════════════════════════════════════════
    // 2. Adding Items
    // ═══════════════════════════════════════════

    public function test_add_items_to_cafe_order(): void
    {
        $this->actingAs($this->admin);

        $order = $this->cafeOrderService->createOrder([
            'order_type'   => 'table',
            'table_number' => 'طاولة 3',
        ]);

        // Add 2 espressos
        $item1 = $this->cafeOrderService->addItem($order, $this->espresso->id, 2);
        $this->assertEquals(2, $item1->quantity);
        $this->assertEquals(40.00, (float) $item1->total_price);

        // Add 1 water
        $item2 = $this->cafeOrderService->addItem($order, $this->water->id, 1);
        $this->assertEquals(1, $item2->quantity);
        $this->assertEquals(8.00, (float) $item2->total_price);

        // Verify order total updated: 40 + 8 = 48
        $order->refresh();
        $this->assertEquals(48.00, (float) $order->total_amount);

        // Stock should be decremented
        $this->espresso->refresh();
        $this->assertEquals(28, $this->espresso->stock_quantity);

        $this->water->refresh();
        $this->assertEquals(99, $this->water->stock_quantity);
    }

    public function test_add_same_product_merges_in_cafe_order(): void
    {
        $this->actingAs($this->admin);

        $order = $this->cafeOrderService->createOrder(['order_type' => 'table']);

        $this->cafeOrderService->addItem($order, $this->espresso->id, 1);
        $this->cafeOrderService->addItem($order, $this->espresso->id, 3);

        $order->load('items');
        $this->assertCount(1, $order->items);
        $this->assertEquals(4, $order->items->first()->quantity);
        $this->assertEquals(80.00, (float) $order->items->first()->total_price);
    }

    // ═══════════════════════════════════════════
    // 3. Removing Items
    // ═══════════════════════════════════════════

    public function test_remove_item_from_cafe_order_restores_stock(): void
    {
        $this->actingAs($this->admin);

        $order = $this->cafeOrderService->createOrder(['order_type' => 'table']);

        $item = $this->cafeOrderService->addItem($order, $this->espresso->id, 3);

        $this->espresso->refresh();
        $this->assertEquals(27, $this->espresso->stock_quantity);

        $this->cafeOrderService->removeItem($order, $item->id);

        $this->espresso->refresh();
        $this->assertEquals(30, $this->espresso->stock_quantity);

        $order->refresh();
        $this->assertEquals(0.00, (float) $order->total_amount);
    }

    // ═══════════════════════════════════════════
    // 4. Checkout / Close
    // ═══════════════════════════════════════════

    public function test_checkout_cafe_order_with_cash(): void
    {
        $this->actingAs($this->admin);

        $order = $this->cafeOrderService->createOrder([
            'order_type'   => 'table',
            'table_number' => 'طاولة 5',
        ]);

        $this->cafeOrderService->addItem($order, $this->espresso->id, 2);  // 40
        $this->cafeOrderService->addItem($order, $this->water->id, 3);     // 24

        $completed = $this->cafeOrderService->checkout($order, [
            'discount'       => 4.00,
            'payment_method' => 'cash',
        ]);

        $this->assertEquals('completed', $completed->status);
        $this->assertEquals(64.00, (float) $completed->total_amount);
        $this->assertEquals(4.00, (float) $completed->discount);
        $this->assertEquals(60.00, (float) $completed->final_amount);
        $this->assertEquals('cash', $completed->payment_method);
    }

    public function test_checkout_takeaway_with_vodafone_cash(): void
    {
        $this->actingAs($this->admin);

        $order = $this->cafeOrderService->createOrder(['order_type' => 'takeaway']);
        $this->cafeOrderService->addItem($order, $this->water->id, 2);  // 16

        $completed = $this->cafeOrderService->checkout($order, [
            'payment_method' => 'vodafone_cash',
        ]);

        $this->assertEquals('completed', $completed->status);
        $this->assertEquals('vodafone_cash', $completed->payment_method);
        $this->assertEquals(16.00, (float) $completed->final_amount);
    }

    public function test_cannot_checkout_already_completed_order(): void
    {
        $this->actingAs($this->admin);

        $order = $this->cafeOrderService->createOrder(['order_type' => 'table']);
        $this->cafeOrderService->addItem($order, $this->water->id, 1);

        $this->cafeOrderService->checkout($order, ['payment_method' => 'cash']);

        $this->expectException(InvalidArgumentException::class);

        $order->refresh();
        $this->cafeOrderService->checkout($order, ['payment_method' => 'cash']);
    }

    // ═══════════════════════════════════════════
    // 5. Cancellation
    // ═══════════════════════════════════════════

    public function test_cancel_cafe_order_restores_all_stock(): void
    {
        $this->actingAs($this->admin);

        $order = $this->cafeOrderService->createOrder(['order_type' => 'takeaway']);

        $this->cafeOrderService->addItem($order, $this->espresso->id, 4);  // -4
        $this->cafeOrderService->addItem($order, $this->water->id, 6);     // -6

        $this->espresso->refresh();
        $this->assertEquals(26, $this->espresso->stock_quantity);
        $this->water->refresh();
        $this->assertEquals(94, $this->water->stock_quantity);

        $cancelled = $this->cafeOrderService->cancelOrder($order);

        $this->assertEquals('cancelled', $cancelled->status);

        // All stock restored
        $this->espresso->refresh();
        $this->assertEquals(30, $this->espresso->stock_quantity);
        $this->water->refresh();
        $this->assertEquals(100, $this->water->stock_quantity);
    }

    // ═══════════════════════════════════════════
    // 6. HTTP Route Tests
    // ═══════════════════════════════════════════

    public function test_http_create_table_order(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('cafe-orders.store'), [
            'order_type'   => 'table',
            'table_number' => 'طاولة 7',
            'client_name'  => 'عميل HTTP',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cafe_orders', [
            'order_type'   => 'table',
            'table_number' => 'طاولة 7',
            'status'       => 'open',
        ]);
    }

    public function test_http_create_takeaway_order(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('cafe-orders.store'), [
            'order_type' => 'takeaway',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cafe_orders', [
            'order_type' => 'takeaway',
            'status'     => 'open',
        ]);
    }

    public function test_http_add_item_to_cafe_order(): void
    {
        $this->actingAs($this->admin);

        $order = $this->cafeOrderService->createOrder(['order_type' => 'table']);

        $response = $this->post(route('cafe-orders.addItem', $order), [
            'product_id' => $this->espresso->id,
            'quantity'   => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cafe_order_items', [
            'cafe_order_id' => $order->id,
            'product_id'    => $this->espresso->id,
            'quantity'      => 1,
        ]);
    }

    public function test_http_close_cafe_order(): void
    {
        $this->actingAs($this->admin);

        $order = $this->cafeOrderService->createOrder(['order_type' => 'table']);
        $this->cafeOrderService->addItem($order, $this->espresso->id, 1);

        $response = $this->post(route('cafe-orders.close', $order), [
            'discount'       => 0,
            'payment_method' => 'cash',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cafe_orders', [
            'id'     => $order->id,
            'status' => 'completed',
        ]);
    }
}
