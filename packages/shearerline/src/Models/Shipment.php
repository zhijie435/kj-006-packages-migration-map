<?php

namespace Shearerline\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_RETURNED = 'returned';
    const STATUS_FAILED = 'failed';

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

    public function order(): BelongsTo
    {
        return $this->belongsTo(config('shearerline.models.moq_order', MoqOrder::class), 'moq_order_id');
    }

    public function getStatusTextAttribute(): string
    {
        $statusMap = [
            self::STATUS_PENDING => '待发货',
            self::STATUS_SHIPPED => '已发货',
            self::STATUS_IN_TRANSIT => '运输中',
            self::STATUS_DELIVERED => '已送达',
            self::STATUS_RETURNED => '已退回',
            self::STATUS_FAILED => '发货失败',
        ];

        return $statusMap[$this->status] ?? $this->status;
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeShipped($query)
    {
        return $query->where('status', self::STATUS_SHIPPED);
    }

    public function scopeInTransit($query)
    {
        return $query->where('status', self::STATUS_IN_TRANSIT);
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', self::STATUS_DELIVERED);
    }

    public function scopeReturned($query)
    {
        return $query->where('status', self::STATUS_RETURNED);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function canUpdateTracking(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_SHIPPED, self::STATUS_IN_TRANSIT]);
    }
}
