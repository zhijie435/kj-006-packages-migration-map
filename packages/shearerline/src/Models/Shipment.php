<?php

namespace Shearerline\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Shearerline\Contracts\HasStatusInterface;
use Shearerline\Traits\HasStatus;

class Shipment extends Model implements HasStatusInterface
{
    use HasFactory;
    use HasStatus;

    const STATUS_PENDING = 'pending';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_RETURNED = 'returned';
    const STATUS_FAILED = 'failed';

    protected string $statusConfigKey = 'shipment';

    protected string $defaultStatus = self::STATUS_PENDING;

    protected array $statusTransitions = [
        self::STATUS_PENDING => [
            self::STATUS_SHIPPED,
            self::STATUS_FAILED,
        ],
        self::STATUS_SHIPPED => [
            self::STATUS_IN_TRANSIT,
            self::STATUS_DELIVERED,
            self::STATUS_RETURNED,
            self::STATUS_FAILED,
        ],
        self::STATUS_IN_TRANSIT => [
            self::STATUS_DELIVERED,
            self::STATUS_RETURNED,
            self::STATUS_FAILED,
        ],
        self::STATUS_DELIVERED => [
            self::STATUS_RETURNED,
        ],
        self::STATUS_RETURNED => [],
        self::STATUS_FAILED => [],
    ];

    protected $fillable = [
        'moq_order_id',
        'shipment_no',
        'logistics_company',
        'tracking_no',
        'items',
        'total_quantity',
        'weight',
        'shipping_cost',
        'status',
        'receiver_name',
        'receiver_phone',
        'receiver_address',
        'remark',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'items' => 'array',
        'total_quantity' => 'integer',
        'weight' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('shearerline.tables.shipments', 'shearerline_shipments');
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

    public function order(): BelongsTo
    {
        return $this->belongsTo(config('shearerline.models.moq_order', MoqOrder::class), 'moq_order_id');
    }

    public function scopePending($query)
    {
        return $query->whereStatus(self::STATUS_PENDING);
    }

    public function scopeShipped($query)
    {
        return $query->whereStatus(self::STATUS_SHIPPED);
    }

    public function scopeInTransit($query)
    {
        return $query->whereStatus(self::STATUS_IN_TRANSIT);
    }

    public function scopeDelivered($query)
    {
        return $query->whereStatus(self::STATUS_DELIVERED);
    }

    public function scopeReturned($query)
    {
        return $query->whereStatus(self::STATUS_RETURNED);
    }

    public function scopeFailed($query)
    {
        return $query->whereStatus(self::STATUS_FAILED);
    }

    public function canUpdateTracking(): bool
    {
        return $this->isStatus([
            self::STATUS_PENDING,
            self::STATUS_SHIPPED,
            self::STATUS_IN_TRANSIT,
        ]);
    }

    protected function beforeStatusTransition(string $targetStatus, array $extra = []): void
    {
        if ($targetStatus === self::STATUS_SHIPPED && empty($this->shipped_at)) {
            $this->shipped_at = now();
        }

        if ($targetStatus === self::STATUS_DELIVERED && empty($this->delivered_at)) {
            $this->delivered_at = now();
        }
    }
}
