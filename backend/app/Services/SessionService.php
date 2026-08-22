<?php

namespace App\Services;

use App\Models\Device;
use App\Models\GameSession;
use App\Models\Product;
use App\Models\SessionItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * خدمة إدارة الجلسات
 * تتحكم في دورة حياة الجلسة: البدء، إضافة المنتجات، الإغلاق، الإلغاء
 */
class SessionService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * بدء جلسة جديدة على جهاز معين
     *
     * @param array $data ['device_id', 'session_type', 'pre_paid_minutes', 'client_name', 'active_controllers']
     * @return GameSession
     * @throws InvalidArgumentException
     */
    public function startSession(array $data): GameSession
    {
        return DB::transaction(function () use ($data) {
            $device = null;
            $requestedControllers = isset($data['active_controllers'])
                ? max(1, (int) $data['active_controllers'])
                : null; // افتراضياً: لعب جماعي بذراعين (أو حسب المتاح بالجهاز)

            // التحقق من الجهاز إذا تم تحديده (ليس طلب كافيه فقط)
            if (!empty($data['device_id'])) {
                $device = Device::lockForUpdate()->findOrFail($data['device_id']);

                if (!$device->isAvailable()) {
                    throw new InvalidArgumentException(
                        __('messages.errors.device_not_available')
                    );
                }

                if ($requestedControllers === null) {
                    // افتراضي: لعب جماعي بذراعين أو حسب المتاح بالجهاز
                    $requestedControllers = $device->total_controllers > 0
                        ? min(2, $device->total_controllers)
                        : 0;
                }

                // ═══ التحقق من صلاحية عدد الأذرع المطلوبة ═══
                if ($requestedControllers > 0) {
                    $this->validateControllerAvailability($device, $requestedControllers);
                }

                // تحديث حالة الجهاز إلى مشغول
                $device->update(['status' => 'occupied']);
            }

            // إنشاء الجلسة
            $session = GameSession::create([
                'device_id'          => $data['device_id'] ?? null,
                'user_id'            => Auth::id(),
                'client_name'        => $data['client_name'] ?? null,
                'session_type'       => $data['session_type'] ?? 'open',
                'pre_paid_minutes'   => $data['pre_paid_minutes'] ?? null,
                'active_controllers' => empty($data['device_id']) ? 0 : $requestedControllers,
                'start_time'         => Carbon::now(),
                'status'             => 'active',
            ]);

            return $session->load('device', 'user');
        });
    }

    /**
     * تحديث عدد الأذرع النشطة في جلسة قائمة
     * (زيادة أو تقليل عدد اللاعبين أثناء الجلسة)
     *
     * @throws InvalidArgumentException
     */
    public function updateActiveControllers(GameSession $session, int $controllers): GameSession
    {
        if (!$session->isActive()) {
            throw new InvalidArgumentException(
                __('messages.errors.session_not_active')
            );
        }

        if ($session->isCafeOnly()) {
            throw new InvalidArgumentException(
                __('messages.errors.controllers_cafe_only')
            );
        }

        return DB::transaction(function () use ($session, $controllers) {
            $device = Device::lockForUpdate()->findOrFail($session->device_id);

            // الأذرع المستهلكة حالياً من الجلسات الأخرى على نفس الجهاز
            $usedByOthers = GameSession::active()
                ->where('device_id', $device->id)
                ->where('id', '!=', $session->id)
                ->sum('active_controllers');

            $idle = $device->total_controllers - (int) $usedByOthers;

            if ($controllers < 1) {
                throw new InvalidArgumentException(
                    __('messages.errors.controllers_min')
                );
            }

            if ($controllers > $device->total_controllers) {
                throw new InvalidArgumentException(
                    __('messages.errors.controllers_exceed_total', [
                        'total' => $device->total_controllers,
                    ])
                );
            }

            if ($controllers > $idle) {
                throw new InvalidArgumentException(
                    __('messages.errors.controllers_not_idle', [
                        'available' => max(0, $idle),
                    ])
                );
            }

            $session->update(['active_controllers' => $controllers]);

            return $session->fresh(['device', 'user', 'items.product']);
        });
    }

    /**
     * التحقق من أن عدد الأذرع المطلوب متاح فعلياً قبل بدء الجلسة
     *
     * @throws InvalidArgumentException
     */
    protected function validateControllerAvailability(Device $device, int $requested): void
    {
        if ($requested < 1) {
            throw new InvalidArgumentException(
                __('messages.errors.controllers_min')
            );
        }

        // لا يمكن طلب أذرع أكثر من المنافذ المادية للجهاز
        if ($requested > $device->total_controllers) {
            throw new InvalidArgumentException(
                __('messages.errors.controllers_exceed_total', [
                    'total' => $device->total_controllers,
                ])
            );
        }

        // لا يمكن طلب أذرع أكثر من الأذرع الخاملة حالياً على الجهاز
        $usedByOthers = GameSession::active()
            ->where('device_id', $device->id)
            ->sum('active_controllers');

        $idle = $device->total_controllers - (int) $usedByOthers;

        if ($requested > $idle) {
            throw new InvalidArgumentException(
                __('messages.errors.controllers_not_idle', [
                    'available' => max(0, $idle),
                ])
            );
        }
    }

    /**
     * إحصائيات أذرع التحكم لحظياً على مستوى الصالة كلها
     *
     * @return array{total: int, occupied: int, available: int}
     */
    public function getLoungeControllersStats(): array
    {
        $total = (int) Device::where('type', '!=', 'billiard')->sum('total_controllers');

        $occupied = (int) GameSession::active()
            ->whereNotNull('device_id')
            ->sum('active_controllers');

        return [
            'total'     => $total,
            'occupied'  => min($occupied, $total),
            'available' => max(0, $total - $occupied),
        ];
    }

    /**
     * إضافة منتج إلى جلسة نشطة
     *
     * @param GameSession $session
     * @param int $productId
     * @param int $quantity
     * @return SessionItem
     * @throws InvalidArgumentException
     */
    public function addItemToSession(GameSession $session, int $productId, int $quantity): SessionItem
    {
        if (!$session->isActive()) {
            throw new InvalidArgumentException(
                __('messages.errors.session_not_active')
            );
        }

        return DB::transaction(function () use ($session, $productId, $quantity) {
            $product = Product::lockForUpdate()->findOrFail($productId);

            // خصم المخزون (يلقي استثناء إذا الكمية غير كافية)
            $this->inventoryService->deductStock($product, $quantity);

            // التحقق من وجود نفس المنتج في الجلسة لتحديثه بدلاً من إنشاء سجل جديد
            $existingItem = $session->items()->where('product_id', $productId)->first();

            if ($existingItem) {
                $existingItem->update([
                    'quantity'    => $existingItem->quantity + $quantity,
                    'total_price' => ($existingItem->quantity + $quantity) * $existingItem->unit_price,
                ]);
                $item = $existingItem;
            } else {
                $item = SessionItem::create([
                    'game_session_id' => $session->id,
                    'product_id'      => $productId,
                    'quantity'        => $quantity,
                    'unit_price'      => $product->sale_price,
                    'total_price'     => $product->sale_price * $quantity,
                ]);
            }

            // تحديث إجمالي الكافيه في الجلسة
            $this->updateSessionCafeTotals($session);

            return $item->load('product');
        });
    }

    /**
     * إزالة عنصر من الجلسة (مع إعادة المخزون)
     */
    public function removeItemFromSession(GameSession $session, int $itemId): void
    {
        if (!$session->isActive()) {
            throw new InvalidArgumentException(
                __('messages.errors.session_not_active')
            );
        }

        DB::transaction(function () use ($session, $itemId) {
            $item = $session->items()->findOrFail($itemId);

            // إعادة المخزون
            $this->inventoryService->restoreStock(
                Product::findOrFail($item->product_id),
                $item->quantity
            );

            $item->delete();

            // تحديث إجمالي الكافيه في الجلسة
            $this->updateSessionCafeTotals($session);
        });
    }

    /**
     * إغلاق الجلسة وحساب الفاتورة النهائية
     *
     * @param GameSession $session
     * @param array $data ['discount', 'payment_method']
     * @return GameSession
     * @throws InvalidArgumentException
     */
    public function closeSession(GameSession $session, array $data = []): GameSession
    {
        if (!$session->isActive()) {
            throw new InvalidArgumentException(
                __('messages.errors.session_already_closed')
            );
        }

        return DB::transaction(function () use ($session, $data) {
            $now = Carbon::now();

            // حساب تكلفة البلايستيشن
            $session->end_time = $now;
            $playstationTotal = $session->calculatePlaystationCost();

            // حساب إجمالي الكافيه
            $cafeTotal = (float) $session->items()->sum('total_price');

            // الحسابات المالية
            $totalAmount = $playstationTotal + $cafeTotal;
            $discount = (float) ($data['discount'] ?? 0);
            $finalAmount = max(0, $totalAmount - $discount);

            // تحديث الجلسة
            $session->update([
                'end_time'          => $now,
                'playstation_total' => $playstationTotal,
                'cafe_total'        => $cafeTotal,
                'total_amount'      => $totalAmount,
                'discount'          => $discount,
                'final_amount'      => $finalAmount,
                'payment_method'    => $data['payment_method'] ?? 'cash',
                'status'            => 'closed',
            ]);

            // إعادة حالة الجهاز إلى متاح
            if ($session->device_id) {
                Device::where('id', $session->device_id)
                    ->update(['status' => 'available']);
            }

            return $session->fresh(['device', 'user', 'items.product']);
        });
    }

    /**
     * إلغاء الجلسة وإعادة المخزون والجهاز
     */
    public function cancelSession(GameSession $session): GameSession
    {
        if (!$session->isActive()) {
            throw new InvalidArgumentException(
                __('messages.errors.session_not_active')
            );
        }

        return DB::transaction(function () use ($session) {
            // إعادة المخزون لجميع العناصر
            foreach ($session->items as $item) {
                $this->inventoryService->restoreStock(
                    Product::findOrFail($item->product_id),
                    $item->quantity
                );
            }

            // تحديث حالة الجلسة
            $session->update([
                'end_time' => Carbon::now(),
                'status'   => 'cancelled',
            ]);

            // إعادة حالة الجهاز إلى متاح
            if ($session->device_id) {
                Device::where('id', $session->device_id)
                    ->update(['status' => 'available']);
            }

            return $session->fresh(['device', 'user']);
        });
    }

    /**
     * تحديث إجمالي الكافيه في الجلسة
     */
    protected function updateSessionCafeTotals(GameSession $session): void
    {
        $cafeTotal = $session->items()->sum('total_price');
        $session->update(['cafe_total' => $cafeTotal]);
    }
}
