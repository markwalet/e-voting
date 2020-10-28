<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        // Log out
        Auth::logout();

        // Refresh session
        $request->session()->regenerate();

        // Redirect
        $this->sendNotice('Je bent uitgelogd');
        return \response()->redirectTo('/');
    }
}
