<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResolveTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'resolution_note' => ['required', 'string', 'min:10'],
            'root_cause' => ['required', 'string', 'max:255'],
            'prevention_note' => ['nullable', 'string'],
        ];
    }
}
