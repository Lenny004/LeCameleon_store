<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
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
        $product = Product::query()->create($request->validated());

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
            'product' => $product,
            'brands' => Brand::query()->orderBy('name')->get(),
            'categories' => Category::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted.');
    }
}
