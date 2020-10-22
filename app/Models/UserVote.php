<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A vote cast by the user on the given poll, without value
 */
class UserVote extends Model
{
    /**
     * The attributes that should be cast.
     * @var array
     */
    protected $casts = [
        'poll_id' => 'int',
        'user_id' => 'int'
    ];

    /**
     * Associated poll
     * @return BelongsTo<Poll>
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Associated user
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
