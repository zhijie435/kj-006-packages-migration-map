<?php

namespace Shearerline\Traits;

trait HasStatus
{
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

    public function activate(): self
    {
        $this->update(['status' => 'active']);
        return $this->fresh();
    }

    public function deactivate(): self
    {
        $this->update(['status' => 'inactive']);
        return $this->fresh();
    }

    public function suspend(): self
    {
        $this->update(['status' => 'suspended']);
        return $this->fresh();
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
}
