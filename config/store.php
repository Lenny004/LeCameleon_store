<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Store defaults
    |--------------------------------------------------------------------------
    */

    'currency' => env('STORE_CURRENCY', 'USD'),

    'order_number_prefix' => env('STORE_ORDER_PREFIX', 'LC'),

    'shipping_flat_rate' => (float) env('STORE_SHIPPING_FLAT_RATE', 9.99),

    // Origin municipality for A→B shipping quotes (sv_municipalities.id).
    'warehouse_municipality_id' => env('STORE_WAREHOUSE_MUNICIPALITY_ID') !== null
        ? (int) env('STORE_WAREHOUSE_MUNICIPALITY_ID')
        : null,

    // Fallback distance when municipality coordinates are missing.
    'default_shipping_distance_km' => (float) env('STORE_DEFAULT_SHIPPING_DISTANCE_KM', 10),

    // Default ETA when zone rate does not define estimated_hours.
    'default_shipping_eta_hours' => (int) env('STORE_DEFAULT_SHIPPING_ETA_HOURS', 48),

    'tax_rate' => (float) env('STORE_TAX_RATE', 0),

    'session_cart_key' => 'cart_session_id',

    'session_wishlist_key' => 'wishlist_session_id',

    'catalog_per_page' => 24,

    'recommendations_limit' => 8,

    // Minutes before unpaid pending order reservations are released.
    'reservation_ttl_minutes' => (int) env('STORE_RESERVATION_TTL_MINUTES', 30),

    // Payment providers whose pending reservations use the short online TTL.
    'online_payment_providers' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('STORE_ONLINE_PAYMENT_PROVIDERS', 'stripe')),
    ), static fn (string $provider): bool => $provider !== '')),

    // Manual-payment reservation lifetime. Zero disables automatic cancellation.
    'manual_payment_ttl_hours' => (int) env('STORE_MANUAL_PAYMENT_TTL_HOURS', 72),

    'offer_acceptance_hours' => (int) env('STORE_OFFER_ACCEPTANCE_HOURS', 48),

    // Hours of cart inactivity before sending an abandoned-cart reminder email.
    'abandoned_cart_hours' => (int) env('STORE_ABANDONED_CART_HOURS', 24),

    // Public-disk path used when a product image is missing or not found.
    'product_image_placeholder' => 'placeholders/vintage-product.jpg',

    'product_thumbnail_max_side' => (int) env('PRODUCT_THUMBNAIL_MAX_SIDE', 400),

    'feeds' => [
        'enabled' => (bool) env('PRODUCT_FEEDS_ENABLED', true),
        'token' => env('PRODUCT_FEEDS_TOKEN'),
    ],

    // Static brand logos from legacy/LeCameleon/recursos/img (via LegacyPublicAssetsSeeder).
    // compact (logo_icon) → narrow; horizontal (logo) → header; wide (logo3) → hero/large.
    'brand_logo' => 'images/brand/logo.png',
    'brand_logo_icon' => 'images/brand/logo-icon.png',
    'brand_logo_wide' => 'images/brand/logo-wide.png',
    'hero_image' => 'images/marketing/decoracion-vintage.jpg',
    'collection_banner_image' => 'images/marketing/banner-collection.jpg',

];
