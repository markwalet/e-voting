<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\MessageBirdService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('throttle:login')->only('find', 'verify');
    }

    public function index(Request $request)
    {
        return \response()->view('login.user')
            ->setPublic();
    }

    public function find(Request $request, MessageBirdService $textService)
    {
        // Validate
        $request->validate([
            'email' => ['required', 'email']
        ]);

        // Find a user
        $user = User::where('email', $request->email)->first();

        // Check if a user was found
        if (!$user) {
            return \response()
                ->redirectToRoute('login')
                ->withInput()
                ->with('message', 'Deze gebruiker kon niet worden gevonden');
        }

        return \response()->view('login.token')
            ->setPrivate();
    }

    public function verify(Request $request)
    {
        # code...
    }

    public function retry(Request $request)
    {
        # code...
    }
}
