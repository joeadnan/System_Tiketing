<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EscalateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'to_level' => ['required', Rule::in(['L2', 'L3', 'vendor'])],
            'reason' => ['required', 'string', 'max:255'],
            'handover_note' => ['required', 'string', 'min:10'],
            'vendor_name' => ['nullable', 'required_if:to_level,vendor', 'string', 'max:255'],
            'vendor_contact' => ['nullable', 'required_if:to_level,vendor', 'string', 'max:255'],
        ];
    }
}
