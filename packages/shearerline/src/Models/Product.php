<?php

namespace Shearerline\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'name',
        'sku',
        'description',
        'price',
        'moq',
        'stock',
        'unit',
        'image_url',
        'specs',
        'is_active',
    ];

    protected $casts = [
        'specs' => 'array',
        'price' => 'decimal:2',
        'moq' => 'integer',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('shearerline.tables.products', 'shearerline_products');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(config('shearerline.models.supplier', Supplier::class));
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(config('shearerline.models.moq_order_item', MoqOrderItem::class));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function hasEnoughStock(int $quantity): bool
    {
        return $this->stock >= $quantity;
    }

    public function meetsMoq(int $quantity): bool
    {
        return $quantity >= $this->moq;
    }
}
