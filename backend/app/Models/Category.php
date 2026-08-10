<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * نموذج التصنيفات - تصنيف المنتجات (مشروبات، تسالي، وجبات)
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // ========================
    // العلاقات (Relationships)
    // ========================

    /**
     * منتجات هذا التصنيف
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
