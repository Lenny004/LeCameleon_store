<?php

namespace App\Support;

class PublicUrl
{
    public static function absolute(?string $url): ?string
    {
        if ($url === null || $url === '') {
            return $url;
        }

        return str_starts_with($url, 'http://') || str_starts_with($url, 'https://')
            ? $url
            : url($url);
    }
}
