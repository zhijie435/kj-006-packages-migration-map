<?php

namespace Shearerline\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MoqOrder extends Model
{
    use HasFactory;
    use SoftDeletes;

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'order_no',
        'supplier_id',
        'customer_name',
        'customer_phone',
        'customer_address',
        'customer_remark',
        'total_amount',
        'paid_amount',
        'total_quantity',
        'shipped_quantity',
        'status',
        'payment_method',
        'paid_at',
        'confirmed_at',
        'processed_at',
        'shipped_at',
        'completed_at',
        'cancelled_at',
        'refunded_at',
        'cancelled_reason',
        'refunded_reason',
        'internal_remark',
        'created_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'total_quantity' => 'integer',
        'shipped_quantity' => 'integer',
        'paid_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'processed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('shearerline.tables.moq_orders', 'shearerline_moq_orders');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(config('shearerline.models.supplier', Supplier::class));
    }

    public function items(): HasMany
    {
        return $this->hasMany(config('shearerline.models.moq_order_item', MoqOrderItem::class));
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(config('shearerline.models.shipment', Shipment::class));
    }

    public function getIsFullyShippedAttribute(): bool
    {
        return $this->shipped_quantity >= $this->total_quantity && $this->total_quantity > 0;
    }

    public function getIsPartiallyShippedAttribute(): bool
    {
        return $this->shipped_quantity > 0 && $this->shipped_quantity < $this->total_quantity;
    }

    public function getStatusTextAttribute(): string
    {
        $statusMap = [
            self::STATUS_PENDING => '待确认',
            self::STATUS_CONFIRMED => '已确认',
            self::STATUS_PROCESSING => '处理中',
            self::STATUS_SHIPPED => '已发货',
            self::STATUS_COMPLETED => '已完成',
            self::STATUS_CANCELLED => '已取消',
            self::STATUS_REFUNDED => '已退款',
        ];

        return $statusMap[$this->status] ?? $this->status;
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    public function scopeShipped($query)
    {
        return $query->where('status', self::STATUS_SHIPPED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', self::STATUS_REFUNDED);
    }

    public function canConfirm(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canProcess(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function canShip(): bool
    {
        return in_array($this->status, [self::STATUS_CONFIRMED, self::STATUS_PROCESSING]);
    }

    public function canCancel(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED, self::STATUS_PROCESSING]);
    }

    public function canRefund(): bool
    {
        return in_array($this->status, [self::STATUS_CONFIRMED, self::STATUS_PROCESSING, self::STATUS_SHIPPED, self::STATUS_COMPLETED]);
    }

    public function canComplete(): bool
    {
        return $this->status === self::STATUS_SHIPPED && $this->is_fully_shipped;
    }
}
