<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PollApproval extends Model
{
    use HasFactory;

    public const RESULTS = [
        'pass' => 'Goedgekeurd',
        'reject' => 'Afgekeurd',
        'neutral' => 'Geen uitspraak'
    ];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getResultNameAttribute(): string
    {
        return self::RESULTS[$this->result] ?? $this->result;
    }
}
