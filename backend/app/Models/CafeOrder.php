<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * نموذج طلبات الكافيه المستقلة (طاولات وتيك أواي)
 */
class CafeOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_type',
        'table_number',
        'client_name',
        'user_id',
        'total_amount',
        'discount',
        'final_amount',
        'payment_method',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'discount' => 'decimal:2',
            'final_amount' => 'decimal:2',
        ];
    }

    // ========================
    // العلاقات (Relationships)
    // ========================

    /**
     * الكاشير المسؤول عن الطلب
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * عناصر الطلب
     */
    public function items()
    {
        return $this->hasMany(CafeOrderItem::class, 'cafe_order_id');
    }

    // ========================
    // النطاقات (Scopes)
    // ========================

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // ========================
    // الدوال المساعدة (Helpers)
    // ========================

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function getOrderTypeNameAttribute(): string
    {
        return match ($this->order_type) {
            'table' => 'طاولة ' . ($this->table_number ? '(' . $this->table_number . ')' : ''),
            'takeaway' => 'تيك أواي',
            default => $this->order_type,
        };
    }

    public function getStatusNameAttribute(): string
    {
        return match ($this->status) {
            'open' => 'مفتوحة',
            'completed' => 'مكتملة',
            'cancelled' => 'ملغاة',
            default => $this->status,
        };
    }

    public function getPaymentMethodNameAttribute(): string
    {
        return match ($this->payment_method) {
            'cash' => 'كاش',
            'vodafone_cash' => 'فودافون كاش',
            'card' => 'بطاقة',
            default => $this->payment_method ?? '-',
        };
    }
}
