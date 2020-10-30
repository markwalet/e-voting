<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VoteController extends Controller
{
    /**
     * Index page, shows a 'welcome' if not logged in
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        // Skip if empty
        if (!$request->user()) {
            return response()->view('welcome');
        }

        return \response()
            ->view('polls')
            ->setPrivate();
    }
}
