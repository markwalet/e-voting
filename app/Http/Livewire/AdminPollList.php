<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Poll;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Livewire\Component;

class AdminPollList extends Component
{
    public string $search = '';
    public string $filter = 'recent';

    /**
     * Returns a paginated collection of polls
     * @return LengthAwarePaginator
     * @throws InvalidArgumentException
     */
    public function getPollsProperty()
    {
        $query = Poll::query()
            ->orderByDesc('id');

        \assert($query instanceof Builder);

        if ($this->filter === 'recent') {
            $query->where('updated_at', '>', Date::now()->subDay());
        }

        if ($this->filter === 'complete') {
            $query->whereNotNull('started_at')
                ->whereNotNull('ended_at')
                ->has('approvals');
        }

        if ($this->filter === 'closed') {
            $query->whereNotNull('started_at')
                ->whereNotNull('ended_at');
        }

        if ($this->filter === 'open') {
            $query->whereNotNull('started_at')
                ->whereNull('ended_at');
        }

        if ($this->filter === 'concepts') {
            $query->whereNull('started_at')
                ->whereNull('ended_at');
        }

        // Add search
        if (!empty($this->search)) {
            $query->where(function ($query) {
                $search = trim($this->search, '%');
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('title', 'like', Str::finish($search, '%'))
                    ->orWhere('title', 'like', Str::start($search, '%'));
            });
        }

        // Get polls
        return $query->paginate(20);
    }

    /**
     * Render
     * @return View|Factory
     * @throws BindingResolutionException
     */
    public function render()
    {
        return view('livewire.admin-poll-list');
    }
}
