<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketAssignmentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'assigned_from_user_id',
        'assigned_to_user_id',
        'from_level',
        'to_level',
        'vendor_name',
        'vendor_contact',
        'reason',
        'handover_note',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function assignedFrom(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_from_user_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }
}
