<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * نموذج المستخدم - يدعم أدوار: مدير، كاشير، موظف
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // ========================
    // العلاقات (Relationships)
    // ========================

    /**
     * الجلسات التي أدارها هذا المستخدم
     */
    public function gameSessions()
    {
        return $this->hasMany(GameSession::class);
    }

    /**
     * طلبات الكافيه المستقلة التي أدارها هذا المستخدم
     */
    public function cafeOrders()
    {
        return $this->hasMany(CafeOrder::class);
    }

    /**
     * المصروفات المسجلة بواسطة هذا المستخدم
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    // ========================
    // الدوال المساعدة (Helpers)
    // ========================

    /**
     * هل المستخدم مدير؟
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * هل المستخدم كاشير؟
     */
    public function isCashier(): bool
    {
        return $this->role === 'cashier';
    }

    /**
     * الحصول على اسم الدور بالعربية
     */
    public function getRoleNameAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'مدير',
            'cashier' => 'كاشير',
            'staff' => 'موظف',
            default => $this->role,
        };
    }
}
