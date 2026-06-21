<?php

namespace Shearerline\Contracts;

interface HasStatusInterface
{
    public function getStatus(): string;

    public function setStatus(string $status): self;

    public function canTransitionTo(string $targetStatus): bool;

    public function transitionTo(string $targetStatus, array $extra = []): self;

    public function getAllStatuses(): array;

    public function getStatusLabels(): array;

    public function getStatusLabel(): string;

    public function getAvailableTransitions(): array;
}
