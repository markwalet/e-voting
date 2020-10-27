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
    public function list(Request $request)
    {
        // Get only param
        $only = $request->get('only');

        // Get users
        $query = User::query()
            ->orderBy('name');

        // Add filters
        if ($only === 'proxy') {
            $query->has('proxy');
        } elseif ($only === 'is-proxy') {
            $query->has('proxyFor');
        } elseif ($only === 'present') {
            $query->where('is_present', '1');
        }

        // Get response
        return \response()
            ->view('admin.users.list', [
                'users' => $query->paginate(50)
            ]);
    }

    /**
     * Shows the given user's details
     * @param User $user
     * @return void
     */
    public function show(User $user)
    {
        return response()
            ->view('admin.users.show', compact('user'));
    }

    /**
     * Marks the user as present or absent
     * @param Request $request
     * @param User $user
     * @return void
     */
    public function markPresent(Request $request, User $user)
    {
        // Check
        $valid = $request->validate([
            'present' => ['required', Rule::in(['yes', 'no'])]
        ]);

        // Save, removing the monitor role if no longer present
        $user->is_present = $valid['present'] === 'yes';
        $user->is_monitor = $user->is_monitor && $user->is_present;
        $user->save();

        // Notify and go back
        $this->sendNotice(
            'De gebruiker "%s" is nu gemarkeerd als "%s"',
            $user->name,
            $user->is_present ? 'aanwezig' : 'afwezig'
        );
        return \redirect()->back();
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
            'type' => ['required', Rule::in('unset', 'set')]
        ]);

        // Check if possible
        if ($user->is_voter || $user->proxyFor !== null) {
            $this->sendNotice('"%s" is stemgerechtigd, en mag dus niet in de telraad.', $user->name);
            return \redirect()->back();
        }

        // Update
        $user->is_monitor = $valid['type'] === 'set';
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
