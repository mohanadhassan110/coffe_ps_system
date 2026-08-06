<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * نموذج عناصر طلبات الكافيه المستقلة
 */
class CafeOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cafe_order_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    // ========================
    // العلاقات (Relationships)
    // ========================

    public function cafeOrder()
    {
        return $this->belongsTo(CafeOrder::class, 'cafe_order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
