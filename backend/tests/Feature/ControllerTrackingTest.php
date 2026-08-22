<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\GameSession;
use App\Models\User;
use App\Services\SessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * اختبارات تتبع أذرع التحكم لكل جهاز ولكل جلسة نشطة
 * Controller Tracking — per-device and per-session availability tests
 */
class ControllerTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Device $device;
    protected SessionService $sessionService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'مدير اختبار',
            'username' => 'ctrladmin',
            'password' => 'password',
            'role'     => 'admin',
        ]);

        // جهاز PS5 بأربعة أذرع مادية
        $this->device = Device::create([
            'name'              => 'PS5 - CTRL',
            'type'              => 'ps5',
            'hourly_rate'       => 40.00,
            'status'            => 'available',
            'total_controllers' => 4,
        ]);

        $this->sessionService = app(SessionService::class);
    }

    // ═══════════════════════════════════════════
    // 1. Starting sessions with controllers
    // ═══════════════════════════════════════════

    public function test_start_session_records_requested_controllers(): void
    {
        $this->actingAs($this->admin);

        $session = $this->sessionService->startSession([
            'device_id'          => $this->device->id,
            'session_type'       => 'open',
            'active_controllers' => 3,
        ]);

        $this->assertEquals(3, $session->active_controllers);
    }

    public function test_default_controllers_is_two_for_multiplayer(): void
    {
        $this->actingAs($this->admin);

        // لم يتم تحديد عدد الأذرع → الافتراضي ذراعان
        $session = $this->sessionService->startSession([
            'device_id'    => $this->device->id,
            'session_type' => 'open',
        ]);

        $this->assertEquals(2, $session->active_controllers);
    }

    public function test_cannot_exceed_device_total_controllers_on_start(): void
    {
        $this->actingAs($this->admin);

        $this->expectException(InvalidArgumentException::class);

        $this->sessionService->startSession([
            'device_id'          => $this->device->id,
            'session_type'       => 'open',
            'active_controllers' => 5, // الجهاز يحتوي 4 فقط
        ]);
    }

    public function test_cafe_only_session_has_zero_controllers(): void
    {
        $this->actingAs($this->admin);

        $session = $this->sessionService->startSession([
            'session_type' => 'open',
        ]);

        $this->assertEquals(0, $session->active_controllers);
    }

    // ═══════════════════════════════════════════
    // 2. Updating controllers mid-session
    // ═══════════════════════════════════════════

    public function test_update_active_controllers_mid_session(): void
    {
        $this->actingAs($this->admin);

        $session = $this->sessionService->startSession([
            'device_id'          => $this->device->id,
            'session_type'       => 'open',
            'active_controllers' => 2,
        ]);

        $updated = $this->sessionService->updateActiveControllers($session, 4);

        $this->assertEquals(4, $updated->active_controllers);
    }

    public function test_update_cannot_exceed_device_total(): void
    {
        $this->actingAs($this->admin);

        $session = $this->sessionService->startSession([
            'device_id'          => $this->device->id,
            'session_type'       => 'open',
            'active_controllers' => 2,
        ]);

        $this->expectException(InvalidArgumentException::class);

        $this->sessionService->updateActiveControllers($session, 9);
    }

    public function test_update_rejects_less_than_one_controller(): void
    {
        $this->actingAs($this->admin);

        $session = $this->sessionService->startSession([
            'device_id'          => $this->device->id,
            'session_type'       => 'open',
            'active_controllers' => 2,
        ]);

        $this->expectException(InvalidArgumentException::class);

        $this->sessionService->updateActiveControllers($session, 0);
    }

    public function test_cannot_update_controllers_after_close(): void
    {
        $this->actingAs($this->admin);

        $session = $this->sessionService->startSession([
            'device_id'          => $this->device->id,
            'session_type'       => 'open',
            'active_controllers' => 2,
        ]);

        $this->sessionService->closeSession($session, ['payment_method' => 'cash']);

        $this->expectException(InvalidArgumentException::class);

        $session->refresh();
        $this->sessionService->updateActiveControllers($session, 3);
    }

    // ═══════════════════════════════════════════
    // 3. Lounge-wide real-time tracking
    // ═══════════════════════════════════════════

    public function test_lounge_controller_stats_reflect_active_sessions(): void
    {
        $this->actingAs($this->admin);

        $secondDevice = Device::create([
            'name'              => 'PS5 - CTRL-2',
            'type'              => 'ps5',
            'hourly_rate'       => 40.00,
            'status'            => 'available',
            'total_controllers' => 4,
        ]);

        // الصالة تبدأ بـ 8 أذرع متاحة بالكامل
        $stats = $this->sessionService->getLoungeControllersStats();
        $this->assertEquals(8, $stats['total']);
        $this->assertEquals(8, $stats['available']);
        $this->assertEquals(0, $stats['occupied']);

        // جلسة بثلاثة أذرع نشطة
        $this->sessionService->startSession([
            'device_id'          => $this->device->id,
            'session_type'       => 'open',
            'active_controllers' => 3,
        ]);

        $stats = $this->sessionService->getLoungeControllersStats();
        $this->assertEquals(3, $stats['occupied']);
        $this->assertEquals(5, $stats['available']);

        // جلسة ثانية بذراعين
        $this->sessionService->startSession([
            'device_id'          => $secondDevice->id,
            'session_type'       => 'open',
            'active_controllers' => 2,
        ]);

        $stats = $this->sessionService->getLoungeControllersStats();
        $this->assertEquals(5, $stats['occupied']);
        $this->assertEquals(3, $stats['available']);
    }

    public function test_device_idle_controllers_attribute(): void
    {
        $this->actingAs($this->admin);

        $session = $this->sessionService->startSession([
            'device_id'          => $this->device->id,
            'session_type'       => 'open',
            'active_controllers' => 3,
        ]);

        $device = $this->device->fresh(['activeSession']);

        $this->assertEquals(3, $device->occupied_controllers);
        $this->assertEquals(1, $device->idle_controllers);
    }

    // ═══════════════════════════════════════════
    // 4. HTTP layer
    // ═══════════════════════════════════════════

    public function test_http_start_session_with_controllers(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('sessions.store'), [
            'device_id'          => $this->device->id,
            'session_type'       => 'open',
            'active_controllers' => 4,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('game_sessions', [
            'device_id'          => $this->device->id,
            'status'             => 'active',
            'active_controllers' => 4,
        ]);
    }

    public function test_http_update_controllers_mid_session(): void
    {
        $this->actingAs($this->admin);

        $session = $this->sessionService->startSession([
            'device_id'          => $this->device->id,
            'session_type'       => 'open',
            'active_controllers' => 2,
        ]);

        $response = $this->post(route('sessions.updateControllers', $session), [
            'active_controllers' => 4,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('game_sessions', [
            'id'                 => $session->id,
            'active_controllers' => 4,
        ]);
    }

    public function test_http_update_controllers_validates_max(): void
    {
        $this->actingAs($this->admin);

        $session = $this->sessionService->startSession([
            'device_id'          => $this->device->id,
            'session_type'       => 'open',
            'active_controllers' => 2,
        ]);

        $response = $this->from(route('pos.index'))
            ->post(route('sessions.updateControllers', $session), [
                'active_controllers' => 99,
            ]);

        $response->assertRedirect(route('pos.index'));
        $response->assertSessionHasErrors('active_controllers');
    }
}
