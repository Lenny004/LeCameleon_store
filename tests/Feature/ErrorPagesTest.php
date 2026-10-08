<?php

namespace Tests\Feature;

use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    public function test_phase_one_error_views_render(): void
    {
        foreach (['419', '429', '500', '503'] as $code) {
            $html = view("errors.{$code}")->render();

            $this->assertStringContainsString("error-page__code", $html);
            $this->assertStringContainsString($code, $html);
        }
    }
}
