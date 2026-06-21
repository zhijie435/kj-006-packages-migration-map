<?php

namespace Shearerline\Policies;

use Shearerline\Models\Product;
use Illuminate\Foundation\Auth\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, 'products.view');
    }

    public function view(User $user, Product $product): bool
    {
        return $this->hasPermission($user, 'products.view');
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'products.create');
    }

    public function update(User $user, Product $product): bool
    {
        return $this->hasPermission($user, 'products.update');
    }

    public function delete(User $user, Product $product): bool
    {
        return $this->hasPermission($user, 'products.delete');
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
