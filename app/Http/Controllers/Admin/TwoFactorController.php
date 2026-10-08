<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function setup(Request $request): View
    {
        $user = $request->user();

        if ($user->two_factor_confirmed_at) {
            return view('admin.two-factor.setup', ['qrCode' => null]);
        }

        if (! $user->two_factor_secret) {
            $user->forceFill([
                'two_factor_secret' => app(Google2FA::class)->generateSecretKey(),
            ])->save();
        }

        $google = app(Google2FA::class);
        $uri = $google->getQRCodeUrl(config('app.name', 'Le Cameleon'), $user->email, $user->two_factor_secret);
        $renderer = new ImageRenderer(new RendererStyle(220), new SvgImageBackEnd);
        $qrCode = (new Writer($renderer))->writeString($uri);

        return view('admin.two-factor.setup', compact('qrCode'));
    }

    public function confirm(Request $request): RedirectResponse
    {
        $data = $request->validate(['code' => ['required', 'digits:6']]);
        $user = $request->user();

        if (! $user->two_factor_secret || ! app(Google2FA::class)->verifyKey($user->two_factor_secret, $data['code'])) {
            return back()->withErrors(['code' => 'El código no es válido.']);
        }

        $codes = collect(range(1, 8))
            ->map(fn (): string => strtoupper(bin2hex(random_bytes(5))))
            ->all();

        $user->forceFill([
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => $codes,
        ])->save();
        $request->session()->put('two_factor_passed', true);

        return back()
            ->with('recovery_codes', $codes)
            ->with('success', 'La autenticación de dos factores quedó activada. Guarda tus códigos de recuperación.');
    }

    public function regenerate(Request $request): RedirectResponse
    {
        $this->checkPassword($request);
        $codes = collect(range(1, 8))
            ->map(fn (): string => strtoupper(bin2hex(random_bytes(5))))
            ->all();

        $request->user()->forceFill(['two_factor_recovery_codes' => $codes])->save();

        return back()
            ->with('recovery_codes', $codes)
            ->with('success', 'Códigos de recuperación regenerados.');
    }

    public function disable(Request $request): RedirectResponse
    {
        $this->checkPassword($request);
        $request->user()->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();
        $request->session()->forget('two_factor_passed');

        return back()->with('success', 'La autenticación de dos factores fue desactivada.');
    }

    public function challenge(): View
    {
        return view('admin.two-factor.challenge');
    }

    public function verifyChallenge(Request $request): RedirectResponse
    {
        $data = $request->validate(['code' => ['required', 'string', 'max:20']]);
        $user = $request->user();
        $code = strtoupper(trim($data['code']));
        $valid = $user->two_factor_secret
            && preg_match('/^\d{6}$/', $code)
            && app(Google2FA::class)->verifyKey($user->two_factor_secret, $code);

        if (! $valid) {
            $recovery = collect($user->two_factor_recovery_codes ?? []);
            $match = $recovery->search(fn ($item): bool => hash_equals((string) $item, $code));

            if ($match !== false) {
                $recovery->forget($match);
                $user->forceFill([
                    'two_factor_recovery_codes' => $recovery->values()->all(),
                ])->save();
                $valid = true;
            }
        }

        if (! $valid) {
            return back()->withErrors(['code' => 'El código no es válido.']);
        }

        $request->session()->regenerate();
        $request->session()->put('two_factor_passed', true);

        return redirect()->intended(route('admin.dashboard'));
    }

    private function checkPassword(Request $request): void
    {
        $request->validate(['current_password' => ['required', 'current_password']]);
    }
}
