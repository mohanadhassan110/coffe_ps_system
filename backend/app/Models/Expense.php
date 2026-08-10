<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * نموذج المصروفات اليومية
 */
class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'reason',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'date' => 'date',
        ];
    }

    // ========================
    // العلاقات (Relationships)
    // ========================

    /**
     * المستخدم الذي أضاف المصروف
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========================
    // النطاقات (Scopes)
    // ========================

    /**
     * مصروفات يوم معين
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }
}
