<?php

namespace Shearerline\Policies;

use Shearerline\Models\MoqOrder;
use Illuminate\Foundation\Auth\User;

class MoqOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, 'moq_orders.view');
    }

    public function view(User $user, MoqOrder $order): bool
    {
        if ($this->hasPermission($user, 'moq_orders.view_all')) {
            return true;
        }

        if ($this->hasPermission($user, 'moq_orders.view')) {
            return $this->isOwner($user, $order);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'moq_orders.create');
    }

    public function update(User $user, MoqOrder $order): bool
    {
        if ($this->hasPermission($user, 'moq_orders.update_all')) {
            return true;
        }

        if ($this->hasPermission($user, 'moq_orders.update')) {
            return $this->isOwner($user, $order) && $order->isStatus([
                MoqOrder::STATUS_PENDING,
                MoqOrder::STATUS_CONFIRMED,
            ]);
        }

        return false;
    }

    public function delete(User $user, MoqOrder $order): bool
    {
        return $this->hasPermission($user, 'moq_orders.delete');
    }

    public function confirm(User $user, MoqOrder $order): bool
    {
        if ($this->hasPermission($user, 'moq_orders.confirm_all')) {
            return true;
        }

        if ($this->hasPermission($user, 'moq_orders.confirm')) {
            return $this->isOwner($user, $order);
        }

        return false;
    }

    public function process(User $user, MoqOrder $order): bool
    {
        if ($this->hasPermission($user, 'moq_orders.process_all')) {
            return true;
        }

        if ($this->hasPermission($user, 'moq_orders.process')) {
            return $this->isOwner($user, $order);
        }

        return false;
    }

    public function ship(User $user, MoqOrder $order): bool
    {
        if ($this->hasPermission($user, 'moq_orders.ship_all')) {
            return true;
        }

        if ($this->hasPermission($user, 'moq_orders.ship')) {
            return $this->isOwner($user, $order);
        }

        return false;
    }

    public function complete(User $user, MoqOrder $order): bool
    {
        if ($this->hasPermission($user, 'moq_orders.complete_all')) {
            return true;
        }

        if ($this->hasPermission($user, 'moq_orders.complete')) {
            return $this->isOwner($user, $order);
        }

        return false;
    }

    public function cancel(User $user, MoqOrder $order): bool
    {
        if ($this->hasPermission($user, 'moq_orders.cancel_all')) {
            return true;
        }

        if ($this->hasPermission($user, 'moq_orders.cancel')) {
            return $this->isOwner($user, $order) && $order->isStatus([
                MoqOrder::STATUS_PENDING,
                MoqOrder::STATUS_CONFIRMED,
                MoqOrder::STATUS_PROCESSING,
            ]);
        }

        return false;
    }

    public function refund(User $user, MoqOrder $order): bool
    {
        return $this->hasPermission($user, 'moq_orders.refund');
    }

    public function pay(User $user, MoqOrder $order): bool
    {
        if ($this->hasPermission($user, 'moq_orders.pay_all')) {
            return true;
        }

        if ($this->hasPermission($user, 'moq_orders.pay')) {
            return $this->isOwner($user, $order);
        }

        return false;
    }

    public function viewStatistics(User $user): bool
    {
        return $this->hasPermission($user, 'moq_orders.statistics');
    }

    protected function isOwner(User $user, MoqOrder $order): bool
    {
        return $order->created_by == $user->id;
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
