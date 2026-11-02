<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Database\Factories\StudentFactory;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DemoDataSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FactoriesAndSeedersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_the_seeder_supplies_the_volume_of_records_the_coursework_asks_for(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertGreaterThanOrEqual(5, Student::count());
        $this->assertGreaterThanOrEqual(4, Course::count());
        $this->assertGreaterThanOrEqual(1, Lecturer::count());
        $this->assertGreaterThan(0, CourseRegistration::count());
    }

    public function test_every_seeded_account_carries_the_role_that_decides_what_it_may_see(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(1, User::role('administrator')->count());
        $this->assertSame(3, User::role('lecturer')->count());
        $this->assertSame(8, User::role('student')->count());

        $this->assertTrue(User::role('administrator')->firstOrFail()->hasPermissionTo('manage students'));
        $this->assertTrue(User::role('lecturer')->firstOrFail()->hasPermissionTo('view assigned courses'));
        $this->assertTrue(User::role('student')->firstOrFail()->hasPermissionTo('register for courses'));
    }

    public function test_a_seeded_account_signs_in_and_links_to_the_record_it_belongs_to(): void
    {
        $this->seed(DatabaseSeeder::class);

        $student = Student::where('student_number', '2400709637')->firstOrFail();

        $this->assertTrue(Hash::check(DemoDataSeeder::PASSWORD, $student->user->password));
        $this->assertSame('Nansikombi Kirsten', $student->full_name);
        $this->assertSame($student->email, $student->user->email);
        $this->assertNotNull($student->user->email_verified_at);

        $this->actingAs($student->user)->get('/registrations')->assertOk();
    }

    public function test_the_seeded_registrations_reach_both_sides_of_the_relationship(): void
    {
        $this->seed(DatabaseSeeder::class);

        $student = Student::where('student_number', '2400709637')->firstOrFail();
        $course = Course::where('course_code', 'BUC3131')->firstOrFail();

        $this->assertTrue($student->courses->contains($course));
        $this->assertTrue($course->students->contains($student));
        $this->assertSame('registered', $student->courses->first()->pivot->status);
        $this->assertNotNull($student->courses->first()->pivot->registered_at);
        $this->assertTrue($course->lecturer->user->hasRole('lecturer'));
    }

    public function test_seeding_a_second_time_leaves_the_same_number_of_rows(): void
    {
        $this->seed(DatabaseSeeder::class);
        $before = $this->snapshot();

        $this->seed(DatabaseSeeder::class);

        $this->assertSame($before, $this->snapshot());
    }

    public function test_no_student_is_registered_for_the_same_course_twice(): void
    {
        $this->seed(DatabaseSeeder::class);

        $duplicates = DB::table('course_registrations')
            ->select('student_id', 'course_id')
            ->groupBy('student_id', 'course_id')
            ->havingRaw('count(*) > 1')
            ->get();

        $pairs = DB::table('course_registrations')
            ->get(['student_id', 'course_id'])
            ->map(fn (object $row) => $row->student_id.'-'.$row->course_id);

        $this->assertCount(0, $duplicates);
        $this->assertSame($pairs->count(), $pairs->unique()->count());
    }

    public function test_the_factories_build_records_the_constraints_accept(): void
    {
        $student = Student::factory()->create();
        $course = Course::factory()->create();
        $registration = CourseRegistration::factory()->create([
            'student_id' => $student->id,
            'course_id' => $course->id,
        ]);

        $this->assertMatchesRegularExpression('/^\d{10}$/', $student->student_number);
        $this->assertContains($student->programme, StudentFactory::PROGRAMMES);
        $this->assertNotNull($student->date_of_birth);
        $this->assertTrue($course->lecturer->exists);
        $this->assertSame('registered', $registration->status);
    }

    public function test_the_student_factory_links_a_signable_account_only_when_asked(): void
    {
        $withoutAccount = Student::factory()->create();
        $withAccount = Student::factory()->withAccount()->create();

        $this->assertNull($withoutAccount->user_id);
        $this->assertTrue($withAccount->user->hasRole('student'));
        $this->assertTrue($withAccount->user->can('view', $withAccount));
    }

    public function test_the_lecturer_and_course_factories_can_be_stacked_on_one_another(): void
    {
        $lecturer = Lecturer::factory()->withAccount()->create();
        $course = Course::factory()->taughtBy($lecturer)->create();

        $this->assertTrue($lecturer->user->hasRole('lecturer'));
        $this->assertSame($lecturer->id, $course->lecturer_id);
        $this->assertTrue($lecturer->courses->contains($course));
    }

    public function test_bulk_factory_creation_keeps_the_unique_columns_unique(): void
    {
        $students = Student::factory()->count(20)->create();
        $lecturers = Lecturer::factory()->count(10)->create();
        $courses = Course::factory()->count(8)->create();

        $this->assertSame(20, $students->pluck('student_number')->unique()->count());
        $this->assertSame(20, $students->pluck('email')->unique()->count());
        $this->assertSame(10, $lecturers->pluck('staff_number')->unique()->count());
        $this->assertSame(8, $courses->pluck('course_code')->unique()->count());
    }

    public function test_a_registration_factory_builds_its_own_student_and_course_when_given_neither(): void
    {
        $registration = CourseRegistration::factory()->create();

        $this->assertTrue($registration->student->exists);
        $this->assertTrue($registration->course->exists);
        $this->assertSame($registration->course->id, $registration->student->courses->sole()->id);
    }

    public function test_the_cancelled_state_records_the_status_the_register_keeps(): void
    {
        $registration = CourseRegistration::factory()->cancelled()->create();

        $this->assertSame('cancelled', $registration->status);
    }

    public function test_the_roles_and_permissions_themselves_come_from_their_own_seeder(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(3, Role::count());
        $this->assertSame(9, Permission::count());
    }

    /**
     * @return array<string, int>
     */
    private function snapshot(): array
    {
        return [
            'users' => User::count(),
            'students' => Student::count(),
            'lecturers' => Lecturer::count(),
            'courses' => Course::count(),
            'registrations' => CourseRegistration::count(),
            'roles' => Role::count(),
            'permissions' => Permission::count(),
            'role_assignments' => DB::table('model_has_roles')->count(),
        ];
    }
}
