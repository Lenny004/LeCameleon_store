<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
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
        $publishedConstraint = static function ($query): void {
            $query->where('status', 'published')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now());
        };

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
            route('size-guide'),
        ];

        return response()
            ->view('sitemap', [
                'products' => $products,
                'staticUrls' => $staticPaths,
                'categories' => Category::query()->whereHas('products', $publishedConstraint)->get(['slug', 'updated_at']),
                'brands' => Brand::query()->whereHas('products', $publishedConstraint)->get(['slug', 'updated_at']),
            ])
            ->header('Content-Type', 'application/xml');
    }
}
