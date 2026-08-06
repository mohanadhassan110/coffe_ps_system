<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Device;
use App\Models\Product;
use App\Models\User;
use App\Services\CafeOrderService;
use App\Services\InventoryService;
use App\Services\SessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * اختبارات سلامة المخزون والتحقق من حافة الحالات
 * Inventory & Stock Validation Tests
 */
class InventoryStockTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Device $device;
    protected Product $limitedProduct;
    protected Product $abundantProduct;
    protected InventoryService $inventoryService;
    protected SessionService $sessionService;
    protected CafeOrderService $cafeOrderService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'مدير مخزون',
            'username' => 'stockadmin',
            'password' => 'password',
            'role'     => 'admin',
        ]);

        $this->device = Device::create([
            'name'        => 'PS5 - Test',
            'type'        => 'ps5',
            'hourly_rate' => 40.00,
            'status'      => 'available',
        ]);

        $category = Category::create(['name' => 'تسالي']);

        $this->limitedProduct = Product::create([
            'category_id'     => $category->id,
            'name'            => 'شوكولاتة محدودة',
            'purchase_price'  => 10.00,
            'sale_price'      => 18.00,
            'stock_quantity'  => 3,
            'min_stock_alert' => 2,
        ]);

        $this->abundantProduct = Product::create([
            'category_id'     => $category->id,
            'name'            => 'مياه معدنية',
            'purchase_price'  => 3.00,
            'sale_price'      => 8.00,
            'stock_quantity'  => 200,
            'min_stock_alert' => 20,
        ]);

        $this->inventoryService = app(InventoryService::class);
        $this->sessionService = app(SessionService::class);
        $this->cafeOrderService = app(CafeOrderService::class);
    }

    // ═══════════════════════════════════════════
    // 1. Direct Stock Deduction Tests
    // ═══════════════════════════════════════════

    public function test_deduct_stock_decrements_quantity(): void
    {
        $this->inventoryService->deductStock($this->abundantProduct, 10);

        $this->abundantProduct->refresh();
        $this->assertEquals(190, $this->abundantProduct->stock_quantity);
    }

    public function test_restore_stock_increments_quantity(): void
    {
        $this->inventoryService->deductStock($this->abundantProduct, 10);
        $this->inventoryService->restoreStock($this->abundantProduct, 10);

        $this->abundantProduct->refresh();
        $this->assertEquals(200, $this->abundantProduct->stock_quantity);
    }

    public function test_insufficient_stock_throws_arabic_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/شوكولاتة محدودة/');

        $this->inventoryService->deductStock($this->limitedProduct, 5);
    }

    public function test_exact_stock_deduction_succeeds(): void
    {
        // Deducting exactly what's available should work
        $this->inventoryService->deductStock($this->limitedProduct, 3);

        $this->limitedProduct->refresh();
        $this->assertEquals(0, $this->limitedProduct->stock_quantity);
    }

    public function test_zero_stock_prevents_further_deduction(): void
    {
        $this->inventoryService->deductStock($this->limitedProduct, 3);

        $this->expectException(InvalidArgumentException::class);

        $this->limitedProduct->refresh();
        $this->inventoryService->deductStock($this->limitedProduct, 1);
    }

    // ═══════════════════════════════════════════
    // 2. Stock Deduction via PlayStation Session
    // ═══════════════════════════════════════════

    public function test_ps_session_item_deducts_global_stock(): void
    {
        $this->actingAs($this->admin);

        $session = $this->sessionService->startSession([
            'device_id'    => $this->device->id,
            'session_type' => 'open',
        ]);

        $this->sessionService->addItemToSession($session, $this->abundantProduct->id, 15);

        $this->abundantProduct->refresh();
        $this->assertEquals(185, $this->abundantProduct->stock_quantity);
    }

    public function test_ps_session_insufficient_stock_throws(): void
    {
        $this->actingAs($this->admin);

        $session = $this->sessionService->startSession([
            'device_id'    => $this->device->id,
            'session_type' => 'open',
        ]);

        $this->expectException(InvalidArgumentException::class);

        $this->sessionService->addItemToSession($session, $this->limitedProduct->id, 10);
    }

    // ═══════════════════════════════════════════
    // 3. Stock Deduction via Café Order
    // ═══════════════════════════════════════════

    public function test_cafe_order_item_deducts_global_stock(): void
    {
        $this->actingAs($this->admin);

        $order = $this->cafeOrderService->createOrder(['order_type' => 'table']);

        $this->cafeOrderService->addItem($order, $this->abundantProduct->id, 20);

        $this->abundantProduct->refresh();
        $this->assertEquals(180, $this->abundantProduct->stock_quantity);
    }

    public function test_cafe_order_insufficient_stock_throws(): void
    {
        $this->actingAs($this->admin);

        $order = $this->cafeOrderService->createOrder(['order_type' => 'takeaway']);

        $this->expectException(InvalidArgumentException::class);

        $this->cafeOrderService->addItem($order, $this->limitedProduct->id, 100);
    }

    // ═══════════════════════════════════════════
    // 4. Cross-Channel Stock Consistency
    // ═══════════════════════════════════════════

    public function test_stock_is_consistent_across_session_and_cafe_order(): void
    {
        $this->actingAs($this->admin);

        // Start with 3 items
        // Use 2 via PS session
        $session = $this->sessionService->startSession([
            'device_id'    => $this->device->id,
            'session_type' => 'open',
        ]);
        $this->sessionService->addItemToSession($session, $this->limitedProduct->id, 2);

        $this->limitedProduct->refresh();
        $this->assertEquals(1, $this->limitedProduct->stock_quantity);

        // Use 1 via café order — should succeed
        $order = $this->cafeOrderService->createOrder(['order_type' => 'takeaway']);
        $this->cafeOrderService->addItem($order, $this->limitedProduct->id, 1);

        $this->limitedProduct->refresh();
        $this->assertEquals(0, $this->limitedProduct->stock_quantity);

        // Trying to add more from either channel should fail
        $this->expectException(InvalidArgumentException::class);
        $this->limitedProduct->refresh();
        $this->sessionService->addItemToSession($session, $this->limitedProduct->id, 1);
    }

    // ═══════════════════════════════════════════
    // 5. Low Stock Alert Detection
    // ═══════════════════════════════════════════

    public function test_low_stock_detection(): void
    {
        // limitedProduct: stock=3, alert=2 → NOT low (3 > 2)
        $this->assertFalse($this->limitedProduct->isLowStock());

        // Deduct to bring it to alert level
        $this->inventoryService->deductStock($this->limitedProduct, 1);
        $this->limitedProduct->refresh();
        $this->assertEquals(2, $this->limitedProduct->stock_quantity);
        $this->assertTrue($this->limitedProduct->isLowStock());

        // Get low stock products via service
        $lowStock = $this->inventoryService->getLowStockProducts();
        $this->assertTrue($lowStock->contains('id', $this->limitedProduct->id));
    }
}
