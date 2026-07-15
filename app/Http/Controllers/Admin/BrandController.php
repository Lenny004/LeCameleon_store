<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function index(): View
    {
        $brands = Brand::query()->orderBy('name')->paginate(20);

        return view('admin.brands.index', compact('brands'));
    }

    public function create(): View
    {
        return view('admin.brands.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:brands,slug'],
            'description' => ['nullable', 'string'],
        ]);

        $brand = Brand::query()->create($data);

        return redirect()->route('admin.brands.show', $brand)->with('success', 'Brand created.');
    }

    public function show(Brand $brand): View
    {
        return view('admin.brands.show', [
            'brand' => $brand->loadCount('products'),
        ]);
    }

    public function edit(Brand $brand): View
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:brands,slug,'.$brand->id],
            'description' => ['nullable', 'string'],
        ]);

        $brand->update($data);

        return redirect()->route('admin.brands.show', $brand)->with('success', 'Brand updated.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        $brand->delete();

        return redirect()->route('admin.brands.index')->with('success', 'Brand deleted.');
    }
}
