<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * نموذج عناصر الجلسة - المنتجات المطلوبة في كل جلسة
 */
class SessionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_session_id',
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

    /**
     * الجلسة التي ينتمي إليها هذا العنصر
     */
    public function gameSession()
    {
        return $this->belongsTo(GameSession::class, 'game_session_id');
    }

    /**
     * المنتج المرتبط
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
