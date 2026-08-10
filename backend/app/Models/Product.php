<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * نموذج المنتجات - مع تتبع المخزون وتنبيهات النقص
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'purchase_price',
        'sale_price',
        'stock_quantity',
        'min_stock_alert',
    ];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'min_stock_alert' => 'integer',
        ];
    }

    // ========================
    // العلاقات (Relationships)
    // ========================

    /**
     * التصنيف الذي ينتمي إليه المنتج
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * عناصر الجلسات المرتبطة بهذا المنتج
     */
    public function sessionItems()
    {
        return $this->hasMany(SessionItem::class);
    }

    /**
     * عناصر طلبات الكافيه المستقلة المرتبطة بهذا المنتج
     */
    public function cafeOrderItems()
    {
        return $this->hasMany(CafeOrderItem::class);
    }

    // ========================
    // النطاقات (Scopes)
    // ========================

    /**
     * المنتجات التي وصلت لحد التنبيه
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock_alert');
    }

    // ========================
    // الدوال المساعدة (Helpers)
    // ========================

    /**
     * هل المخزون منخفض؟
     */
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->min_stock_alert;
    }

    /**
     * هل الكمية المطلوبة متوفرة؟
     */
    public function hasEnoughStock(int $quantity): bool
    {
        return $this->stock_quantity >= $quantity;
    }

    /**
     * هامش الربح
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->purchase_price <= 0) {
            return 0;
        }
        return round((($this->sale_price - $this->purchase_price) / $this->purchase_price) * 100, 2);
    }
}
