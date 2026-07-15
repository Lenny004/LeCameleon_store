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

];
