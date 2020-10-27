<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller as BaseController;

class AdminController extends BaseController
{
    /**
     * Ensure safety
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }
}
