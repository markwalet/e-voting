<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Vote;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Only allow admins and observers
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'can:admin', 'private']);
    }

    /**
     * List all votes
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $votes = Vote::query()->orderBy('created_at', 'desc')->paginate(20);

        return \response()->view('admin.list', [
            'votes' => $votes
        ]);
    }

    public function create(Request $request)
    {
        # code...
    }

    public function store(Request $request)
    {
        # code...
    }

    public function open(Request $request, Vote $vote)
    {
        # code...
    }

    public function close(Request $request, Vote $vote)
    {
        # code...
    }

    public function delete(Request $request, Vote $vote)
    {
        # code...
    }
}
