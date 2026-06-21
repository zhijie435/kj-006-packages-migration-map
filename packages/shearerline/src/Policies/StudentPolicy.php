<?php

namespace Shearerline\Policies;

use Shearerline\Models\Student;
use Illuminate\Foundation\Auth\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, 'students.view');
    }

    public function view(User $user, Student $student): bool
    {
        return $this->hasPermission($user, 'students.view');
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'students.create');
    }

    public function update(User $user, Student $student): bool
    {
        return $this->hasPermission($user, 'students.update');
    }

    public function delete(User $user, Student $student): bool
    {
        return $this->hasPermission($user, 'students.delete');
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
