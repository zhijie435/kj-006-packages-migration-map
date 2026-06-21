<?php

namespace Shearerline\Policies;

use Shearerline\Models\Shipment;
use Illuminate\Foundation\Auth\User;

class ShipmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, 'shipments.view');
    }

    public function view(User $user, Shipment $shipment): bool
    {
        return $this->hasPermission($user, 'shipments.view');
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'shipments.create');
    }

    public function update(User $user, Shipment $shipment): bool
    {
        return $this->hasPermission($user, 'shipments.update');
    }

    public function delete(User $user, Shipment $shipment): bool
    {
        return $this->hasPermission($user, 'shipments.delete');
    }

    public function updateTracking(User $user, Shipment $shipment): bool
    {
        return $this->hasPermission($user, 'shipments.update_tracking');
    }

    protected function hasPermission(User $user, string $permission): bool
    {
        if (method_exists($user, 'hasPermission')) {
            return $user->hasPermission($permission);
        }

        if (method_exists($user, 'can')) {
            return $user->can($permission);
        }

        if (method_exists($user, 'isAdmin')) {
            return $user->isAdmin();
        }

        return true;
    }
}
