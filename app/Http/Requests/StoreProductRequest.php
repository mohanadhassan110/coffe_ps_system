<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id'     => 'required|exists:categories,id',
            'name'            => 'required|string|max:255',
            'purchase_price'  => 'required|numeric|min:0',
            'sale_price'      => 'required|numeric|min:0',
            'stock_quantity'  => 'required|integer|min:0',
            'min_stock_alert' => 'required|integer|min:0',
        ];
    }
}
