<?php

namespace App\Http\Controllers\Store;

use App\Enums\UserRole;
use App\Http\Controllers\Concerns\ResolvesStoreSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\Store\LoginRequest;
use App\Http\Requests\Store\RegisterRequest;
use App\Models\User;
use App\Services\CartService;
use App\Services\WishlistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    use ResolvesStoreSession;

    public function __construct(
        private readonly CartService $cartService,
        private readonly WishlistService $wishlistService,
    ) {}

    public function showLogin(): View
    {
        return view('store.auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        $this->mergeGuestData($request);

        return redirect()->intended(route('home'));
    }

    public function showRegister(): View
    {
        return view('store.auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = User::query()->create([
            ...$request->validated(),
            'role' => UserRole::Customer,
            'is_active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        $this->mergeGuestData($request);

        return redirect()->route('home')->with('success', 'Welcome to Le Cameleon.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function mergeGuestData(Request $request): void
    {
        $user = $request->user();

        if (! $user) {
            return;
        }

        $cartSessionId = $this->sessionId($request, 'session_cart_key');
        if ($cartSessionId) {
            $guestCart = $this->cartService->resolveCart(null, $cartSessionId);
            if ($guestCart->items()->exists()) {
                $this->cartService->mergeGuestCartIntoUser($guestCart, $user);
            }
        }

        $wishlistSessionId = $this->sessionId($request, 'session_wishlist_key');
        if ($wishlistSessionId) {
            $guestWishlist = $this->wishlistService->resolveWishlist(null, $wishlistSessionId);
            if ($guestWishlist->items()->exists()) {
                $this->wishlistService->mergeGuestIntoUser($guestWishlist, $user);
            }
        }
    }
}
