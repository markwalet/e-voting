<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    /**
     * Ensure safety
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'can:admin', 'private']);
    }
}
