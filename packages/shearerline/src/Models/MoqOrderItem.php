<?php

namespace Shearerline\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoqOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'moq_order_id',
        'product_id',
        'product_name',
        'product_sku',
        'unit_price',
        'quantity',
        'shipped_quantity',
        'subtotal',
        'specs',
        'remark',
    ];

    protected $casts = [
        'specs' => 'array',
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'shipped_quantity' => 'integer',
        'subtotal' => 'decimal:2',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('shearerline.tables.moq_order_items', 'shearerline_moq_order_items');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(config('shearerline.models.moq_order', MoqOrder::class), 'moq_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(config('shearerline.models.product', Product::class));
    }

    public function getIsFullyShippedAttribute(): bool
    {
        return $this->shipped_quantity >= $this->quantity;
    }

    public function getRemainingQuantityAttribute(): int
    {
        return max(0, $this->quantity - $this->shipped_quantity);
    }
}
