<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Services\CatalogService;
use Illuminate\Http\Response;

/**
 * XML sitemap for published products and key public pages.
 */
class SitemapController extends Controller
{
    public function __construct(
        private readonly CatalogService $catalogService,
    ) {}

    public function __invoke(): Response
    {
        $products = $this->catalogService->publishedForSitemap();

        // Static marketing and trust pages that should be crawlable.
        $staticPaths = [
            route('home'),
            route('shop.index'),
            route('about'),
            route('faq'),
            route('care'),
            route('shipping'),
            route('returns'),
            route('privacy'),
            route('terms'),
            route('contact.show'),
        ];

        return response()
            ->view('sitemap', [
                'products' => $products,
                'staticUrls' => $staticPaths,
            ])
            ->header('Content-Type', 'application/xml');
    }
}
