<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Poll;
use App\Models\PollApproval;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Livewire\Component;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class MonitorResult extends Component
{
    use AuthorizesRequests;

    public Poll $poll;
    public ?PollApproval $approval;
    public bool $expand = false;

    public function render()
    {
        return view('livewire.monitor-result', [
            'results' => $this->expand ? $this->poll->calculateResults() : null
        ]);
    }

    /**
     * Set expand
     * @param bool $expand
     * @return void
     */
    public function setExpand(bool $expand): void
    {
        $this->expand = $expand;
    }

    /**
     * Set the result
     * @param Request $request
     * @param string $result
     * @return RedirectResponse
     */
    public function store(Request $request, string $result)
    {
        // check if allowed
        $this->authorize('create', [PollApproval::class, $this->poll]);

        // Validate request
        if (!\array_key_exists($result, PollApproval::RESULTS)) {
            throw new BadRequestHttpException('Result is invalid');
        }

        // Save
        $approval = new PollApproval();
        $approval->user_id = $request->user()->id;
        $approval->poll_id = $this->poll->id;
        $approval->result = $result;
        $approval->save();

        // Redirect
        return \response()
            ->redirectToRoute('monitor.index');
    }
}
