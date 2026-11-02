<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    /**
     * An administrator keeps the records, a lecturer reads them, and a student reads only their own.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['manage students', 'view student records']);
    }

    public function view(User $user, Student $student): bool
    {
        return $this->viewAny($user) || $user->id === $student->user_id;
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
