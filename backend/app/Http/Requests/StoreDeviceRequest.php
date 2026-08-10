<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'type'        => 'required|in:ps4,ps5,vr,billiard',
            'hourly_rate' => 'required|numeric|min:0',
            'status'      => 'sometimes|in:available,occupied,maintenance',
        ];
    }
}
