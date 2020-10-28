<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Livewire\Component;

class AdminUserList extends Component
{
    public string $search = '';
    public string $filter = 'can-vote-present';

    protected $queryString = [
        'search' => ['except' => ''],
        'filter' => ['except' => 'can-vote-present'],
    ];

    /**
     * Returns a collection of users for the given filters and
     * search query
     * @return LengthAwarePaginator<User>
     */
    public function getUsersProperty(): LengthAwarePaginator
    {
        // Get users
        $query = User::query()
            ->with('proxy', 'proxyFor')
            ->orderBy('name');

        // Skip filters if searching
        if ($this->search) {
            $query->where(function ($query) {
                $search = trim($this->search, '%');
                $query->where('name', 'like', "%{$search}%")
                ->orWhere('name', 'like', Str::finish($search, '%'))
                ->orWhere('name', 'like', Str::start($search, '%'));
            });

            // Return a subset
            return $query->take(20)->paginate(20);
        }

        // Add filters
        if ($this->filter === 'proxy') {
            $query->has('proxy');
        } elseif ($this->filter === 'is-proxy') {
            $query->has('proxyFor');
        } elseif ($this->filter === 'present') {
            $query->where('is_present', true);
        } elseif ($this->filter !== 'all') {
            $query->hasVoteRights();

            // Constrain to present
            if ($this->filter === 'can-vote-present') {
                $query->where('is_present', '1');
            }
        }

        // Return paginated
        return $query->paginate(100);
    }

    /**
     * Render the livewire list
     * @return View|Factory
     */
    public function render()
    {
        return view('livewire.admin-user-list', [
            'scores' => User::getEligibleUsers()
        ]);
    }
}
