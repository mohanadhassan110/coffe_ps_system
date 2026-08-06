<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CloseSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'discount'       => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,vodafone_cash,card',
        ];
    }
}
