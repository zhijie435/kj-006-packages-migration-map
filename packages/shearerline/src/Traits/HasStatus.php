<?php

namespace Shearerline\Traits;

use Shearerline\Exceptions\InvalidStatusTransitionException;

trait HasStatus
{
    public function getStatus(): string
    {
        return $this->{$this->getStatusColumn()};
    }

    public function setStatus(string $status): self
    {
        $this->{$this->getStatusColumn()} = $status;
        return $this;
    }

    public function getStatusColumn(): string
    {
        return $this->statusColumn ?? 'status';
    }

    public function getStatusConfigKey(): string
    {
        return $this->statusConfigKey ?? '';
    }

    public function getDefaultStatus(): string
    {
        return $this->defaultStatus ?? '';
    }

    public function getStatusTransitions(): array
    {
        return $this->statusTransitions ?? [];
    }

    public function getStatusLabels(): array
    {
        $configKey = $this->getStatusConfigKey();

        if ($configKey && config()->has("shearerline.status.{$configKey}")) {
            return config("shearerline.status.{$configKey}");
        }

        return [];
    }

    public function getStatusLabel(): string
    {
        $labels = $this->getStatusLabels();
        return $labels[$this->getStatus()] ?? $this->getStatus();
    }

    public function getAllStatuses(): array
    {
        return array_keys($this->getStatusLabels());
    }

    public function canTransitionTo(string $targetStatus): bool
    {
        $currentStatus = $this->getStatus();
        $transitions = $this->getStatusTransitions();

        if (isset($transitions[$currentStatus])) {
            return in_array($targetStatus, (array) $transitions[$currentStatus], true);
        }

        return false;
    }

    public function getAvailableTransitions(): array
    {
        $currentStatus = $this->getStatus();
        $transitions = $this->getStatusTransitions();

        return $transitions[$currentStatus] ?? [];
    }

    public function transitionTo(string $targetStatus, array $extra = []): self
    {
        if (!$this->canTransitionTo($targetStatus)) {
            throw new InvalidStatusTransitionException(
                $this->getStatus(),
                $targetStatus
            );
        }

        $this->beforeStatusTransition($targetStatus, $extra);

        $this->setStatus($targetStatus);

        $this->afterStatusTransition($targetStatus, $extra);

        return $this;
    }

    protected function beforeStatusTransition(string $targetStatus, array $extra = []): void
    {
    }

    protected function afterStatusTransition(string $targetStatus, array $extra = []): void
    {
    }

    public function isStatus(string|array $status): bool
    {
        if (is_array($status)) {
            return in_array($this->getStatus(), $status, true);
        }

        return $this->getStatus() === $status;
    }

    public function scopeWhereStatus($query, string|array $status)
    {
        if (is_array($status)) {
            return $query->whereIn($this->getStatusColumn(), $status);
        }

        return $query->where($this->getStatusColumn(), $status);
    }

    public function scopeWhereNotStatus($query, string|array $status)
    {
        if (is_array($status)) {
            return $query->whereNotIn($this->getStatusColumn(), $status);
        }

        return $query->where($this->getStatusColumn(), '!=', $status);
    }

    public function getStatusTextAttribute(): string
    {
        return $this->getStatusLabel();
    }
}
