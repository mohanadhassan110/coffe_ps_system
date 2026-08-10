<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Device;
use App\Models\GameSession;
use App\Models\Product;
use App\Models\User;
use App\Services\SessionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * اختبارات شاملة لإدارة جلسات البلايستيشن
 * PlayStation Session Management — Full Lifecycle Tests
 */
class PlaystationSessionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Device $device;
    protected Product $product;
    protected SessionService $sessionService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'مدير اختبار',
            'username' => 'testadmin',
            'password' => 'password',
            'role'     => 'admin',
        ]);

        $this->device = Device::create([
            'name'        => 'PS5 - 01',
            'type'        => 'ps5',
            'hourly_rate' => 40.00,
            'status'      => 'available',
        ]);

        $category = Category::create(['name' => 'مشروبات ساخنة']);

        $this->product = Product::create([
            'category_id'     => $category->id,
            'name'            => 'شاي',
            'purchase_price'  => 2.00,
            'sale_price'      => 10.00,
            'stock_quantity'  => 50,
            'min_stock_alert' => 5,
        ]);

        $this->sessionService = app(SessionService::class);
    }

    // ═══════════════════════════════════════════
    // 1. Starting Sessions
    // ═══════════════════════════════════════════

    public function test_start_open_session_changes_device_to_occupied(): void
    {
        $this->actingAs($this->admin);

        $session = $this->sessionService->startSession([
            'device_id'    => $this->device->id,
            'session_type' => 'open',
            'client_name'  => 'أحمد',
        ]);

        $this->assertNotNull($session);
        $this->assertEquals('active', $session->status);
        $this->assertEquals('open', $session->session_type);
        $this->assertEquals('أحمد', $session->client_name);

        // Device must now be occupied
        $this->device->refresh();
        $this->assertEquals('occupied', $this->device->status);
    }

    public function test_start_prepaid_session_records_minutes(): void
    {
        $this->actingAs($this->admin);

        $session = $this->sessionService->startSession([
            'device_id'        => $this->device->id,
            'session_type'     => 'pre_paid',
            'pre_paid_minutes' => 60,
            'client_name'      => 'محمد',
        ]);

        $this->assertEquals('pre_paid', $session->session_type);
        $this->assertEquals(60, $session->pre_paid_minutes);
        $this->assertEquals('active', $session->status);
    }

    public function test_cannot_start_session_on_occupied_device(): void
    {
        $this->actingAs($this->admin);

        // Start the first session — should succeed
        $this->sessionService->startSession([
            'device_id'    => $this->device->id,
            'session_type' => 'open',
        ]);

        // Attempt second session on same device — expect exception
        $this->expectException(InvalidArgumentException::class);

        $this->sessionService->startSession([
            'device_id'    => $this->device->id,
            'session_type' => 'open',
        ]);
    }

    // ═══════════════════════════════════════════
    // 2. Adding Items to Session
    // ═══════════════════════════════════════════

    public function test_add_cafe_item_to_active_session(): void
    {
        $this->actingAs($this->admin);

        $session = $this->sessionService->startSession([
            'device_id'    => $this->device->id,
            'session_type' => 'open',
        ]);

        $item = $this->sessionService->addItemToSession($session, $this->product->id, 2);

        $this->assertEquals($this->product->id, $item->product_id);
        $this->assertEquals(2, $item->quantity);
        $this->assertEquals(10.00, (float) $item->unit_price);
        $this->assertEquals(20.00, (float) $item->total_price);

        // Stock should be decremented
        $this->product->refresh();
        $this->assertEquals(48, $this->product->stock_quantity);

        // Session cafe_total should be updated
        $session->refresh();
        $this->assertEquals(20.00, (float) $session->cafe_total);
    }

    public function test_adding_same_product_merges_quantity(): void
    {
        $this->actingAs($this->admin);

        $session = $this->sessionService->startSession([
            'device_id'    => $this->device->id,
            'session_type' => 'open',
        ]);

        $this->sessionService->addItemToSession($session, $this->product->id, 1);
        $this->sessionService->addItemToSession($session, $this->product->id, 2);

        // Should have 1 item row with qty 3
        $session->load('items');
        $this->assertCount(1, $session->items);
        $this->assertEquals(3, $session->items->first()->quantity);
        $this->assertEquals(30.00, (float) $session->items->first()->total_price);
    }

    // ═══════════════════════════════════════════
    // 3. Removing Items from Session
    // ═══════════════════════════════════════════

    public function test_remove_item_restores_stock(): void
    {
        $this->actingAs($this->admin);

        $session = $this->sessionService->startSession([
            'device_id'    => $this->device->id,
            'session_type' => 'open',
        ]);

        $item = $this->sessionService->addItemToSession($session, $this->product->id, 3);

        $this->product->refresh();
        $this->assertEquals(47, $this->product->stock_quantity);

        $this->sessionService->removeItemFromSession($session, $item->id);

        $this->product->refresh();
        $this->assertEquals(50, $this->product->stock_quantity);

        $session->refresh();
        $this->assertEquals(0.00, (float) $session->cafe_total);
    }

    // ═══════════════════════════════════════════
    // 4. Closing / Checkout
    // ═══════════════════════════════════════════

    public function test_close_session_calculates_totals_and_resets_device(): void
    {
        $this->actingAs($this->admin);

        // Start 30 minutes ago so PS cost = (30/60)*40 = 20.00
        $session = $this->sessionService->startSession([
            'device_id'    => $this->device->id,
            'session_type' => 'open',
            'client_name'  => 'عميل اختبار',
        ]);

        // Manually set start_time to 30 min ago for deterministic calculation
        $session->update(['start_time' => Carbon::now()->subMinutes(30)]);

        // Add a drink
        $this->sessionService->addItemToSession($session, $this->product->id, 2);

        $closedSession = $this->sessionService->closeSession($session, [
            'discount'       => 5.00,
            'payment_method' => 'cash',
        ]);

        $this->assertEquals('closed', $closedSession->status);
        $this->assertNotNull($closedSession->end_time);
        $this->assertEquals('cash', $closedSession->payment_method);

        // PS cost: ~20.00 (30 min at 40/hr)
        $this->assertGreaterThanOrEqual(19.00, (float) $closedSession->playstation_total);
        $this->assertLessThanOrEqual(21.00, (float) $closedSession->playstation_total);

        // Café total: 2 × 10 = 20
        $this->assertEquals(20.00, (float) $closedSession->cafe_total);

        // Total = PS + cafe, Final = total - discount
        $expectedTotal = (float) $closedSession->playstation_total + 20.00;
        $this->assertEquals(round($expectedTotal, 2), (float) $closedSession->total_amount);
        $this->assertEquals(round($expectedTotal - 5.00, 2), (float) $closedSession->final_amount);

        // Device is available again
        $this->device->refresh();
        $this->assertEquals('available', $this->device->status);
    }

    public function test_close_session_with_vodafone_cash_payment(): void
    {
        $this->actingAs($this->admin);

        $session = $this->sessionService->startSession([
            'device_id'    => $this->device->id,
            'session_type' => 'open',
        ]);

        $closedSession = $this->sessionService->closeSession($session, [
            'payment_method' => 'vodafone_cash',
        ]);

        $this->assertEquals('vodafone_cash', $closedSession->payment_method);
    }

    public function test_close_session_with_card_payment(): void
    {
        $this->actingAs($this->admin);

        $session = $this->sessionService->startSession([
            'device_id'    => $this->device->id,
            'session_type' => 'open',
        ]);

        $closedSession = $this->sessionService->closeSession($session, [
            'payment_method' => 'card',
        ]);

        $this->assertEquals('card', $closedSession->payment_method);
    }

    public function test_cannot_close_already_closed_session(): void
    {
        $this->actingAs($this->admin);

        $session = $this->sessionService->startSession([
            'device_id'    => $this->device->id,
            'session_type' => 'open',
        ]);

        $this->sessionService->closeSession($session, ['payment_method' => 'cash']);

        $this->expectException(InvalidArgumentException::class);

        $session->refresh();
        $this->sessionService->closeSession($session, ['payment_method' => 'cash']);
    }

    // ═══════════════════════════════════════════
    // 5. Cancellation
    // ═══════════════════════════════════════════

    public function test_cancel_session_restores_stock_and_device(): void
    {
        $this->actingAs($this->admin);

        $session = $this->sessionService->startSession([
            'device_id'    => $this->device->id,
            'session_type' => 'open',
        ]);

        $this->sessionService->addItemToSession($session, $this->product->id, 5);

        $this->product->refresh();
        $this->assertEquals(45, $this->product->stock_quantity);

        $cancelled = $this->sessionService->cancelSession($session);

        $this->assertEquals('cancelled', $cancelled->status);

        // Stock restored
        $this->product->refresh();
        $this->assertEquals(50, $this->product->stock_quantity);

        // Device available
        $this->device->refresh();
        $this->assertEquals('available', $this->device->status);
    }

    // ═══════════════════════════════════════════
    // 6. HTTP Route Tests (Controller layer)
    // ═══════════════════════════════════════════

    public function test_http_start_session_via_post(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('sessions.store'), [
            'device_id'    => $this->device->id,
            'session_type' => 'open',
            'client_name'  => 'عميل HTTP',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('game_sessions', [
            'device_id'    => $this->device->id,
            'status'       => 'active',
            'client_name'  => 'عميل HTTP',
        ]);
    }

    public function test_http_add_item_to_session(): void
    {
        $this->actingAs($this->admin);

        $session = $this->sessionService->startSession([
            'device_id'    => $this->device->id,
            'session_type' => 'open',
        ]);

        $response = $this->post(route('sessions.addItem', $session), [
            'product_id' => $this->product->id,
            'quantity'   => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('session_items', [
            'game_session_id' => $session->id,
            'product_id'      => $this->product->id,
            'quantity'        => 1,
        ]);
    }

    public function test_http_close_session(): void
    {
        $this->actingAs($this->admin);

        $session = $this->sessionService->startSession([
            'device_id'    => $this->device->id,
            'session_type' => 'open',
        ]);

        $response = $this->post(route('sessions.close', $session), [
            'discount'       => 0,
            'payment_method' => 'cash',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('game_sessions', [
            'id'     => $session->id,
            'status' => 'closed',
        ]);
    }
}
