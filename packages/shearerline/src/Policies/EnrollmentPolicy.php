<?php

namespace Shearerline\Policies;

use Shearerline\Models\Enrollment;
use Illuminate\Foundation\Auth\User;

class EnrollmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, 'enrollments.view');
    }

    public function view(User $user, Enrollment $enrollment): bool
    {
        return $this->hasPermission($user, 'enrollments.view');
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'enrollments.create');
    }

    public function update(User $user, Enrollment $enrollment): bool
    {
        return $this->hasPermission($user, 'enrollments.update');
    }

    public function delete(User $user, Enrollment $enrollment): bool
    {
        return $this->hasPermission($user, 'enrollments.delete');
    }

    public function cancel(User $user, Enrollment $enrollment): bool
    {
        return $this->hasPermission($user, 'enrollments.cancel');
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
