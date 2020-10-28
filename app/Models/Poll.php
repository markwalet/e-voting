<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class Poll extends Model
{
    /**
     * The attributes that should be cast.
     * @var array
     */
    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'completed_at' => 'datetime',
        'results' => 'json',
        'start_count' => 'int',
        'end_count' => 'int',
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
        return $this->hasMany(PollVote::class)
            ->orderBy('created_at');
    }

    /**
     * Approvals by the monitors
     * @return HasMany<PollVote>
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(PollApproval::class)
            ->orderByAsc('created_at');
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
        if ($this->ended_at !== null && $this->ended_at <= $now) {
            return false;
        }

        // All good :)
        return true;
    }

    public function getStatusAttribute(): string
    {
        if ($this->ended_at !== null) {
            return 'Gesloten';
        }
        if ($this->started_at !== null) {
            return 'Open';
        }
        return 'Concept';
    }

    /**
     * Returns alleged poll results
     * @return null|PollResults
     */
    public function calculateResults(): ?PollResults
    {
        if ($this->started_at === null ||  $this->ended_at === null) {
            return null;
        }

        // Get votes
        $results = $this->votes()
            ->reorder()
            ->groupBy('vote')
            ->select('vote', DB::raw('COUNT(*) as count'))
            ->get()
            ->pluck('count', 'vote');

        // Map to model
        return new PollResults(
            $results['favor'] ?? 0,
            $results['against'] ?? 0,
            $results['blank'] ?? 0,
        );
    }
}
