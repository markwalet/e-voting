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
    /**
     * The attributes that should be cast.
     * @var array
     */
    protected $casts = [
        'poll_id' => 'int'
    ];

    /**
     * Associated poll
     * @return BelongsTo<Poll>
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }
}
