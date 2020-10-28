<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class MonitorController extends Controller
{
    /**
     * Ensure auth
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'can:monitor', 'private']);
    }

    /**
     * Display all votes
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        // Get query for the polls we're looking at
        $pollsQuery = Poll::query()
            ->with('votes')
            ->whereBetween('ended_at', [
                Date::now()->subMonths(3),
                Date::now()
            ])
            ->whereNotNull('ended_at')
            ->orderByDesc('ended_at');

        // Get own approvals
        $approvals = PollApproval::where('user_id', $request->user()->id)
            ->whereIn('poll_id', (clone $pollsQuery)->select('id'))
            ->get()
            ->keyBy('poll_id');

        // Get the polls
        $polls = $pollsQuery->get();

        // Render view
        return \response()
            ->view('monitor.list', compact('polls', 'approvals'));
    }
}
