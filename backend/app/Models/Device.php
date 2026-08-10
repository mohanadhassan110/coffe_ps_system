<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * نموذج الأجهزة - بلايستيشن 4، 5، VR، بلياردو
 */
class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'hourly_rate',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'hourly_rate' => 'decimal:2',
        ];
    }

    // ========================
    // العلاقات (Relationships)
    // ========================

    /**
     * جلسات هذا الجهاز
     */
    public function gameSessions()
    {
        return $this->hasMany(GameSession::class);
    }

    /**
     * الجلسة النشطة الحالية على هذا الجهاز
     */
    public function activeSession()
    {
        return $this->hasOne(GameSession::class)->where('status', 'active');
    }

    // ========================
    // النطاقات (Scopes)
    // ========================

    /**
     * الأجهزة المتاحة فقط
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * الأجهزة المشغولة فقط
     */
    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    // ========================
    // الدوال المساعدة (Helpers)
    // ========================

    /**
     * هل الجهاز متاح؟
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /**
     * الحصول على نوع الجهاز بالعربية
     */
    public function getTypeNameAttribute(): string
    {
        return match ($this->type) {
            'ps4' => 'بلايستيشن 4',
            'ps5' => 'بلايستيشن 5',
            'vr' => 'في آر',
            'billiard' => 'بلياردو',
            default => $this->type,
        };
    }

    /**
     * الحصول على حالة الجهاز بالعربية
     */
    public function getStatusNameAttribute(): string
    {
        return match ($this->status) {
            'available' => 'متاحة',
            'occupied' => 'مشغولة',
            'maintenance' => 'صيانة',
            default => $this->status,
        };
    }
}
