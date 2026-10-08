<?php

namespace App\Http\Controllers\Store;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Support\PublicUrl;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class FeedController extends Controller
{
    public function google(): Response
    {
        $products = $this->products();

        return response()->view('feeds.google', compact('products'), 200, [
            'Content-Type' => 'application/rss+xml; charset=UTF-8',
        ]);
    }

    public function meta(): Response
    {
        $products = $this->products();
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['id', 'title', 'description', 'availability', 'condition', 'price', 'link', 'image_link', 'brand', 'google_product_category', 'item_group_id']);

        foreach ($products as $product) {
            fputcsv($handle, $this->row($product));
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv)->header('Content-Type', 'text/csv; charset=UTF-8');
    }

    /** @return Collection<int, Product> */
    private function products()
    {
        abort_unless((bool) config('store.feeds.enabled', true), 404);
        $token = config('store.feeds.token');
        if (is_string($token) && $token !== '') {
            abort_unless(hash_equals($token, (string) request()->query('token')), 404);
        }

        return Cache::remember('product-feeds:published-in-stock', now()->addMinutes(30), function () {
            return Product::query()
                ->with(['brand', 'images'])
                ->where('status', ProductStatus::Published)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->whereColumn('quantity_available', '>', 'quantity_reserved')
                ->orderBy('id')
                ->get();
        });
    }

    /** @return array<int, string> */
    private function row(Product $product): array
    {
        $image = $product->images->first();
        $description = Str::limit(trim(strip_tags((string) ($product->description ?: $product->short_description ?: $product->name))), 5000, '');

        return [
            (string) $product->id,
            $product->name,
            $description,
            'in stock',
            'used',
            number_format((float) $product->price, 2, '.', '').' USD',
            route('shop.show', $product->slug),
            PublicUrl::absolute($image instanceof ProductImage ? $image->url() : ProductImage::placeholderUrl()),
            $product->brand?->name ?: config('app.name', 'Le Cameleon'),
            '',
            '',
        ];
    }
}
