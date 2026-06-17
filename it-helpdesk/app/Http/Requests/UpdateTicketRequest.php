<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
            'department_id' => ['required', 'exists:departments,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'category_id' => ['required', 'exists:ticket_categories,id'],
            'impact' => ['required', Rule::in(['wide', 'narrow'])],
            'urgency' => ['required', Rule::in(['high', 'low'])],
        ];
    }
}
