<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Services\CatalogService;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __construct(
        private readonly CatalogService $catalogService,
    ) {}

    public function __invoke(): Response
    {
        $products = $this->catalogService->publishedForSitemap();

        return response()
            ->view('sitemap', compact('products'))
            ->header('Content-Type', 'application/xml');
    }
}
