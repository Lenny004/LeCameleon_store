<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CouponType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CouponRequest;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(): View
    {
        $coupons = Coupon::query()->orderByDesc('created_at')->paginate(20);

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create(): View
    {
        return view('admin.coupons.create', [
            'types' => CouponType::cases(),
            'categories' => Category::query()->orderBy('name')->get(),
            'products' => Product::query()->where('status', 'published')->orderBy('name')->get(['id', 'name', 'sku']),
        ]);
    }

    public function store(CouponRequest $request): RedirectResponse
    {
        $data = $this->validated($request);
        $coupon = Coupon::query()->create(collect($data)->except(['category_ids', 'product_ids'])->all());
        $coupon->categories()->sync($data['category_ids'] ?? []);
        $coupon->products()->sync($data['product_ids'] ?? []);

        return redirect()->route('admin.coupons.show', $coupon)->with('success', 'Cupón creado.');
    }

    public function show(Coupon $coupon): View
    {
        return view('admin.coupons.show', compact('coupon'));
    }

    public function edit(Coupon $coupon): View
    {
        return view('admin.coupons.edit', [
            'coupon' => $coupon->load(['categories', 'products']),
            'types' => CouponType::cases(),
            'categories' => Category::query()->orderBy('name')->get(),
            'products' => Product::query()->where('status', 'published')->orderBy('name')->get(['id', 'name', 'sku']),
        ]);
    }

    public function update(CouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $data = $request->validated();
        $data['code'] = strtoupper($data['code']);
        $data['is_active'] = $request->boolean('is_active', $coupon->is_active);
        $data['shipping_only'] = $request->boolean('shipping_only', $coupon->shipping_only);
        $coupon->forceFill(collect($data)->except(['category_ids', 'product_ids'])->all())->save();
        $coupon->categories()->sync($data['category_ids'] ?? []);
        $coupon->products()->sync($data['product_ids'] ?? []);

        return redirect()->route('admin.coupons.show', $coupon)->with('success', 'Cupón actualizado.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Cupón eliminado.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(CouponRequest $request): array
    {
        $data = $request->validated();

        $data['code'] = strtoupper($data['code']);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['shipping_only'] = $request->boolean('shipping_only', false);

        return $data;
    }
}
