<?php

namespace Shearerline\Policies;

use Shearerline\Models\Lesson;
use Illuminate\Foundation\Auth\User;

class LessonPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, 'lessons.view');
    }

    public function view(User $user, Lesson $lesson): bool
    {
        return $this->hasPermission($user, 'lessons.view');
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'lessons.create');
    }

    public function update(User $user, Lesson $lesson): bool
    {
        return $this->hasPermission($user, 'lessons.update');
    }

    public function delete(User $user, Lesson $lesson): bool
    {
        return $this->hasPermission($user, 'lessons.delete');
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
