<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\ArchivedResultsCast;
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
        'start_count' => 'int',
        'end_count' => 'int',
        'results' => ArchivedResultsCast::class,
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
            ->orderBy('created_at');
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
        if ($this->completed_at !== null) {
            return 'Afgerond';
        }
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
        // Skip if not yet finished
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
            $this->votes
        );
    }

    /**
     * Returns the approval rate of this poll
     * @return null|PollApprovalResults
     */
    public function calculateApproval(): ?PollApprovalResults
    {
        // Skip if not yet finished
        if ($this->started_at === null ||  $this->ended_at === null) {
            return null;
        }

        // Get votes
        $results = $this->approvals()
            ->reorder()
            ->groupBy('result')
            ->select('result', DB::raw('COUNT(*) as count'))
            ->get()
            ->pluck('count', 'result');

        // Map to model
        return new PollApprovalResults(
            $results['pass'] ?? 0,
            $results['reject'] ?? 0,
            $results['neutral'] ?? 0,
            $this->approvals
        );
    }

    /**
     * Check if the request was weird
     * @return bool
     */
    public function getIsWeirdAttribute(): bool
    {
        // Check if still active
        if ($this->started_at === null || $this->ended_at === null) {
            return false;
        }

        // Load vote count
        $this->loadCount('votes');

        // Check some properties
        if (
            $this->start_count != $this->end_count ||
            $this->votes_count > $this->start_count
        ) {
            return true;
        }

        // Check vote times
        $firstVote = $this->votes()->oldest()->first();
        $lastVote = $this->votes()->latest()->first();

        // Warn if outside of window
        if ($firstVote && $firstVote->created_at < $this->started_at) {
            return true;
        }
        if ($lastVote && $lastVote->created_at >= $this->ended_at) {
            return true;
        }

        // No weirdness
        return false;
    }
}
