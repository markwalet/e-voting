<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\Poll;
use Illuminate\Http\Request;

class PollController extends AdminController
{
    /**
     * List all votes
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        // Get only param
        $only = $request->get('only');

        // Get users
        $query = Poll::query()
            ->orderByDesc('ended_at')
            ->orderByDesc('started_at')
            ->orderBy('id');

        // Add filters
        if ($only === 'concept') {
            $query
                ->whereNull('started_at')
                ->whereNull('ended_at');
        } elseif ($only === 'opened') {
            $query
                ->whereNotNull('started_at')
                ->whereNull('ended_at');
        } elseif ($only === 'closed') {
            $query
                ->whereNotNull('started_at')
                ->whereNotNull('ended_at');
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

        // Set
        $this->sendNotice('De nieuwe peiling is aangemaakt.');

        // Return
        return \redirect()
            ->back();
    }

    /**
     * Stats the poll, if possible
     */
    public function open(Poll $poll)
    {
        // A poll can only be opened once
        if ($poll->started_at !== null) {
            $this->sendNotice('De stemming "%s" is al eens geopend.', $poll->title);
            return \redirect()->back();
        }

        // Start the poll
        $poll->started_at = now();
        $poll->save();

        // Message
        $this->sendNotice('De stemming "%s" is geopend.', $poll->title);

        // And return to once ye came
        return \redirect()->back();
    }

    /**
     * Closes the given poll, if possible
     * @param Poll $poll
     * @return RedirectResponse
     */
    public function close(Poll $poll)
    {
        // A poll can only be closed if it's open to begin with
        if ($poll->started_at === null || $poll->ended_at !== null) {
            $this->sendNotice('De stemming "%s" is niet gestart of al gesloten.', $poll->title);
            return \redirect()->back();
        }

        // Close it immediately
        $poll->ended_at = now();
        $poll->save();

        // Message
        $this->sendNotice('De stemming "%s" is gesloten.', $poll->title);

        // Done
        return \redirect()->back();
    }

    /**
     * Remove the given unstarted poll
     * @param Poll $poll
     * @return RedirectResponse
     */
    public function delete(Poll $poll)
    {
        // Cannot modify started polls
        if ($poll->started_at !== null || $poll->ended_at !== null) {
            $this->sendNotice('De stemming "%s" is eens geopend, en kan niet meer worden verwijderd.', $poll->title);
            return \redirect()->back();
        }

        // Safe to remove, go ahead and do it
        $poll->delete();

        // Report OK
        $this->sendNotice('De stemming "%s" is verwiijderd.', $poll->title);

        // Redirect back
        return \redirect()->back();
    }
}
