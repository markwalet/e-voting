<?php

declare(strict_types=1);

namespace App\Http\Controllers;

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
}
