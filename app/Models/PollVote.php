<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A vote cast on the poll, without owner
 */
class PollVote extends Model
{
    public const VALID_VOTES = [
        'favor' => 'Voor',
        'against' => 'Tegen',
        'blank' => 'Onthouding'
    ];

    /**
     * The attributes that should be cast.
     * @var array
     */
    protected $casts = [
        'poll_id' => 'int'
    ];

    /**
     * The attributes that are mass assignable.
     * @var array<string>
     */
    protected $fillable = [
        'poll_id',
        'vote'
    ];

    /**
     * Associated poll
     * @return BelongsTo<Poll>
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    public function getVoteLabelAttribute(): string
    {
        return self::VALID_VOTES[$this->vote] ?? $this->vote;
    }
}
