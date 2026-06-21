<?php

namespace Shearerline\Policies;

use Shearerline\Models\Teacher;
use Illuminate\Foundation\Auth\User;

class TeacherPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, 'teachers.view');
    }

    public function view(User $user, Teacher $teacher): bool
    {
        return $this->hasPermission($user, 'teachers.view');
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'teachers.create');
    }

    public function update(User $user, Teacher $teacher): bool
    {
        return $this->hasPermission($user, 'teachers.update');
    }

    public function delete(User $user, Teacher $teacher): bool
    {
        return $this->hasPermission($user, 'teachers.delete');
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
