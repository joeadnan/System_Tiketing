<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlaPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'priority_code',
        'priority_label',
        'response_minutes',
        'resolution_minutes',
        'warning_percentage',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'response_minutes' => 'integer',
            'resolution_minutes' => 'integer',
            'warning_percentage' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
