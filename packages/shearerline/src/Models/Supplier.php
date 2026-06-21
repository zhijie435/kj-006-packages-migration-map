<?php

namespace Shearerline\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'contact_name',
        'contact_phone',
        'address',
        'remark',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('shearerline.tables.suppliers', 'shearerline_suppliers');
    }

    public function products(): HasMany
    {
        return $this->hasMany(config('shearerline.models.product', Product::class));
    }

    public function orders(): HasMany
    {
        return $this->hasMany(config('shearerline.models.moq_order', MoqOrder::class));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
