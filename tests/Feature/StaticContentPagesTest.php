<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Smoke tests for static trust and policy pages.
 */
class StaticContentPagesTest extends TestCase
{
    public function test_care_page_is_reachable(): void
    {
        $this->get(route('care'))->assertOk()->assertSee('Cuidado de piezas vintage', false);
    }

    public function test_shipping_page_is_reachable(): void
    {
        $this->get(route('shipping'))->assertOk()->assertSee('Envíos', false);
    }

    public function test_privacy_page_is_reachable(): void
    {
        $this->get(route('privacy'))->assertOk()->assertSee('Política de privacidad', false);
    }

    public function test_terms_page_is_reachable(): void
    {
        $this->get(route('terms'))->assertOk()->assertSee('Términos de uso', false);
    }
}
