<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function create(): View
    {
        return view('store.auth.forgot-password');
    }

    public function email(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);
        $email = Str::lower(trim($validated['email']));
        $user = User::query()->where('email', $email)->first();

        if ($user?->is_active) {
            // The broker throttles repeated links per account; the response stays neutral either way.
            Password::broker()->sendResetLink(['email' => $email]);
        }

        return back()->with('success', __('passwords.sent'));
    }

    public function show(Request $request, string $token): View
    {
        return view('store.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'confirmed', 'max:255', PasswordRule::defaults()],
            'password_confirmation' => ['required', 'string', 'max:255'],
        ]);

        $status = Password::broker()->reset(
            $validated,
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            },
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', __('passwords.reset'));
        }

        return back()->withErrors(['email' => __($status)])->withInput();
    }
}
