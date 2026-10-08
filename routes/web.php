<?php

use App\Http\Controllers\Admin\TwoFactorController;
use App\Http\Controllers\Store\AboutController;
use App\Http\Controllers\Store\AccountController;
use App\Http\Controllers\Store\AddressController;
use App\Http\Controllers\Store\AuthController;
use App\Http\Controllers\Store\CareGuideController;
use App\Http\Controllers\Store\CartController;
use App\Http\Controllers\Store\CheckoutController;
use App\Http\Controllers\Store\ContactController;
use App\Http\Controllers\Store\EmailVerificationController;
use App\Http\Controllers\Store\FaqController;
use App\Http\Controllers\Store\FeedController;
use App\Http\Controllers\Store\HomeController;
use App\Http\Controllers\Store\NewsletterController;
use App\Http\Controllers\Store\OfferController;
use App\Http\Controllers\Store\PasswordResetController;
use App\Http\Controllers\Store\PaymentReceiptController;
use App\Http\Controllers\Store\PrivacyController;
use App\Http\Controllers\Store\ReturnsController;
use App\Http\Controllers\Store\ReviewController;
use App\Http\Controllers\Store\SavedSearchController;
use App\Http\Controllers\Store\ShippingInfoController;
use App\Http\Controllers\Store\ShippingQuoteController;
use App\Http\Controllers\Store\ShopController;
use App\Http\Controllers\Store\SitemapController;
use App\Http\Controllers\Store\SizeGuideController;
use App\Http\Controllers\Store\StockAlertController;
use App\Http\Controllers\Store\TermsController;
use App\Http\Controllers\Store\TrackingController;
use App\Http\Controllers\Store\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/guia-de-tallas', SizeGuideController::class)->name('size-guide');
Route::get('/feeds/google.xml', [FeedController::class, 'google'])->middleware('throttle:30,1')->name('feeds.google');
Route::get('/feeds/meta.csv', [FeedController::class, 'meta'])->middleware('throttle:30,1')->name('feeds.meta');

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::post('/shop/{product}/stock-alert', [StockAlertController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('shop.stock-alert');
Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shop.show');
Route::get('/search', [ShopController::class, 'search'])->name('search');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');

Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::delete('/wishlist/{wishlistItem}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:10,1');
    Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'email'])
        ->middleware('throttle:5,1')
        ->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'show'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])
        ->middleware('throttle:5,1')
        ->name('password.update');
});

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware(['auth', 'active'])
    ->name('logout');

Route::middleware(['auth', 'active', 'verified', 'role:admin|staff'])->group(function () {
    Route::get('/two-factor-challenge', [TwoFactorController::class, 'challenge'])->name('two-factor.challenge');
    Route::post('/two-factor-challenge', [TwoFactorController::class, 'verifyChallenge'])->middleware('throttle:5,1')->name('two-factor.challenge.verify');
});

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])
    ->middleware('throttle:20,1')
    ->name('checkout.store');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/pedido/{order}/comprobante', [PaymentReceiptController::class, 'create'])
    ->middleware(['signed', 'throttle:12,1'])
    ->name('checkout.receipts.create');
Route::post('/checkout/success/{order}/receipts', [PaymentReceiptController::class, 'store'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('checkout.receipts.store');

Route::get('/about', AboutController::class)->name('about');
Route::get('/faq', FaqController::class)->name('faq');
Route::get('/care', CareGuideController::class)->name('care');
Route::get('/shipping', ShippingInfoController::class)->name('shipping');
Route::get('/shipping/quote', [ShippingQuoteController::class, 'index'])->name('shipping.quote');
Route::get('/shipping/quote/calculate', [ShippingQuoteController::class, 'calculate'])
    ->middleware('throttle:60,1')
    ->name('shipping.quote.calculate');

Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
Route::post('/tracking', [TrackingController::class, 'lookup'])
    ->middleware('throttle:30,1')
    ->name('tracking.lookup');
Route::get('/tracking/{code}', [TrackingController::class, 'show'])
    ->middleware('throttle:30,1')
    ->where('code', '[A-Za-z0-9\-]+')
    ->name('tracking.show');

Route::post('/newsletter', [NewsletterController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('newsletter.store');
Route::get('/newsletter/confirm/{token}', [NewsletterController::class, 'confirm'])->name('newsletter.confirm');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
Route::post('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe.post');
Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('contact.store');

Route::get('/returns', ReturnsController::class)->name('returns');
Route::get('/privacy', PrivacyController::class)->name('privacy');
Route::get('/terms', TermsController::class)->name('terms');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

Route::middleware(['auth', 'active', 'verified'])->group(function () {
    Route::post('/shop/{product}/reviews', [ReviewController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('shop.reviews.store');

    Route::post('/shop/{product}/offers', [OfferController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('shop.offers.store');

    Route::get('/account', [AccountController::class, 'profile'])->name('account.index');
    Route::get('/account/profile', [AccountController::class, 'profile'])->name('account.profile');
    Route::put('/account/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::put('/account/password', [AccountController::class, 'updatePassword'])
        ->middleware('throttle:6,1')
        ->name('account.password.update');
    Route::get('/account/orders', [AccountController::class, 'orders'])->name('account.orders.index');
    Route::get('/account/orders/{order}', [AccountController::class, 'showOrder'])->name('account.orders.show');
    Route::post('/account/orders/{order}/payment-receipts', [PaymentReceiptController::class, 'store'])->middleware('throttle:6,1')->name('account.orders.receipts.store');
    Route::post('/account/orders/{order}/returns', [AccountController::class, 'storeReturn'])->name('account.orders.returns.store');
    Route::get('/account/offers', [AccountController::class, 'offers'])->name('account.offers.index');
    Route::patch('/account/offers/{offer}/accept', [AccountController::class, 'acceptOffer'])->middleware('throttle:10,1')->name('account.offers.accept');
    Route::patch('/account/offers/{offer}/decline', [AccountController::class, 'declineOffer'])->middleware('throttle:10,1')->name('account.offers.decline');
    Route::post('/account/offers/{offer}/cart', [AccountController::class, 'addOfferToCart'])->middleware('throttle:10,1')->name('account.offers.cart');
    Route::get('/account/addresses', [AddressController::class, 'index'])->name('account.addresses.index');
    Route::get('/account/addresses/create', [AddressController::class, 'create'])->name('account.addresses.create');
    Route::post('/account/addresses', [AddressController::class, 'store'])->name('account.addresses.store');
    Route::get('/account/addresses/{address}/edit', [AddressController::class, 'edit'])->name('account.addresses.edit');
    Route::put('/account/addresses/{address}', [AddressController::class, 'update'])->name('account.addresses.update');
    Route::delete('/account/addresses/{address}', [AddressController::class, 'destroy'])->name('account.addresses.destroy');
    Route::patch('/account/addresses/{address}/default', [AddressController::class, 'makeDefault'])->name('account.addresses.default');
    Route::get('/account/saved-searches', [SavedSearchController::class, 'index'])->name('account.saved-searches.index');
    Route::post('/account/saved-searches', [SavedSearchController::class, 'store'])->name('account.saved-searches.store');
    Route::patch('/account/saved-searches/{savedSearch}/notify', [SavedSearchController::class, 'toggleNotify'])->name('account.saved-searches.notify');
    Route::delete('/account/saved-searches/{savedSearch}', [SavedSearchController::class, 'destroy'])->name('account.saved-searches.destroy');
});
