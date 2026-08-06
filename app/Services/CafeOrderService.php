<?php

namespace App\Services;

use App\Models\CafeOrder;
use App\Models\CafeOrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * خدمة إدارة طلبات الكافيه المستقلة (طاولات وتيك أواي)
 */
class CafeOrderService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * إنشاء طلب كافيه جديد
     *
     * @param array $data ['order_type', 'table_number', 'client_name']
     * @return CafeOrder
     */
    public function createOrder(array $data): CafeOrder
    {
        return DB::transaction(function () use ($data) {
            return CafeOrder::create([
                'order_type'   => $data['order_type'] ?? 'table',
                'table_number' => $data['table_number'] ?? null,
                'client_name'  => $data['client_name'] ?? null,
                'user_id'      => Auth::id(),
                'status'       => 'open',
                'total_amount' => 0,
                'discount'     => 0,
                'final_amount' => 0,
            ]);
        });
    }

    /**
     * إضافة منتج إلى طلب كافيه مفتوح
     *
     * @param CafeOrder $order
     * @param int $productId
     * @param int $quantity
     * @return CafeOrderItem
     * @throws InvalidArgumentException
     */
    public function addItem(CafeOrder $order, int $productId, int $quantity): CafeOrderItem
    {
        if (!$order->isOpen()) {
            throw new InvalidArgumentException(__('messages.errors.session_not_active'));
        }

        return DB::transaction(function () use ($order, $productId, $quantity) {
            $product = Product::lockForUpdate()->findOrFail($productId);

            // خصم المخزون باستخدام خدمة المخزون الموحدة
            $this->inventoryService->deductStock($product, $quantity);

            $existingItem = $order->items()->where('product_id', $productId)->first();

            if ($existingItem) {
                $newQty = $existingItem->quantity + $quantity;
                $existingItem->update([
                    'quantity'    => $newQty,
                    'total_price' => $newQty * $existingItem->unit_price,
                ]);
                $item = $existingItem;
            } else {
                $item = CafeOrderItem::create([
                    'cafe_order_id' => $order->id,
                    'product_id'    => $productId,
                    'quantity'      => $quantity,
                    'unit_price'    => $product->sale_price,
                    'total_price'   => $product->sale_price * $quantity,
                ]);
            }

            $this->updateOrderTotals($order);

            return $item->load('product');
        });
    }

    /**
     * إزالة عنصر من الطلب وإعادة كميته للمخزون
     */
    public function removeItem(CafeOrder $order, int $itemId): void
    {
        if (!$order->isOpen()) {
            throw new InvalidArgumentException(__('messages.errors.session_not_active'));
        }

        DB::transaction(function () use ($order, $itemId) {
            $item = $order->items()->findOrFail($itemId);

            // استعادة المخزون
            $this->inventoryService->restoreStock(
                Product::findOrFail($item->product_id),
                $item->quantity
            );

            $item->delete();

            $this->updateOrderTotals($order);
        });
    }

    /**
     * إغلاق وتحصيل طلب الكافيه
     *
     * @param CafeOrder $order
     * @param array $data ['discount', 'payment_method']
     * @return CafeOrder
     */
    public function checkout(CafeOrder $order, array $data = []): CafeOrder
    {
        if (!$order->isOpen()) {
            throw new InvalidArgumentException(__('messages.errors.session_already_closed'));
        }

        return DB::transaction(function () use ($order, $data) {
            $totalAmount = (float) $order->items()->sum('total_price');
            $discount = (float) ($data['discount'] ?? 0);
            $finalAmount = max(0, $totalAmount - $discount);

            $order->update([
                'total_amount'   => $totalAmount,
                'discount'       => $discount,
                'final_amount'   => $finalAmount,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'status'         => 'completed',
            ]);

            return $order->fresh(['user', 'items.product']);
        });
    }

    /**
     * إلغاء الطلب وإعادة كامل المخزون
     */
    public function cancelOrder(CafeOrder $order): CafeOrder
    {
        if (!$order->isOpen()) {
            throw new InvalidArgumentException(__('messages.errors.session_not_active'));
        }

        return DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $this->inventoryService->restoreStock(
                    Product::findOrFail($item->product_id),
                    $item->quantity
                );
            }

            $order->update([
                'status' => 'cancelled',
            ]);

            return $order->fresh(['user']);
        });
    }

    /**
     * تحديث الإجمالي للطلب
     */
    protected function updateOrderTotals(CafeOrder $order): void
    {
        $totalAmount = (float) $order->items()->sum('total_price');
        $finalAmount = max(0, $totalAmount - (float) $order->discount);

        $order->update([
            'total_amount' => $totalAmount,
            'final_amount' => $finalAmount,
        ]);
    }
}
