<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Support\RecordsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with(['brand', 'category'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'brands' => Brand::query()->orderBy('name')->get(),
            'categories' => Category::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $product = Product::query()->create($this->productPayload($request));

        $this->storeUploadedImages($product, $request->file('images', []));

        RecordsActivity::log('product.created', $product);

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Product created.');
    }

    public function show(Product $product): View
    {
        return view('admin.products.show', [
            'product' => $product->load(['brand', 'category', 'images', 'attributes']),
        ]);
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'product' => $product->load('images'),
            'brands' => Brand::query()->orderBy('name')->get(),
            'categories' => Category::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($this->productPayload($request, $product));

        if ($request->boolean('manage_images')) {
            $this->syncExistingImages($product, $request->input('keep_image_ids', []));
        }

        $this->storeUploadedImages($product, $request->file('images', []));

        RecordsActivity::log('product.updated', $product);

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);

        RecordsActivity::log('product.deleted', $product, [
            'name' => $product->name,
            'slug' => $product->slug,
        ]);

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted.');
    }

    /**
     * Build validated product attributes, including provenance audit fields.
     *
     * @return array<string, mixed>
     */
    private function productPayload(ProductRequest $request, ?Product $product = null): array
    {
        $data = $request->validated();

        if ($request->boolean('is_authenticated')) {
            $data['authenticated_at'] = $product?->authenticated_at ?? now();
            $data['authenticated_by'] = $product?->authenticated_by ?? $request->user()?->id;
        } else {
            $data['authenticated_at'] = null;
            $data['authenticated_by'] = null;
            $data['authenticity_notes'] = null;
        }

        return $data;
    }

    /**
     * @param  list<UploadedFile>  $images
     */
    private function storeUploadedImages(Product $product, array $images): void
    {
        if ($images === []) {
            return;
        }

        $existingCount = $product->images()->count();
        $hasPrimary = $product->images()->where('is_primary', true)->exists();

        foreach ($images as $index => $image) {
            if (! $image instanceof UploadedFile) {
                continue;
            }

            $path = $image->store("products/{$product->id}", 'public');

            $this->maybeCreateGdThumbnail($path);

            $product->images()->create([
                'path' => $path,
                'alt' => $product->name,
                'sort_order' => $existingCount + $index,
                'is_primary' => ! $hasPrimary && $index === 0,
            ]);
        }
    }

    /**
     * @param  list<int|string>  $keepImageIds
     */
    private function syncExistingImages(Product $product, array $keepImageIds): void
    {
        $keepIds = collect($keepImageIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();

        $imagesToRemove = $product->images()
            ->when($keepIds->isNotEmpty(), fn ($query) => $query->whereNotIn('id', $keepIds))
            ->when($keepIds->isEmpty(), fn ($query) => $query)
            ->get();

        foreach ($imagesToRemove as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }

        if ($keepIds->isNotEmpty() && ! $product->images()->where('is_primary', true)->exists()) {
            ProductImage::query()
                ->where('product_id', $product->id)
                ->whereIn('id', $keepIds)
                ->orderBy('sort_order')
                ->first()
                ?->update(['is_primary' => true]);
        }
    }

    /**
     * Optional native GD thumbnail alongside the original (not stored in DB).
     */
    private function maybeCreateGdThumbnail(string $storedPath): void
    {
        if (! extension_loaded('gd')) {
            return;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($storedPath)) {
            return;
        }

        $fullPath = $disk->path($storedPath);
        $info = @getimagesize($fullPath);

        if ($info === false) {
            return;
        }

        [$width, $height, $type] = $info;

        $source = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($fullPath),
            IMAGETYPE_PNG => @imagecreatefrompng($fullPath),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($fullPath) : false,
            default => false,
        };

        if ($source === false) {
            return;
        }

        $max = 400;
        $ratio = min($max / max($width, 1), $max / max($height, 1), 1);
        $newWidth = max(1, (int) round($width * $ratio));
        $newHeight = max(1, (int) round($height * $ratio));

        $thumb = imagecreatetruecolor($newWidth, $newHeight);

        if ($thumb === false) {
            imagedestroy($source);

            return;
        }

        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $pathInfo = pathinfo($storedPath);
        $thumbRelative = ($pathInfo['dirname'] ?? '.').'/'.($pathInfo['filename'] ?? 'image').'_thumb.'.($pathInfo['extension'] ?? 'jpg');
        $thumbFull = $disk->path($thumbRelative);

        $saved = match ($type) {
            IMAGETYPE_JPEG => imagejpeg($thumb, $thumbFull, 85),
            IMAGETYPE_PNG => imagepng($thumb, $thumbFull),
            IMAGETYPE_WEBP => function_exists('imagewebp') ? imagewebp($thumb, $thumbFull, 85) : false,
            default => false,
        };

        imagedestroy($source);
        imagedestroy($thumb);

        if ($saved === false) {
            @unlink($thumbFull);
        }
    }
}
