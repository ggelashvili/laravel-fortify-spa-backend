<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Ticket
 *
 * @property int            $id
 * @property int            $ticket_number
 * @property string         $subject
 * @property string         $content
 * @property int            $user_id
 * @property int            $agent_id
 * @property int|null       $priority_id
 * @property Carbon|null    $created_at
 * @property Carbon|null    $updated_at
 * @property string|null    $completed_at
 * @property string|null    $deleted_at
 *
 * @property-read User|null $user
 * @property-read User|null $agent
 *
 * @method static Builder|Ticket newModelQuery()
 * @method static Builder|Ticket newQuery()
 * @method static Builder|Ticket query()
 * @method static Builder|Ticket whereAgentId($value)
 * @method static Builder|Ticket whereCompletedAt($value)
 * @method static Builder|Ticket whereContent($value)
 * @method static Builder|Ticket whereCreatedAt($value)
 * @method static Builder|Ticket whereDeletedAt($value)
 * @method static Builder|Ticket whereId($value)
 * @method static Builder|Ticket wherePriorityId($value)
 * @method static Builder|Ticket whereSubject($value)
 * @method static Builder|Ticket whereTicketNumber($value)
 * @method static Builder|Ticket whereUpdatedAt($value)
 * @method static Builder|Ticket whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeIncomplete(Builder $query): Builder
    {
        return $query->whereNull('completed_at');
    }
}
