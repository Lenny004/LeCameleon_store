<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Customer order with immutable address snapshots.
 */
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'number',
        'status',
        'currency',
        'subtotal',
        'discount_total',
        'shipping_total',
        'tax_total',
        'grand_total',
        'coupon_code',
        'billing_address',
        'shipping_address',
        'notes',
        'placed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'subtotal' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'shipping_total' => 'decimal:2',
            'tax_total' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'billing_address' => 'array',
            'shipping_address' => 'array',
            'placed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    /**
     * Fulfillment steps with completed flags for the current status.
     *
     * @return array<int, array{status: string, label: string, completed: bool, current: bool}>
     */
    public function statusTimeline(): array
    {
        $flow = [
            OrderStatus::Pending,
            OrderStatus::Paid,
            OrderStatus::Processing,
            OrderStatus::Shipped,
            OrderStatus::Delivered,
        ];

        $isTerminal = in_array($this->status, [OrderStatus::Cancelled, OrderStatus::Refunded], true);
        $currentIndex = $isTerminal ? false : array_search($this->status, $flow, true);

        $timeline = [];

        foreach ($flow as $index => $status) {
            $timeline[] = [
                'status' => $status->value,
                'label' => ucfirst($status->value),
                'completed' => $currentIndex !== false && $index <= $currentIndex,
                'current' => $this->status === $status,
            ];
        }

        if ($isTerminal) {
            $timeline[] = [
                'status' => $this->status->value,
                'label' => ucfirst($this->status->value),
                'completed' => true,
                'current' => true,
            ];
        }

        return $timeline;
    }

    /**
     * Customer email from shipping snapshot or linked user account.
     */
    public function customerEmail(): ?string
    {
        $shippingEmail = $this->shipping_address['email'] ?? null;

        if (is_string($shippingEmail) && $shippingEmail !== '') {
            return $shippingEmail;
        }

        return $this->user?->email;
    }
}
