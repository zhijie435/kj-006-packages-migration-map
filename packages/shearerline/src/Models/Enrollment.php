<?php

namespace Shearerline\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'student_id',
        'enrolled_at',
        'status',
        'progress',
        'completed_at',
        'remark',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'progress' => 'float',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('shearerline.tables.enrollments', 'shearerline_enrollments');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(config('shearerline.models.course', Course::class));
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(config('shearerline.models.student', Student::class));
    }

    public function scopeEnrolled($query)
    {
        return $query->where('status', 'enrolled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function markAsCompleted(): self
    {
        $this->update([
            'status' => 'completed',
            'progress' => 100,
            'completed_at' => now(),
        ]);
        return $this->fresh();
    }

    public function cancel(): self
    {
        $this->update(['status' => 'cancelled']);
        return $this->fresh();
    }

    public function refund(): self
    {
        $this->update(['status' => 'refunded']);
        return $this->fresh();
    }

    public function updateProgress(float $progress): self
    {
        $progress = max(0, min(100, $progress));
        $this->update(['progress' => $progress]);

        if ($progress >= 100 && $this->status === 'enrolled') {
            $this->markAsCompleted();
        }

        return $this->fresh();
    }

    public function isEnrolled(): bool
    {
        return $this->status === 'enrolled';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    public function getStatusTextAttribute(): string
    {
        return [
            'enrolled' => '已报名',
            'completed' => '已完成',
            'cancelled' => '已取消',
            'refunded' => '已退款',
        ][$this->status] ?? '未知';
    }
}
