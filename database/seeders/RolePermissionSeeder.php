<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * The permissions each role needs to do the job described in the coursework.
     *
     * @var array<string, list<string>>
     */
    private const ROLE_PERMISSIONS = [
        'administrator' => [
            'manage users',
            'manage students',
            'manage courses',
            'manage registrations',
        ],
        'lecturer' => [
            'view assigned courses',
            'view student records',
        ],
        'student' => [
            'view available courses',
            'register for courses',
            'view own registrations',
        ],
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::ROLE_PERMISSIONS as $role => $permissions) {
            $model = Role::findOrCreate($role, 'web');

            foreach ($permissions as $permission) {
                $model->givePermissionTo(Permission::findOrCreate($permission, 'web'));
            }
        }
    }
}
