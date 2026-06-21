<?php

namespace Shearerline\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'gender',
        'birth_date',
        'address',
        'status',
        'remark',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('shearerline.tables.students', 'shearerline_students');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(config('shearerline.models.enrollment', Enrollment::class));
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

    public function scopeMale($query)
    {
        return $query->where('gender', 'male');
    }

    public function scopeFemale($query)
    {
        return $query->where('gender', 'female');
    }

    public function getEnrolledCoursesCountAttribute(): int
    {
        return $this->enrollments()->count();
    }

    public function getCompletedCoursesCountAttribute(): int
    {
        return $this->enrollments()->where('status', 'completed')->count();
    }

    public function getAgeAttribute(): ?int
    {
        if (!$this->birth_date) {
            return null;
        }
        return $this->birth_date->age;
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

    public function getGenderTextAttribute(): string
    {
        return [
            'male' => '男',
            'female' => '女',
            'other' => '其他',
        ][$this->gender] ?? '未知';
    }
}
