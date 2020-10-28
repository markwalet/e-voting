<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class AdminUser extends Component
{
    use AuthorizesRequests;

    public User $user;
    public bool $expanded = false;

    public function render()
    {
        $proxyUsers = [];
        if ($this->expanded) {
            $proxyUsers = User::query()
                ->with(['proxyFor', 'proxy'])
                ->where('can_proxy', '1')
                ->get();
        }

        return view('livewire.admin-user', compact('proxyUsers'));
    }

    /**
     * Marks a user as present
     * @param App\Http\Livewire\Request $request
     * @param bool $present
     * @return void
     */
    public function setPresent(bool $present)
    {
        $this->authorize('setPresent', $this->user);

        $this->user->is_present = $present;
        $this->user->save();
    }

    /**
     * Marks a user as present
     * @param App\Http\Livewire\Request $request
     * @param bool $monitor
     * @return void
     */
    public function setMonitor(bool $monitor)
    {
        $this->authorize('setMonitor', $this->user);

        $this->user->is_monitor = $monitor;
        $this->user->save();
    }

    public function setShow(bool $show): void
    {
        $this->expanded = $show;
    }
}
