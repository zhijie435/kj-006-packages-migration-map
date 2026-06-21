<?php

namespace Shearerline\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Shearerline\Contracts\HasStatusInterface;
use Shearerline\Traits\HasStatus;

class MoqOrder extends Model implements HasStatusInterface
{
    use HasFactory;
    use SoftDeletes;
    use HasStatus;

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    protected string $statusConfigKey = 'moq_order';

    protected string $defaultStatus = self::STATUS_PENDING;

    protected array $statusTransitions = [
        self::STATUS_PENDING => [
            self::STATUS_CONFIRMED,
            self::STATUS_CANCELLED,
        ],
        self::STATUS_CONFIRMED => [
            self::STATUS_PROCESSING,
            self::STATUS_SHIPPED,
            self::STATUS_CANCELLED,
            self::STATUS_REFUNDED,
        ],
        self::STATUS_PROCESSING => [
            self::STATUS_SHIPPED,
            self::STATUS_CANCELLED,
            self::STATUS_REFUNDED,
        ],
        self::STATUS_SHIPPED => [
            self::STATUS_COMPLETED,
            self::STATUS_REFUNDED,
        ],
        self::STATUS_COMPLETED => [
            self::STATUS_REFUNDED,
        ],
        self::STATUS_CANCELLED => [],
        self::STATUS_REFUNDED => [],
    ];

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

    protected static function booted(): void
    {
        parent::booted();

        static::creating(function ($model) {
            if (empty($model->status)) {
                $model->status = $model->getDefaultStatus();
            }
        });
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

    public function scopePending($query)
    {
        return $query->whereStatus(self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->whereStatus(self::STATUS_CONFIRMED);
    }

    public function scopeProcessing($query)
    {
        return $query->whereStatus(self::STATUS_PROCESSING);
    }

    public function scopeShipped($query)
    {
        return $query->whereStatus(self::STATUS_SHIPPED);
    }

    public function scopeCompleted($query)
    {
        return $query->whereStatus(self::STATUS_COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->whereStatus(self::STATUS_CANCELLED);
    }

    public function scopeRefunded($query)
    {
        return $query->whereStatus(self::STATUS_REFUNDED);
    }

    public function canConfirm(): bool
    {
        return $this->canTransitionTo(self::STATUS_CONFIRMED);
    }

    public function canProcess(): bool
    {
        return $this->canTransitionTo(self::STATUS_PROCESSING);
    }

    public function canShip(): bool
    {
        return $this->canTransitionTo(self::STATUS_SHIPPED);
    }

    public function canCancel(): bool
    {
        return $this->canTransitionTo(self::STATUS_CANCELLED);
    }

    public function canRefund(): bool
    {
        return $this->canTransitionTo(self::STATUS_REFUNDED);
    }

    public function canComplete(): bool
    {
        return $this->canTransitionTo(self::STATUS_COMPLETED) && $this->is_fully_shipped;
    }

    public function canPay(): bool
    {
        return $this->paid_amount <= 0
            && $this->isStatus([self::STATUS_PENDING, self::STATUS_CONFIRMED, self::STATUS_PROCESSING]);
    }

    public function isStatus(string|array $status): bool
    {
        if (is_array($status)) {
            return in_array($this->getStatus(), $status, true);
        }

        return parent::isStatus($status);
    }

    protected function beforeStatusTransition(string $targetStatus, array $extra = []): void
    {
        $timestampFields = [
            self::STATUS_CONFIRMED => 'confirmed_at',
            self::STATUS_PROCESSING => 'processed_at',
            self::STATUS_SHIPPED => 'shipped_at',
            self::STATUS_COMPLETED => 'completed_at',
            self::STATUS_CANCELLED => 'cancelled_at',
            self::STATUS_REFUNDED => 'refunded_at',
        ];

        if (isset($timestampFields[$targetStatus]) && empty($this->{$timestampFields[$targetStatus]})) {
            $this->{$timestampFields[$targetStatus]} = now();
        }
    }
}
