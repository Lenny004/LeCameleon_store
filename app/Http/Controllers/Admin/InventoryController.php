<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InventoryMovementType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InventoryMovementRequest;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function __construct(
        private readonly InventoryService $inventoryService,
    ) {}

    public function index(): View
    {
        return view('admin.inventory.index', [
            'movements' => InventoryMovement::query()
                ->with(['product', 'user'])
                ->orderByDesc('created_at')
                ->paginate(30, ['*'], 'movement_page')
                ->withQueryString(),
            'stockProducts' => Product::query()
                ->with(['brand', 'category'])
                ->orderBy('name')
                ->paginate(20, ['*'], 'stock_page')
                ->withQueryString(),
            'products' => Product::query()->orderBy('name')->get(['id', 'name', 'sku']),
            'totalSkus' => Product::query()->count(),
            'inStock' => Product::query()->whereColumn('quantity_available', '>', 'quantity_reserved')->count(),
            'outOfStock' => Product::query()->whereColumn('quantity_available', '<=', 'quantity_reserved')->count(),
            'lowStockCount' => Product::query()
                ->whereColumn('quantity_available', '>', 'quantity_reserved')
                ->whereRaw('(quantity_available - quantity_reserved) <= low_stock_threshold')
                ->count(),
        ]);
    }

    public function store(InventoryMovementRequest $request): RedirectResponse
    {
        $product = Product::query()->findOrFail($request->validated('product_id'));
        $type = InventoryMovementType::from($request->validated('type'));
        $quantity = (int) $request->validated('quantity');
        $notes = $request->validated('notes');
        $user = $request->user();

        match ($type) {
            InventoryMovementType::StockIn => $this->inventoryService->stockIn($product, $quantity, $user, $notes),
            InventoryMovementType::StockOut => $this->inventoryService->stockOut($product, $quantity, $user, $notes),
            InventoryMovementType::Reserve => $this->inventoryService->reserve($product, $quantity, $user, $notes),
            InventoryMovementType::Release => $this->inventoryService->release($product, $quantity, $user, $notes),
            InventoryMovementType::Adjust => $this->inventoryService->adjust(
                $product,
                (int) $request->validated('new_quantity_available'),
                $user,
                $notes,
            ),
            InventoryMovementType::Return => $this->inventoryService->stockIn($product, $quantity, $user, $notes ?? 'Return'),
        };

        return back()->with('success', 'Movimiento de inventario registrado.');
    }
}
