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
        'total_controllers',
    ];

    protected function casts(): array
    {
        return [
            'hourly_rate' => 'decimal:2',
            'total_controllers' => 'integer',
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
     * عدد الأذرع المشغولة حالياً على هذا الجهاز (من الجلسة النشطة)
     */
    public function getOccupiedControllersAttribute(): int
    {
        if (!$this->relationLoaded('activeSession') || is_null($this->activeSession)) {
            return 0;
        }

        return (int) $this->activeSession->active_controllers;
    }

    /**
     * عدد الأذرع المتاحة (الخاملة) لحظياً على هذا الجهاز
     */
    public function getIdleControllersAttribute(): int
    {
        return max(0, $this->total_controllers - $this->occupied_controllers);
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
