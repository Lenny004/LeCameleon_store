<?php

namespace App\Services;

use App\Models\Setting;

class WhatsAppLinkService
{
    public function settings(): array
    {
        $value = Setting::getValue('store.whatsapp', []);

        return [
            'number' => preg_replace('/\D+/', '', (string) data_get($value, 'number', '')),
            'message' => (string) data_get($value, 'message', ''),
        ];
    }

    public function link(?string $message = null): ?string
    {
        $settings = $this->settings();
        $number = $settings['number'];

        if ($number === '') {
            return null;
        }

        $text = $message ?? $settings['message'];

        return $this->build($number, $text);
    }

    public function build(string $number, string $message = ''): string
    {
        $digits = preg_replace('/\D+/', '', $number);

        return 'https://wa.me/'.$digits.($message !== '' ? '?text='.rawurlencode($message) : '');
    }

    public function productLink(object $product): ?string
    {
        return $this->link(sprintf(
            'Hola, me interesa esta pieza: %s (%s)',
            $product->name,
            route('shop.show', $product->slug),
        ));
    }
}
