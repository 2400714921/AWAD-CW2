<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentCrudTest extends TestCase
{
    use RefreshDatabase;

    private int $sequence = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_a_guest_is_sent_to_the_login_page_before_reaching_the_register(): void
    {
        $this->get('/students')->assertRedirect('/login');
        $this->post('/students')->assertRedirect('/login');
    }

    public function test_an_administrator_opens_the_form_and_puts_a_student_on_the_register(): void
    {
        $administrator = $this->userWithRole('administrator');
        $account = $this->userWithRole('student');

        $this->actingAs($administrator)->get('/students/create')->assertOk();

        $response = $this->actingAs($administrator)->post('/students', $this->studentPayload([
            'student_number' => '2400709637',
            'first_name' => 'Nansikombi',
            'last_name' => 'Kirsten',
            'email' => 'kirsten@student.makerere.ac.ug',
            'year_of_study' => 3,
            'user_id' => $account->id,
        ]));

        $student = Student::sole();

        $response->assertRedirect(route('students.show', $student));
        $response->assertSessionHas('status', 'Student 2400709637 has been added to the register.');

        $this->assertSame('Bachelor of Business Computing', $student->programme);
        $this->assertSame(3, $student->year_of_study);
        $this->assertSame($account->id, $student->user_id);
        $this->assertSame('Nansikombi Kirsten', $student->full_name);

        $this->actingAs($administrator)
            ->get(route('students.show', $student))
            ->assertOk()
            ->assertSee('2400709637')
            ->assertSee('kirsten@student.makerere.ac.ug')
            ->assertSee('Student 2400709637 has been added to the register.');
    }

    public function test_a_lecturer_and_a_student_are_both_refused_the_new_student_form(): void
    {
        $this->actingAs($this->userWithRole('lecturer'))->get('/students/create')->assertForbidden();
        $this->actingAs($this->userWithRole('student'))->get('/students/create')->assertForbidden();

        $this->actingAs($this->userWithRole('lecturer'))
            ->post('/students', ['student_number' => '2400711111'])
            ->assertForbidden();

        $this->assertDatabaseCount('students', 0);
    }

    public function test_an_admission_number_already_on_the_register_is_rejected(): void
    {
        $registered = $this->student();

        $this->actingAs($this->userWithRole('administrator'))
            ->from('/students/create')
            ->post('/students', $this->studentPayload(['student_number' => $registered->student_number]))
            ->assertRedirect('/students/create')
            ->assertSessionHasErrors(['student_number' => 'That admission number is already on the register.']);

        $this->assertDatabaseCount('students', 1);
    }

    public function test_the_email_of_another_student_is_rejected(): void
    {
        $registered = $this->student();

        $this->actingAs($this->userWithRole('administrator'))
            ->post('/students', $this->studentPayload(['email' => $registered->email]))
            ->assertSessionHasErrors(['email' => 'Another student record already uses that email address.']);

        $this->assertDatabaseCount('students', 1);
    }

    public function test_the_year_of_study_must_lie_between_one_and_six(): void
    {
        $administrator = $this->userWithRole('administrator');

        foreach ([0, 7] as $year) {
            $this->actingAs($administrator)
                ->post('/students', $this->studentPayload(['year_of_study' => $year]))
                ->assertSessionHasErrors('year_of_study');
        }

        $this->assertSame('A student can only be in year one to year six.',
            $this->firstError('year_of_study'));

        $this->assertDatabaseCount('students', 0);
    }

    public function test_a_blank_name_and_a_future_date_of_birth_are_rejected_with_their_own_messages(): void
    {
        $this->actingAs($this->userWithRole('administrator'))
            ->post('/students', $this->studentPayload([
                'first_name' => '',
                'date_of_birth' => now()->addYear()->toDateString(),
            ]))
            ->assertSessionHasErrors(['first_name', 'date_of_birth'])
            ->assertSessionHasErrors(['date_of_birth' => 'The date of birth must be in the past.']);

        $this->assertDatabaseCount('students', 0);
    }

    public function test_an_administrator_edits_a_record_without_the_unique_rules_blocking_the_owner(): void
    {
        $student = $this->student();

        $this->actingAs($this->userWithRole('administrator'))
            ->get(route('students.edit', $student))
            ->assertOk()
            ->assertSee($student->student_number);

        $response = $this->actingAs($this->userWithRole('administrator'))
            ->put(route('students.update', $student), [
                'student_number' => $student->student_number,
                'first_name' => 'Daphne',
                'last_name' => $student->last_name,
                'email' => $student->email,
                'programme' => $student->programme,
                'year_of_study' => 4,
                'date_of_birth' => null,
            ]);

        $student->refresh();

        $response->assertRedirect(route('students.show', $student));
        $response->assertSessionHas('status', 'The student record has been updated.');
        $this->assertSame('Daphne', $student->first_name);
        $this->assertSame(4, $student->year_of_study);
        $this->assertNull($student->date_of_birth);
    }

    public function test_an_edit_may_not_steal_the_admission_number_of_another_student(): void
    {
        $student = $this->student();
        $other = $this->student();

        $this->actingAs($this->userWithRole('administrator'))
            ->put(route('students.update', $student), [
                'student_number' => $other->student_number,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'email' => $student->email,
                'programme' => $student->programme,
                'year_of_study' => $student->year_of_study,
            ])
            ->assertSessionHasErrors(['student_number' => 'That admission number belongs to another student.']);

        $this->assertSame($student->student_number, $student->fresh()->student_number);
    }

    public function test_a_lecturer_cannot_edit_a_record_it_may_only_read(): void
    {
        $student = $this->student();

        $this->actingAs($this->userWithRole('lecturer'))
            ->put(route('students.update', $student), ['student_number' => '2400799999'])
            ->assertForbidden();
    }

    public function test_an_administrator_deletes_a_record_that_carries_no_registrations(): void
    {
        $student = $this->student();

        $this->actingAs($this->userWithRole('administrator'))
            ->delete(route('students.destroy', $student))
            ->assertRedirect(route('students.index'))
            ->assertSessionHas('status', 'The student record has been deleted.');

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    public function test_a_record_still_registered_on_a_course_is_protected_from_deletion(): void
    {
        $student = $this->student();
        $this->register($student, $this->course());

        $this->actingAs($this->userWithRole('administrator'))
            ->from(route('students.show', $student))
            ->delete(route('students.destroy', $student))
            ->assertRedirect(route('students.show', $student))
            ->assertSessionHas('error', "Remove {$student->full_name}'s course registrations before deleting the record.");

        $this->assertDatabaseHas('students', ['id' => $student->id]);
        $this->assertDatabaseCount('course_registrations', 1);
    }

    public function test_the_register_shows_each_role_only_the_rows_that_belong_to_it(): void
    {
        $lecturer = $this->lecturerWithRole('lecturer');
        $taught = $this->studentWithAccount();
        $unrelated = $this->studentWithAccount();
        $this->register($taught, $this->course($lecturer));

        $this->actingAs($this->userWithRole('administrator'))
            ->get('/students')
            ->assertOk()
            ->assertSee($taught->student_number)
            ->assertSee($unrelated->student_number);

        $this->actingAs($lecturer->user)
            ->get('/students')
            ->assertOk()
            ->assertSee($taught->student_number)
            ->assertDontSee($unrelated->student_number);

        $this->actingAs($taught->user)
            ->get('/students')
            ->assertOk()
            ->assertSee($taught->student_number)
            ->assertDontSee($unrelated->student_number);
    }

    public function test_a_student_opens_its_own_record_but_is_refused_a_classmates(): void
    {
        $own = $this->studentWithAccount();
        $classmate = $this->studentWithAccount();

        $this->actingAs($own->user)->get(route('students.show', $own))->assertOk();
        $this->actingAs($own->user)->get(route('students.show', $classmate))->assertForbidden();

        $this->actingAs($classmate->user)
            ->delete(route('students.destroy', $own))
            ->assertForbidden();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function studentPayload(array $overrides = []): array
    {
        return array_merge([
            'student_number' => '24007'.str_pad((string) $this->sequence++, 5, '0', STR_PAD_LEFT),
            'first_name' => 'Shadia',
            'last_name' => 'Byonga',
            'email' => 'student'.$this->sequence.'@student.makerere.ac.ug',
            'programme' => 'Bachelor of Business Computing',
            'year_of_study' => 2,
            'date_of_birth' => '2005-01-20',
        ], $overrides);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function student(array $overrides = []): Student
    {
        return Student::create($this->studentPayload($overrides));
    }

    private function studentWithAccount(): Student
    {
        $user = $this->userWithRole('student');
        $student = $this->student(['user_id' => $user->id]);
        $student->setRelation('user', $user);

        return $student;
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create(['email' => 'user'.$this->sequence++.'@example.com']);
        $user->assignRole($role);

        return $user;
    }

    private function lecturerWithRole(string $role): Lecturer
    {
        $user = $this->userWithRole($role);

        $lecturer = Lecturer::create([
            'user_id' => $user->id,
            'staff_number' => 'SP-'.str_pad((string) $this->sequence++, 4, '0', STR_PAD_LEFT),
            'first_name' => 'Godfrey',
            'last_name' => 'Asingya',
            'email' => 'staff'.$this->sequence.'@makerere.ac.ug',
            'department' => 'Computing',
        ]);

        $lecturer->setRelation('user', $user);

        return $lecturer;
    }

    private function course(?Lecturer $lecturer = null): Course
    {
        return Course::create([
            'course_code' => 'BUC'.str_pad((string) $this->sequence++, 4, '0', STR_PAD_LEFT),
            'title' => 'Advanced Web Application Development',
            'credits' => 4,
            'semester' => 'Semester 1',
            'lecturer_id' => ($lecturer ?? $this->lecturerWithRole('lecturer'))->id,
        ]);
    }

    private function register(Student $student, Course $course): CourseRegistration
    {
        return CourseRegistration::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'registered',
            'registered_at' => now(),
        ]);
    }

    private function firstError(string $field): string
    {
        return session('errors')->getBag('default')->first($field);
    }
}
