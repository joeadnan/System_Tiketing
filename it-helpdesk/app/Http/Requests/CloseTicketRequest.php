<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CloseTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'user_confirmed' => ['required', 'accepted'],
        ];
    }
}
