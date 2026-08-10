<?php

namespace Tests\Feature;

use App\Models\CafeOrder;
use App\Models\Category;
use App\Models\Device;
use App\Models\Expense;
use App\Models\GameSession;
use App\Models\Product;
use App\Models\User;
use App\Services\CafeOrderService;
use App\Services\ReportService;
use App\Services\SessionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * اختبارات سلامة التقارير والبيانات المالية
 * Financial & Reports Integrity Tests
 */
class ReportsIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Device $device1;
    protected Device $device2;
    protected Product $drink;
    protected Product $snack;
    protected SessionService $sessionService;
    protected CafeOrderService $cafeOrderService;
    protected ReportService $reportService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'مدير تقارير',
            'username' => 'reportadmin',
            'password' => 'password',
            'role'     => 'admin',
        ]);

        $this->device1 = Device::create([
            'name'        => 'PS5 - A',
            'type'        => 'ps5',
            'hourly_rate' => 40.00,
            'status'      => 'available',
        ]);

        $this->device2 = Device::create([
            'name'        => 'PS4 - B',
            'type'        => 'ps4',
            'hourly_rate' => 25.00,
            'status'      => 'available',
        ]);

        $category = Category::create(['name' => 'مشروبات']);
        $snackCat = Category::create(['name' => 'تسالي']);

        $this->drink = Product::create([
            'category_id'     => $category->id,
            'name'            => 'نسكافيه',
            'purchase_price'  => 4.00,
            'sale_price'      => 15.00,
            'stock_quantity'  => 200,
            'min_stock_alert' => 10,
        ]);

        $this->snack = Product::create([
            'category_id'     => $snackCat->id,
            'name'            => 'شيبسي',
            'purchase_price'  => 4.00,
            'sale_price'      => 8.00,
            'stock_quantity'  => 100,
            'min_stock_alert' => 10,
        ]);

        $this->sessionService = app(SessionService::class);
        $this->cafeOrderService = app(CafeOrderService::class);
        $this->reportService = app(ReportService::class);
    }

    /**
     * Helper: Create and close a PS session with items
     */
    private function createClosedSession(
        Device $device,
        int $minutesAgo,
        array $items,
        string $paymentMethod = 'cash',
        float $discount = 0
    ): GameSession {
        $this->actingAs($this->admin);

        $session = $this->sessionService->startSession([
            'device_id'    => $device->id,
            'session_type' => 'open',
        ]);

        $session->update(['start_time' => Carbon::now()->subMinutes($minutesAgo)]);

        foreach ($items as [$productId, $qty]) {
            $this->sessionService->addItemToSession($session, $productId, $qty);
        }

        return $this->sessionService->closeSession($session, [
            'payment_method' => $paymentMethod,
            'discount'       => $discount,
        ]);
    }

    /**
     * Helper: Create and complete a café order with items
     */
    private function createCompletedCafeOrder(
        string $orderType,
        array $items,
        string $paymentMethod = 'cash',
        float $discount = 0
    ): CafeOrder {
        $this->actingAs($this->admin);

        $order = $this->cafeOrderService->createOrder([
            'order_type' => $orderType,
        ]);

        foreach ($items as [$productId, $qty]) {
            $this->cafeOrderService->addItem($order, $productId, $qty);
        }

        return $this->cafeOrderService->checkout($order, [
            'payment_method' => $paymentMethod,
            'discount'       => $discount,
        ]);
    }

    // ═══════════════════════════════════════════
    // 1. Revenue Separation Tests
    // ═══════════════════════════════════════════

    public function test_daily_report_separates_ps_and_cafe_revenue(): void
    {
        // Session 1: PS5, 60 min, with drinks paid by cash
        $session1 = $this->createClosedSession(
            $this->device1,
            60,
            [[$this->drink->id, 2]],  // 2 × 15 = 30 café
            'cash'
        );

        // Session 2: PS4, 30 min, no items, paid by card
        $session2 = $this->createClosedSession(
            $this->device2,
            30,
            [],
            'card'
        );

        // Independent café order
        $cafeOrder = $this->createCompletedCafeOrder(
            'takeaway',
            [[$this->snack->id, 3]],  // 3 × 8 = 24
            'vodafone_cash'
        );

        $report = $this->reportService->getDailyReport(Carbon::today()->format('Y-m-d'));

        // PS revenue should include both sessions' playstation_total
        $expectedPsRevenue = (float) $session1->playstation_total + (float) $session2->playstation_total;
        $this->assertEquals(round($expectedPsRevenue, 2), $report['ps_revenue']);

        // Café revenue includes session café items (30) + independent order (24)
        $expectedCafeRevenue = 30.00 + 24.00;
        $this->assertEquals($expectedCafeRevenue, $report['cafe_revenue']);

        // Total = PS + Café
        $this->assertEquals(
            round($expectedPsRevenue + $expectedCafeRevenue, 2),
            $report['total_revenue']
        );

        // Counts
        $this->assertEquals(2, $report['sessions_count']);
        $this->assertEquals(1, $report['cafe_orders_count']);
    }

    // ═══════════════════════════════════════════
    // 2. Payment Method Breakdown
    // ═══════════════════════════════════════════

    public function test_daily_report_payment_breakdown_by_method(): void
    {
        // Cash session
        $cashSession = $this->createClosedSession(
            $this->device1,
            30,
            [[$this->drink->id, 1]],  // 15.00 café
            'cash'
        );

        // Card café order
        $cardOrder = $this->createCompletedCafeOrder(
            'table',
            [[$this->snack->id, 2]],  // 16.00
            'card'
        );

        // Vodafone Cash café order
        $vodafoneOrder = $this->createCompletedCafeOrder(
            'takeaway',
            [[$this->drink->id, 1]],  // 15.00
            'vodafone_cash'
        );

        $report = $this->reportService->getDailyReport(Carbon::today()->format('Y-m-d'));
        $breakdown = $report['payment_breakdown'];

        // Cash: 1 transaction (the PS session)
        $this->assertTrue($breakdown->has('cash'));
        $this->assertEquals(1, $breakdown['cash']['count']);
        $this->assertEquals((float) $cashSession->final_amount, $breakdown['cash']['total']);

        // Card: 1 transaction (the table order)
        $this->assertTrue($breakdown->has('card'));
        $this->assertEquals(1, $breakdown['card']['count']);
        $this->assertEquals((float) $cardOrder->final_amount, $breakdown['card']['total']);

        // Vodafone Cash: 1 transaction
        $this->assertTrue($breakdown->has('vodafone_cash'));
        $this->assertEquals(1, $breakdown['vodafone_cash']['count']);
        $this->assertEquals((float) $vodafoneOrder->final_amount, $breakdown['vodafone_cash']['total']);
    }

    // ═══════════════════════════════════════════
    // 3. Discount Tracking
    // ═══════════════════════════════════════════

    public function test_daily_report_tracks_discounts(): void
    {
        $this->createClosedSession(
            $this->device1,
            30,
            [],
            'cash',
            10.00  // 10 discount
        );

        $this->createCompletedCafeOrder(
            'table',
            [[$this->snack->id, 5]],
            'cash',
            5.00  // 5 discount
        );

        $report = $this->reportService->getDailyReport(Carbon::today()->format('Y-m-d'));

        $this->assertEquals(15.00, $report['total_discount']);
    }

    // ═══════════════════════════════════════════
    // 4. Expenses & Net Profit
    // ═══════════════════════════════════════════

    public function test_daily_report_includes_expenses_and_net_profit(): void
    {
        // Revenue: one session
        $session = $this->createClosedSession(
            $this->device1,
            60,
            [[$this->drink->id, 4]],  // 60.00 café
            'cash'
        );

        // Expense
        Expense::create([
            'user_id'     => $this->admin->id,
            'amount'      => 50.00,
            'reason'      => 'شراء مستلزمات',
            'date'        => Carbon::today(),
        ]);

        $report = $this->reportService->getDailyReport(Carbon::today()->format('Y-m-d'));

        $this->assertEquals(50.00, $report['total_expenses']);

        // Net = total_revenue - total_expenses
        $expectedNet = $report['total_revenue'] - 50.00;
        $this->assertEquals(round($expectedNet, 2), $report['net_profit']);
    }

    // ═══════════════════════════════════════════
    // 5. Dashboard Stats Consistency
    // ═══════════════════════════════════════════

    public function test_dashboard_stats_reflect_current_state(): void
    {
        $this->actingAs($this->admin);

        // Start an active session (don't close it)
        $activeSession = $this->sessionService->startSession([
            'device_id'    => $this->device1->id,
            'session_type' => 'open',
        ]);

        // Create a closed session for revenue
        $closedSession = $this->createClosedSession(
            $this->device2,
            30,
            [[$this->drink->id, 2]],
            'cash'
        );

        // Create an open café order
        $openOrder = $this->cafeOrderService->createOrder(['order_type' => 'table']);

        $stats = $this->reportService->getDashboardStats();

        // Active sessions should include the one we started
        $this->assertGreaterThanOrEqual(1, $stats['active_sessions']->count());

        // Open café orders should include the one we created
        $this->assertGreaterThanOrEqual(1, $stats['open_cafe_orders']->count());

        // Revenue should include the closed session amounts
        $this->assertGreaterThan(0, $stats['today_revenue']);

        // Device1 is occupied (active session), device2 is available (session closed)
        $this->assertGreaterThanOrEqual(1, $stats['occupied_devices']);
        $this->assertGreaterThanOrEqual(1, $stats['available_devices']);
    }

    // ═══════════════════════════════════════════
    // 6. Empty Day Report
    // ═══════════════════════════════════════════

    public function test_daily_report_returns_zeros_for_empty_day(): void
    {
        $yesterday = Carbon::yesterday()->format('Y-m-d');
        $report = $this->reportService->getDailyReport($yesterday);

        $this->assertEquals(0, $report['sessions_count']);
        $this->assertEquals(0, $report['cafe_orders_count']);
        $this->assertEquals(0, $report['ps_revenue']);
        $this->assertEquals(0, $report['cafe_revenue']);
        $this->assertEquals(0, $report['total_revenue']);
        $this->assertEquals(0, $report['total_discount']);
        $this->assertEquals(0, $report['total_expenses']);
        $this->assertEquals(0, $report['net_profit']);
    }
}
