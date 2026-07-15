<?php

namespace App\Services;

use App\Enums\InventoryMovementType;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Inventory movements and quantity updates with audit trail.
 */
class InventoryService
{
    public function stockIn(Product $product, int $quantity, ?User $user = null, ?string $notes = null): InventoryMovement
    {
        return $this->mutate($product, $quantity, InventoryMovementType::StockIn, function (Product $locked) use ($quantity) {
            $locked->quantity_available += $quantity;
        }, $user, $notes);
    }

    public function stockOut(
        Product $product,
        int $quantity,
        ?User $user = null,
        ?string $notes = null,
        bool $fromReservation = false,
        ?string $referenceType = null,
        ?string $referenceId = null,
    ): InventoryMovement {
        return $this->mutate(
            $product,
            $quantity,
            InventoryMovementType::StockOut,
            function (Product $locked) use ($quantity, $fromReservation) {
                if ($locked->quantity_available < $quantity) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Insufficient stock available.',
                    ]);
                }

                $locked->quantity_available -= $quantity;

                if ($fromReservation) {
                    if ($locked->quantity_reserved < $quantity) {
                        throw ValidationException::withMessages([
                            'quantity' => 'Insufficient reserved stock.',
                        ]);
                    }

                    $locked->quantity_reserved -= $quantity;
                }
            },
            $user,
            $notes,
            $referenceType,
            $referenceId,
        );
    }

    public function reserve(
        Product $product,
        int $quantity,
        ?User $user = null,
        ?string $notes = null,
        ?string $referenceType = null,
        ?string $referenceId = null,
    ): InventoryMovement {
        return $this->mutate(
            $product,
            $quantity,
            InventoryMovementType::Reserve,
            function (Product $locked) use ($quantity) {
                $sellable = $locked->quantity_available - $locked->quantity_reserved;

                if ($quantity > $sellable) {
                    throw ValidationException::withMessages([
                        'quantity' => "Only {$sellable} unit(s) available for {$locked->name}.",
                    ]);
                }

                $locked->quantity_reserved += $quantity;
            },
            $user,
            $notes,
            $referenceType,
            $referenceId,
        );
    }

    public function release(
        Product $product,
        int $quantity,
        ?User $user = null,
        ?string $notes = null,
        ?string $referenceType = null,
        ?string $referenceId = null,
    ): InventoryMovement {
        return $this->mutate(
            $product,
            $quantity,
            InventoryMovementType::Release,
            function (Product $locked) use ($quantity) {
                if ($locked->quantity_reserved < $quantity) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Cannot release more than reserved quantity.',
                    ]);
                }

                $locked->quantity_reserved -= $quantity;
            },
            $user,
            $notes,
            $referenceType,
            $referenceId,
        );
    }

    public function adjust(
        Product $product,
        int $newQuantityAvailable,
        ?User $user = null,
        ?string $notes = null,
    ): InventoryMovement {
        return DB::transaction(function () use ($product, $newQuantityAvailable, $user, $notes) {
            $locked = Product::query()->lockForUpdate()->findOrFail($product->id);
            $delta = $newQuantityAvailable - $locked->quantity_available;

            $locked->quantity_available = $newQuantityAvailable;
            $locked->save();

            return InventoryMovement::query()->create([
                'product_id' => $locked->id,
                'user_id' => $user?->id,
                'type' => InventoryMovementType::Adjust,
                'quantity' => $delta,
                'notes' => $notes,
                'created_at' => now(),
            ]);
        });
    }

    private function mutate(
        Product $product,
        int $quantity,
        InventoryMovementType $type,
        callable $mutator,
        ?User $user = null,
        ?string $notes = null,
        ?string $referenceType = null,
        ?string $referenceId = null,
    ): InventoryMovement {
        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Quantity must be greater than zero.',
            ]);
        }

        return DB::transaction(function () use ($product, $quantity, $type, $mutator, $user, $notes, $referenceType, $referenceId) {
            $locked = Product::query()->lockForUpdate()->findOrFail($product->id);

            $mutator($locked);

            $locked->save();

            return InventoryMovement::query()->create([
                'product_id' => $locked->id,
                'user_id' => $user?->id,
                'type' => $type,
                'quantity' => $quantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
                'created_at' => now(),
            ]);
        });
    }
}
