<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    /**
     * Every role reaches the register because the controller narrows the list to the rows that
     * role owns. Opening one record is decided by view(), which a student may only do for their own.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['manage students', 'view student records', 'view own registrations']);
    }

    public function view(User $user, Student $student): bool
    {
        return $user->hasAnyPermission(['manage students', 'view student records'])
            || $user->id === $student->user_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage students');
    }

    public function update(User $user, Student $student): bool
    {
        return $user->hasPermissionTo('manage students');
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->hasPermissionTo('manage students');
    }
}
