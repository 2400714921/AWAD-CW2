<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertTrue(User::where('email', 'test@example.com')->firstOrFail()->hasRole('student'));
    }

    public function test_a_self_registered_account_is_taken_straight_to_the_student_desk(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->get('/dashboard')->assertOk()->assertSee('My studies');
        $this->get('/courses')->assertOk()->assertSee('Courses you have not registered for yet');

        $this->get('/administrator')->assertForbidden();
    }

    public function test_a_self_registered_account_is_told_it_has_no_student_record_yet(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->get('/registrations/create')
            ->assertRedirect()
            ->assertSessionHas('error', 'This account is not linked to a student record yet, so there is nothing to register.');
    }
}
