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
    public function index()
    {
        // Return
        return \response()->view('admin.polls.list');
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
}
