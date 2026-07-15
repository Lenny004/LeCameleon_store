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

    'tax_rate' => (float) env('STORE_TAX_RATE', 0),

    'session_cart_key' => 'cart_session_id',

    'session_wishlist_key' => 'wishlist_session_id',

    'catalog_per_page' => 24,

    'recommendations_limit' => 8,

    // Minutes before unpaid pending order reservations are released.
    'reservation_ttl_minutes' => (int) env('STORE_RESERVATION_TTL_MINUTES', 30),

    // Hours of cart inactivity before sending an abandoned-cart reminder email.
    'abandoned_cart_hours' => (int) env('STORE_ABANDONED_CART_HOURS', 24),

    // Public-disk path used when a product image is missing or not found.
    'product_image_placeholder' => 'placeholders/vintage-product.jpg',

];
