<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

/**
 * نموذج الجلسة - الجدول الأساسي للنظام
 * يربط الأجهزة بالمبيعات ويحسب التكلفة الإجمالية
 */
class GameSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'game_sessions';

    protected $fillable = [
        'device_id',
        'user_id',
        'client_name',
        'session_type',
        'pre_paid_minutes',
        'active_controllers',
        'start_time',
        'end_time',
        'playstation_total',
        'cafe_total',
        'total_amount',
        'discount',
        'final_amount',
        'payment_method',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'playstation_total' => 'decimal:2',
            'cafe_total' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'discount' => 'decimal:2',
            'final_amount' => 'decimal:2',
            'pre_paid_minutes' => 'integer',
            'active_controllers' => 'integer',
        ];
    }

    // ========================
    // العلاقات (Relationships)
    // ========================

    /**
     * الجهاز المرتبط بالجلسة
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * الكاشير المسؤول عن الجلسة
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * عناصر الطلب (منتجات الكافيه) في الجلسة
     */
    public function items()
    {
        return $this->hasMany(SessionItem::class, 'game_session_id');
    }

    // ========================
    // النطاقات (Scopes)
    // ========================

    /**
     * الجلسات النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * الجلسات المغلقة فقط
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * جلسات يوم معين
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    // ========================
    // الدوال المساعدة (Helpers)
    // ========================

    /**
     * هل الجلسة نشطة؟
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * هل هي جلسة كافيه فقط (بدون جهاز)؟
     */
    public function isCafeOnly(): bool
    {
        return is_null($this->device_id);
    }

    /**
     * حساب الوقت المنقضي بالدقائق
     */
    public function getElapsedMinutesAttribute(): int
    {
        $end = $this->end_time ?? Carbon::now();
        return (int) $this->start_time->diffInMinutes($end);
    }

    /**
     * حساب الوقت المنقضي كنص مُنسق
     */
    public function getElapsedTimeFormattedAttribute(): string
    {
        $minutes = $this->elapsed_minutes;
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return "{$hours} ساعة و {$mins} دقيقة";
        }
        return "{$mins} دقيقة";
    }

    /**
     * حساب تكلفة وقت البلايستيشن
     */
    public function calculatePlaystationCost(): float
    {
        if ($this->isCafeOnly() || !$this->device) {
            return 0;
        }

        $minutes = $this->elapsed_minutes;
        $hourlyRate = (float) $this->device->hourly_rate;

        return round(($minutes / 60) * $hourlyRate, 2);
    }

    /**
     * الحصول على نوع الجلسة بالعربية
     */
    public function getSessionTypeNameAttribute(): string
    {
        return match ($this->session_type) {
            'open' => 'مفتوح',
            'pre_paid' => 'مدفوع مسبقاً',
            default => $this->session_type,
        };
    }

    /**
     * نص حالة الأذرع للعرض (مثال: الأذرع النشطة: 2 / المتاحة: 2)
     */
    public function getControllersStatusAttribute(): string
    {
        if (is_null($this->device_id) || !$this->device) {
            return '';
        }

        return __('messages.controllers.status_line', [
            'active' => $this->active_controllers,
            'available' => $this->device->idle_controllers,
        ]);
    }

    /**
     * الحصول على حالة الجلسة بالعربية
     */
    public function getStatusNameAttribute(): string
    {
        return match ($this->status) {
            'active' => 'نشطة',
            'closed' => 'مغلقة',
            'cancelled' => 'ملغاة',
            default => $this->status,
        };
    }

    /**
     * الحصول على طريقة الدفع بالعربية
     */
    public function getPaymentMethodNameAttribute(): string
    {
        return match ($this->payment_method) {
            'cash' => 'كاش',
            'vodafone_cash' => 'فودافون كاش',
            'card' => 'بطاقة',
            default => $this->payment_method,
        };
    }
}
