<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    public function notice(): View
    {
        return view('store.auth.verify-email');
    }

    public function verify(Request $request, string $id, string $hash): RedirectResponse
    {
        $user = User::query()->findOrFail($id);

        abort_unless(hash_equals(sha1($user->getEmailForVerification()), $hash), 403);

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        if ($request->user()?->is($user)) {
            return redirect()->route('account.profile')->with('success', 'Tu correo fue verificado.');
        }

        return redirect()->route('login')->with('success', 'Tu correo fue verificado. Ya puedes iniciar sesión.');
    }

    public function send(Request $request): RedirectResponse
    {
        if (! $request->user()->hasVerifiedEmail()) {
            $request->user()->sendEmailVerificationNotification();
        }

        return back()->with('success', 'Te enviamos un nuevo enlace de verificación.');
    }
}
