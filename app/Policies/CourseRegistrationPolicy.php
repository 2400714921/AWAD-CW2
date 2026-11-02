<?php

namespace App\Policies;

use App\Models\CourseRegistration;
use App\Models\User;

class CourseRegistrationPolicy
{
    /**
     * The list itself is filtered per role in the controller, so seeing one row is what needs deciding.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['manage registrations', 'view own registrations', 'view assigned courses']);
    }

    public function view(User $user, CourseRegistration $courseRegistration): bool
    {
        return $user->hasPermissionTo('manage registrations')
            || $courseRegistration->student?->user_id === $user->id
            || $courseRegistration->course?->lecturer?->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyPermission(['manage registrations', 'register for courses']);
    }

    /**
     * Only an administrator edits a registration; a student may only cancel their own.
     */
    public function update(User $user, CourseRegistration $courseRegistration): bool
    {
        return $user->hasPermissionTo('manage registrations')
            || $courseRegistration->student?->user_id === $user->id;
    }

    public function delete(User $user, CourseRegistration $courseRegistration): bool
    {
        return $this->update($user, $courseRegistration);
    }
}
