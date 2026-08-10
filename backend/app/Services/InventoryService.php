<?php

namespace App\Services;

use App\Models\Product;
use InvalidArgumentException;

/**
 * خدمة إدارة المخزون
 * تتحكم في خصم واستعادة الكميات مع تنبيهات النقص
 */
class InventoryService
{
    /**
     * خصم كمية من المخزون
     *
     * @param Product $product
     * @param int $quantity
     * @throws InvalidArgumentException إذا الكمية غير كافية
     */
    public function deductStock(Product $product, int $quantity): void
    {
        if (!$product->hasEnoughStock($quantity)) {
            throw new InvalidArgumentException(
                __('messages.errors.insufficient_stock', [
                    'product'   => $product->name,
                    'available' => $product->stock_quantity,
                ])
            );
        }

        $product->decrement('stock_quantity', $quantity);
    }

    /**
     * إعادة كمية إلى المخزون (عند الإلغاء)
     *
     * @param Product $product
     * @param int $quantity
     */
    public function restoreStock(Product $product, int $quantity): void
    {
        $product->increment('stock_quantity', $quantity);
    }

    /**
     * الحصول على المنتجات التي وصلت لحد التنبيه
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLowStockProducts()
    {
        return Product::lowStock()
            ->with('category')
            ->orderBy('stock_quantity', 'asc')
            ->get();
    }

    /**
     * تحديث مخزون منتج
     */
    public function updateStock(Product $product, int $newQuantity): void
    {
        $product->update(['stock_quantity' => $newQuantity]);
    }
}
