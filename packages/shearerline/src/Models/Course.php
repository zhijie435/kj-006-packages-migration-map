<?php

namespace Shearerline\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'teacher_id',
        'cover_image',
        'price',
        'duration',
        'difficulty',
        'category',
        'tags',
        'status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'tags' => 'array',
        'price' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('shearerline.tables.courses', 'shearerline_courses');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(config('shearerline.models.teacher', Teacher::class));
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(config('shearerline.models.lesson', Lesson::class));
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(config('shearerline.models.enrollment', Enrollment::class));
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function getTotalLessonsDurationAttribute(): int
    {
        return $this->lessons()->sum('duration') ?? 0;
    }

    public function getEnrollmentCountAttribute(): int
    {
        return $this->enrollments()->count();
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }
}
