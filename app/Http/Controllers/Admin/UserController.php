<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends AdminController
{
    /**
     * Lists a filterable set of users
     * @param Request $request
     * @return Response
     */
    public function index()
    {
        // Get response
        return \response()
            ->view('admin.users.list');
    }

    /**
     * Shows the given user's details
     * @param User $user
     * @return void
     */
    public function show(User $user)
    {
        // Determine proxies
        $proxies = [];
        if ($user->is_voter) {
            $proxies = User::where('can_proxy', '1')
                ->doesntHave('proxyFor')
                ->where('id', '!=', $user->id)
                ->pluck('name', 'id');
        }

        return response()
            ->view('admin.users.show', compact('user', 'proxies'));
    }

    /**
     * Sets or unsets the proxy for this user
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     * @throws InvalidArgumentException
     * @throws BindingResolutionException
     */
    public function setProxy(Request $request, User $user)
    {
        // Validate request
        $valid = $request->validate([
            'action' => ['required', Rule::in('unset', 'set')],
            'user_id' => ['required_if:action,set', 'exists:users,id']
        ]);

        // Get action
        $action = $valid['action'];

        // Check if removing
        if ($action === 'unset') {
            // Remove the proxy
            $user->proxy_id = null;
            $user->save();

            // Report OK
            $this->sendNotice('De machtiging van "%s" is ingetrokken.', $user->name);

            // Redirect back
            return \redirect()->back();
        }

        // Check if the user has rights to proxy
        if (!$user->is_voter) {
            $this->sendNotice('"%s" heeft geen stemrecht, er valt dus niets te machtigen.', $user->name);
            return \redirect()->back();
        }

        // Check if the user is not already authorized
        $proxyUser = User::find($valid['user_id']);
        if ($proxyUser === null) {
            $this->sendNotice('De opgegeven gebruiker kan niet worden gevonden');
            return \redirect()->back();
        }

        // Check if the user has an authorisation
        if ($proxyUser->proxy_id !== null) {
            $this->sendNotice(<<<'TXT'
            De gebruiker "%s" heeft al een machtiging afgegeven aan "%s".
            Machtigingen zijn niet stapelbaar.
            TXT, $proxyUser->name, $proxyUser->proxy->name);
            return \redirect()->back();
        }

        // Check if the to-proxy user has rights to proxy
        if (!$proxyUser->can_proxy) {
            $this->sendNotice('"%s" mag geen machtigingen accepteren.', $proxyUser->name);
            return \redirect()->back();
        }

        // Update
        $user->proxy_id = $proxyUser->id;
        $user->is_monitor = false;
        $user->save();

        // Report and go back
        $this->sendNotice('"%s" is nu gemachtigd om te stemmen namens "%s".', $proxyUser->name, $user->name);
        return \redirect()->back();
    }

    /**
     * Sets if a user is allowed to monitor
     */
    public function setMonitor(Request $request, User $user)
    {
        // Validate request
        $valid = $request->validate([
            'action' => ['required', Rule::in('unset', 'set')]
        ]);

        // Check if possible
        if ($user->is_voter || $user->proxyFor !== null) {
            $this->sendNotice('"%s" is stemgerechtigd, en mag dus niet in de telraad.', $user->name);
            return \redirect()->back();
        }

        // Update
        $user->is_monitor = $valid['action'] === 'set' ? 1 : 0;
        $user->save();

        // Done
        $this->sendNotice(
            '"%s" %s de telraad.',
            $user->name,
            $user->is_monitor ? 'toegevoegd aan' : 'is verwijderd uit'
        );
        return \redirect()->back();
    }
}
