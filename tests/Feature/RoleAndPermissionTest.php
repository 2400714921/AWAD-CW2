<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleAndPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_the_seeder_creates_the_three_roles_from_the_coursework(): void
    {
        $this->assertEqualsCanonicalizing(
            ['administrator', 'lecturer', 'student'],
            Role::pluck('name')->all()
        );

        $this->assertSame(9, Permission::count());
    }

    public function test_each_role_holds_only_the_permissions_of_its_own_job(): void
    {
        $this->assertTrue(Role::findByName('administrator')->hasPermissionTo('manage students'));
        $this->assertFalse(Role::findByName('administrator')->hasPermissionTo('view available courses'));

        $this->assertTrue(Role::findByName('lecturer')->hasPermissionTo('view assigned courses'));
        $this->assertFalse(Role::findByName('lecturer')->hasPermissionTo('manage courses'));

        $this->assertTrue(Role::findByName('student')->hasPermissionTo('register for courses'));
        $this->assertFalse(Role::findByName('student')->hasPermissionTo('manage registrations'));
    }

    public function test_seeding_a_second_time_changes_nothing(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $this->assertSame(3, Role::count());
        $this->assertSame(9, Permission::count());
    }

    public function test_assigning_a_role_grants_the_permissions_it_carries(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->hasPermissionTo('manage students'));

        $user->assignRole('administrator');

        $this->assertTrue($user->fresh()->hasPermissionTo('manage students'));
        $this->assertTrue($user->hasRole('administrator'));
    }

    public function test_a_signed_in_student_is_refused_the_administrator_address(): void
    {
        $this->actingAs($this->userWithRole('student'))
            ->get('/administrator')
            ->assertForbidden();
    }

    public function test_a_lecturer_opens_the_lecturer_address_but_not_the_student_one(): void
    {
        $lecturer = $this->userWithRole('lecturer');

        $this->actingAs($lecturer)->get('/lecturer')->assertOk();
        $this->actingAs($lecturer)->get('/student')->assertForbidden();
    }

    public function test_an_administrator_opens_the_administrator_address(): void
    {
        $this->actingAs($this->userWithRole('administrator'))
            ->get('/administrator')
            ->assertOk();
    }

    public function test_a_guest_is_sent_to_the_login_page(): void
    {
        $this->get('/lecturer')->assertRedirect('/login');
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
