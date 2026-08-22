<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_id'           => 'nullable|exists:devices,id',
            'session_type'        => 'required|in:open,pre_paid',
            'pre_paid_minutes'    => 'required_if:session_type,pre_paid|nullable|integer|min:15',
            'client_name'         => 'nullable|string|max:255',
            'active_controllers'  => 'nullable|integer|min:1|max:8',
        ];
    }
}

