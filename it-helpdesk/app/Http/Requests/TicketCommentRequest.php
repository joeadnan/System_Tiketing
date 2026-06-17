<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TicketCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'comment' => ['required', 'string', 'min:2'],
            'status' => ['nullable', Rule::in(['open', 'in_progress', 'pending_user', 'resolved', 'closed', 'reopened', 'cancelled'])],
            'is_internal' => ['nullable', 'boolean'],
        ];
    }
}
