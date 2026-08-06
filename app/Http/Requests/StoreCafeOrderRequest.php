<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCafeOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_type'   => 'required|in:table,takeaway',
            'table_number' => 'required_if:order_type,table|nullable|string|max:50',
            'client_name'  => 'nullable|string|max:255',
        ];
    }
}
