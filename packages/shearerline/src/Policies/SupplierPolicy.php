<?php

namespace Shearerline\Policies;

use Shearerline\Models\Supplier;
use Illuminate\Foundation\Auth\User;

class SupplierPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, 'suppliers.view');
    }

    public function view(User $user, Supplier $supplier): bool
    {
        return $this->hasPermission($user, 'suppliers.view');
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'suppliers.create');
    }

    public function update(User $user, Supplier $supplier): bool
    {
        return $this->hasPermission($user, 'suppliers.update');
    }

    public function delete(User $user, Supplier $supplier): bool
    {
        return $this->hasPermission($user, 'suppliers.delete');
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
