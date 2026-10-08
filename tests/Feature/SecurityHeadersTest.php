<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_are_present_on_web_response(): void
    {
        $response = $this->get(route('login'));

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
        $this->assertFalse($response->headers->has('Strict-Transport-Security'));
    }

    public function test_hsts_is_present_only_for_https_and_csp_nonce_matches_html(): void
    {
        config()->set('security.csp_enabled', true);
        $response = $this->get('https://localhost/login');
        $response->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        $csp = $response->headers->get('Content-Security-Policy');
        preg_match("/'nonce-([^']+)'/", (string) $csp, $headerMatch);
        preg_match('/nonce="([^"]+)"/', $response->getContent(), $htmlMatch);

        $this->assertNotEmpty($headerMatch[1] ?? null);
        $this->assertSame($headerMatch[1], $htmlMatch[1] ?? null);
    }

    public function test_registration_is_rate_limited_after_ten_requests(): void
    {
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $this->from(route('register'))->post(route('register'), [])->assertSessionHasErrors();
        }

        $this->post(route('register'), [])->assertStatus(429);
    }
}
