<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use Illuminate\Http\Request;

class PollController extends Controller
{
    /**
     * List all votes
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        // Get a query
        $query = Poll::query();

        // Hide closed for more than a day by default
        if (!$request->get('all')) {
            $query->where('ended_at', '>', Date::now()->subDay());
        }

        // Prep list
        $polls = $query
            ->orderBy('id', 'desc')
            ->paginate(20);

        // Return
        return \response()->view('admin.polls.list', [
            'polls' => $polls
        ]);
    }

    /**
     * Store the new poll
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate
        $valid = $request->validate([
            'title' => [
                'required',
                'string',
                'min:2',
                'max:250'
            ]
        ]);

        // Make it
        Poll::create($valid);

        // Return
        return \response()->back();
    }

    public function open(Poll $poll)
    {
        // A poll can only be opened once
        if ($poll->started_at !== null) {
            return \redirect()->back();
        }

        // Start the poll
        $poll->started_at = now();
        $poll->save();

        // And return to once ye came
        return \redirect()->back();
    }

    public function close(Poll $poll)
    {
        // A poll can only be closed if it's open to begin with
        if ($poll->started_at === null || $poll->ended_at !== null) {
            return \redirect()->back();
        }

        // Close it immediately
        $poll->ended_at = now();
        $poll->save();

        // Done
        return \redirect()->back();
    }

    public function delete(Poll $poll)
    {
        // Cannot modify started polls
        if ($poll->started_at !== null || $poll->ended_at !== null) {
            return \redirect()->back();
        }

        // Safe to remove, go ahead and do it
        $poll->delete();

        // Redirect back
        return \redirect()->back();
    }
}
