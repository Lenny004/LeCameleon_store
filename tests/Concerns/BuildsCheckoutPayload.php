<?php

namespace Tests\Concerns;

trait BuildsCheckoutPayload
{
    /**
     * @return array<string, mixed>
     */
    protected function checkoutPayload(array $overrides = []): array
    {
        $address = [
            'first_name' => 'Jane',
            'last_name' => 'Vintage',
            'line1' => '123 Market St',
            'line2' => null,
            'city' => 'San Francisco',
            'state' => 'CA',
            'postal_code' => '94102',
            'country' => 'US',
            'phone' => '+1-555-0199',
        ];

        return array_merge([
            'billing_address' => $address,
            'shipping_address' => $address,
            'notes' => null,
        ], $overrides);
    }
}
