<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LayoutAndNavigationTest extends TestCase
{
    use RefreshDatabase;

    private const ADMINISTRATOR = 'registrar@makerere.ac.ug';

    private const LECTURER = 'g.asingya@makerere.ac.ug';

    private const STUDENT = '2400709637@student.makerere.ac.ug';

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_the_layout_rolls_out_a_header_a_navigation_and_a_content_area(): void
    {
        $administrator = $this->account(self::ADMINISTRATOR);

        foreach (['/dashboard', '/students', '/courses', '/registrations', '/profile'] as $address) {
            $this->actingAs($administrator)
                ->get($address)
                ->assertOk()
                ->assertSee('Overview')
                ->assertSee($administrator->name);
        }
    }

    public function test_the_content_section_of_the_layout_carries_the_page_it_wraps(): void
    {
        $this->actingAs($this->account(self::ADMINISTRATOR))
            ->get('/students')
            ->assertSee('2400709637')
            ->assertSee('Nansikombi Kirsten');

        $this->actingAs($this->account(self::ADMINISTRATOR))
            ->get('/courses')
            ->assertSee('BUC3131')
            ->assertSee('Advanced Web Application Development');
    }

    public function test_each_role_lands_on_a_desk_named_after_it(): void
    {
        $this->actingAs($this->account(self::ADMINISTRATOR))->get('/administrator')->assertSee('Administrator desk');
        $this->actingAs($this->account(self::LECTURER))->get('/lecturer')->assertSee('Lecturer desk');
        $this->actingAs($this->account(self::STUDENT))->get('/student')->assertSee('My studies');
    }

    public function test_the_desk_counts_only_the_figures_the_signed_in_role_may_read(): void
    {
        $this->actingAs($this->account(self::ADMINISTRATOR))
            ->get('/dashboard')
            ->assertSee('Students on the register')
            ->assertSee('Signed-in accounts')
            ->assertSee('8');

        $this->actingAs($this->account(self::LECTURER))
            ->get('/dashboard')
            ->assertSee('Courses you teach')
            ->assertDontSee('Signed-in accounts');

        $this->actingAs($this->account(self::STUDENT))
            ->get('/dashboard')
            ->assertSee('Your registrations')
            ->assertSee('Courses still open to you')
            ->assertDontSee('Students on the register');
    }

    public function test_the_navigation_shows_each_role_only_the_addresses_it_holds_the_permission_for(): void
    {
        $this->actingAs($this->account(self::ADMINISTRATOR))
            ->get('/dashboard')
            ->assertSee('Administrator desk')
            ->assertSee('Students')
            ->assertSee('Courses')
            ->assertSee('Registrations')
            ->assertDontSee('My studies')
            ->assertDontSee('Lecturer desk');

        $this->actingAs($this->account(self::LECTURER))
            ->get('/dashboard')
            ->assertSee('Lecturer desk')
            ->assertSee('Students')
            ->assertDontSee('Administrator desk')
            ->assertDontSee('My studies')
            ->assertDontSee('Register for a course');

        $this->actingAs($this->account(self::STUDENT))
            ->get('/dashboard')
            ->assertSee('My studies')
            ->assertSee('Register for a course')
            ->assertDontSee('Administrator desk')
            ->assertDontSee('Lecturer desk');
    }

    public function test_the_desk_lists_only_the_actions_the_role_is_allowed_to_take(): void
    {
        $this->actingAs($this->account(self::ADMINISTRATOR))
            ->get('/dashboard')
            ->assertSee('New student record')
            ->assertSee('New course');

        $this->actingAs($this->account(self::LECTURER))
            ->get('/dashboard')
            ->assertDontSee('New student record')
            ->assertDontSee('New course');

        $this->actingAs($this->account(self::STUDENT))
            ->get('/dashboard')
            ->assertSee('Register for a course')
            ->assertDontSee('New course');
    }

    public function test_the_same_permission_aware_list_fills_the_sidebar_and_the_mobile_drawer(): void
    {
        $page = $this->actingAs($this->account(self::ADMINISTRATOR))->get('/dashboard');

        // The desk label also appears as the page heading, so only the link is counted: one in the
        // sidebar, one in the drawer.
        $this->assertSame(2, substr_count($page->getContent(), 'href="'.route('administrator').'"'));
        $this->assertSame(0, substr_count($page->getContent(), 'href="'.route('student').'"'));

        $studentPage = $this->actingAs($this->account(self::STUDENT))->get('/dashboard');

        $this->assertSame(2, substr_count($studentPage->getContent(), 'href="'.route('student').'"'));
        $this->assertSame(0, substr_count($studentPage->getContent(), 'href="'.route('administrator').'"'));
    }

    public function test_a_guest_page_carries_neither_the_navigation_nor_the_signed_in_account_block(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertDontSee('Signed in as')
            ->assertDontSee('Registrations');
    }

    public function test_an_account_that_has_not_been_given_a_role_yet_still_reaches_a_desk(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('My studies')
            ->assertSee('No student record linked yet')
            ->assertDontSee('New course');

        $this->actingAs(User::factory()->create())
            ->get('/profile')
            ->assertOk();
    }

    private function account(string $email): User
    {
        return User::where('email', $email)->firstOrFail();
    }
}
