<?php

namespace Shearerline\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'title',
        'bio',
        'specialties',
        'experience_years',
        'status',
        'remark',
    ];

    protected $casts = [
        'specialties' => 'array',
        'experience_years' => 'integer',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('shearerline.tables.teachers', 'shearerline_teachers');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(config('shearerline.models.course', Course::class));
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function getPublishedCoursesCountAttribute(): int
    {
        return $this->courses()->where('status', 'published')->count();
    }

    public function getTotalStudentsCountAttribute(): int
    {
        return $this->courses()->withCount('enrollments')->get()->sum('enrollments_count');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }
}
