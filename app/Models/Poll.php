<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Date;

class Poll extends Model
{
    /**
     * The attributes that should be cast.
     * @var array
     */
    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'results' => 'json'
    ];

    /**
     * The attributes that are mass assignable.
     * @var array<string>
     */
    protected $fillable = [
        'title'
    ];

    /**
     * Associated poll
     * @return HasMany<PollVote>
     */
    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    /**
     * Returns true if open
     * @return bool
     */
    public function getIsOpenAttribute(): bool
    {
        $now = Date::now();

        // Refuse if not started
        if ($this->started_at === null || $this->started_at > $now) {
            return false;
        }

        // Refuse if ended
        if ($this->ended_at !== null || $this->ended_at <= $now) {
            return false;
        }

        // All good :)
        return true;
    }
}
