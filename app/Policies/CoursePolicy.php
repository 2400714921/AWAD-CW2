<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Every role has a course list, so the difference is which rows each one may open.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Course $course): bool
    {
        return $user->hasAnyPermission(['manage courses', 'view available courses'])
            || $course->lecturer?->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage courses');
    }

    public function update(User $user, Course $course): bool
    {
        return $user->hasPermissionTo('manage courses');
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->hasPermissionTo('manage courses');
    }
}
