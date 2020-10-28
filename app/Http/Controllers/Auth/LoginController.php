<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\VerificationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class LoginController extends Controller
{
    private const RESEND_MESSAGE = <<<'TXT'
    Er is binnen de afgelopen 90 seconden al een code gestuurd.
    TXT;

    private const SEND_MESSAGE = <<<'TXT'
    Er is een 8-cijferige code opgestuurd naar het nummer eindigend op %s.
    TXT;

    /**
     * Ensure safety of data
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('throttle:login')->only('find', 'verify', 'retry');
    }

    /**
     * E-mail view
     * @return Response
     */
    public function index(): Response
    {
        return \response()->view('login.index')
            ->setPublic();
    }

    /**
     * Find the e-mail address
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws BindingResolutionException
     */
    public function find(Request $request, VerificationService $service): SymfonyResponse
    {
        // Validate
        $request->validate([
            'email' => ['required', 'email']
        ]);

        // Find a user
        $user = User::where('email', $request->email)->first();

        // Check if a user was found
        if (!$user) {
            $this->sendNotice('Deze gebruiker kon niet worden gevonden');

            return \response()
                ->redirectToRoute('login')
                ->withInput();
        }

        // Check the user
        if (empty($user->phone)) {
            $this->sendNotice('Van deze gebruiker is geen telefoonnummer bekend, je kan dus niet inloggen');

            return \response()
                ->redirectToRoute('login')
                ->withInput();
        }

        // Assign user to session
        $request->session()->put([
            'login-user' => $user,
            'login-expire' => Date::now()->addHour()
        ]);

        // Send the text
        $this->sendCode($service, $user);

        // Forward
            return \response()
                ->redirectToRoute('login.verify');
    }

    /**
     * Ask for a token
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function token(Request $request)
    {
        // Get token from the request
        $user = $this->getUserForToken($request);
        if (!$user) {
            return \response()
                ->redirectToRoute('login');
        }

        // Return the view
        return \response()->view('login.token', [
            'user' => $user
        ]);
    }

    public function verify(Request $request)
    {
        // Get the user
        $user = $this->getUserForToken($request);
        if (!$user) {
            return \response()
                ->redirectToRoute('login');
        }

        $valid = $request->validate([
            'token' => [
                'required',
                'string',
                'regex:/^\d{8}$/'
            ]
        ]);

        // Get the token
        $token = $valid['token'];

        // Validate the token, with a 3-period window (90 seconds)
        if (!$user->totp->verify($token, null, 3)) {
            return \response()
                ->redirectToRoute('login.verify')
                ->with('message', 'De opgegeven code is onjuist');
        }

        // Log in
        Auth::login($user, true);

        // Refresh the session ID
        $request->session()->regenerate();

        // Done
        $this->sendNotice('Je bent nu ingelogd.');

        // Redirect
        return \response()
            ->redirectTo('/');
    }

    /**
     * Sends a new token to the user
     * @param Request $request
     * @param VerificationService $service
     * @return RedirectResponse
     * @throws RuntimeException
     * @throws BindingResolutionException
     */
    public function retry(Request $request, VerificationService $service)
    {
        // Get the user
        $user = $this->getUserForToken($request);
        if (!$user) {
            return \response()
                ->redirectToRoute('login');
        }

        // Re-send the text
        $this->sendCode($service, $user);

        // Forward
        return \response()
            ->redirectToRoute('login.verify')
            ->setPrivate();
    }

    /**
     * Check request
     * @param Request $request
     * @return null|User
     */
    public function getUserForToken(Request $request): ?User
    {
        // Get the user and expire
        $user = $request->session()->get('login-user');
        $expireAt = $request->session()->get('login-expire');

        // Fail if invalid
        if (
            $user === null ||
            $expireAt === null ||
            $expireAt < Date::now()
        ) {
            return null;
        }

        return $user;
    }

    /**
     * Re-sends the code if allowed
     * @param VerificationService $service
     * @param User $user
     * @return void
     */
    private function sendCode(VerificationService $service, User $user): void
    {
        // Generate a new TOTP if it's missing
        if (!$user->totp) {
            $user->generateTotp();
            $user->save();
        }

        $ok = $service->sendMessage($user);
        $message = $ok ? sprintf(self::SEND_MESSAGE, substr($user->phone, -2)) : self::RESEND_MESSAGE;
        $this->sendNotice($message);
    }
}
