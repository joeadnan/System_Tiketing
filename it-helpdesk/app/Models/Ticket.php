<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_OPEN = 'open';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_PENDING_USER = 'pending_user';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_REOPENED = 'reopened';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'ticket_number',
        'title',
        'description',
        'reporter_id',
        'department_id',
        'location_id',
        'category_id',
        'assigned_team_level',
        'assigned_agent_id',
        'impact',
        'urgency',
        'priority_code',
        'priority_label',
        'source',
        'status',
        'first_response_due_at',
        'resolution_due_at',
        'first_responded_at',
        'resolved_at',
        'closed_at',
        'sla_paused_at',
        'sla_total_paused_minutes',
        'is_sla_response_breached',
        'is_sla_resolution_breached',
        'sla_warning_sent_at',
        'root_cause',
        'resolution_note',
        'prevention_note',
        'user_confirmed_at',
        'reopened_at',
        'reopen_count',
    ];

    protected function casts(): array
    {
        return [
            'first_response_due_at' => 'datetime',
            'resolution_due_at' => 'datetime',
            'first_responded_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
            'sla_paused_at' => 'datetime',
            'sla_total_paused_minutes' => 'integer',
            'is_sla_response_breached' => 'boolean',
            'is_sla_resolution_breached' => 'boolean',
            'sla_warning_sent_at' => 'datetime',
            'user_confirmed_at' => 'datetime',
            'reopened_at' => 'datetime',
            'reopen_count' => 'integer',
        ];
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(TicketStatusHistory::class);
    }

    public function assignmentHistories(): HasMany
    {
        return $this->hasMany(TicketAssignmentHistory::class);
    }

    public function csatSurvey(): HasOne
    {
        return $this->hasOne(CsatSurvey::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [self::STATUS_RESOLVED, self::STATUS_CLOSED, self::STATUS_CANCELLED]);
    }

    public function scopeBacklog(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_OPEN, self::STATUS_IN_PROGRESS, self::STATUS_PENDING_USER, self::STATUS_REOPENED]);
    }

    public function canBeReopened(): bool
    {
        return $this->status === self::STATUS_CLOSED
            && $this->closed_at
            && $this->closed_at->copy()->addHours(72)->greaterThanOrEqualTo(now());
    }

    public function isAssignedTo(User $user): bool
    {
        return (int) $this->assigned_agent_id === (int) $user->id;
    }
}
