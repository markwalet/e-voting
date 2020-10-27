<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class ProxyController extends Controller
{
    public function list(Request $request)
    {
        $proxyUsers = User::whereNotNull('proxy_id')
            ->orderBy('name');

        $freeUsers = User::whereNotIn('id', $proxyUsers)
            ->whereNull('proxy_id')
            ->orderBy('name');

        return \response()->view('admin.auths.list', [
            'proxiedUsers' => $proxyUsers->paginate(20),
            'freeUsers' => $freeUsers->all()
        ]);
    }

    public function add(Request $request, User $user)
    {
        # code...
    }

    public function remove(Request $request, User $user)
    {
        $user->authorises = null;
        $user->save();
    }
}
