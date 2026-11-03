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

class CourseRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private int $sequence = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_a_guest_is_sent_to_the_login_page_before_registering(): void
    {
        $this->get('/registrations')->assertRedirect('/login');
        $this->post('/registrations')->assertRedirect('/login');
    }

    public function test_a_student_registers_for_a_course_and_is_told_it_worked(): void
    {
        $student = $this->student();
        $course = $this->course();

        $this->actingAs($student->user)->get('/registrations/create')
            ->assertOk()
            ->assertSee($course->course_code)
            ->assertSee($student->full_name);

        $response = $this->actingAs($student->user)->post('/registrations', [
            'student_id' => $student->id,
            'course_id' => $course->id,
        ]);

        $registration = CourseRegistration::sole();

        $response->assertRedirect('/registrations');
        $response->assertSessionHas('status',
            "{$student->full_name} is now registered for {$course->course_code}.");

        $this->assertSame($student->id, $registration->student_id);
        $this->assertSame($course->id, $registration->course_id);
        $this->assertSame('registered', $registration->status);
        $this->assertNotNull($registration->registered_at);

        $this->actingAs($student->user)
            ->get('/registrations')
            ->assertOk()
            ->assertSee($course->course_code)
            ->assertSee('registered')
            ->assertSee("{$student->full_name} is now registered for {$course->course_code}.");

        $this->actingAs($student->user)
            ->get(route('students.show', $student))
            ->assertSee($course->course_code);

        $this->assertSame($course->id, $student->courses->sole()->id);
    }

    public function test_a_second_entry_for_the_same_student_and_course_is_refused(): void
    {
        $student = $this->student();
        $course = $this->course();
        $this->register($student, $course);

        $this->actingAs($student->user)
            ->from('/registrations/create')
            ->post('/registrations', ['student_id' => $student->id, 'course_id' => $course->id])
            ->assertRedirect('/registrations/create')
            ->assertSessionHasErrors(['course_id' => 'That student is already registered for this course.']);

        $this->assertSame(1, CourseRegistration::count());
    }

    public function test_a_course_already_on_the_list_of_courses_is_hidden_from_the_form(): void
    {
        $student = $this->student();
        $taken = $this->course();
        $open = $this->course();
        $this->register($student, $taken);

        $this->actingAs($student->user)
            ->get('/registrations/create')
            ->assertOk()
            ->assertDontSee($taken->course_code)
            ->assertSee($open->course_code);
    }

    public function test_a_student_cannot_register_a_classmate_by_editing_the_form(): void
    {
        $sender = $this->student();
        $classmate = $this->student();
        $course = $this->course();

        $this->actingAs($sender->user)
            ->post('/registrations', ['student_id' => $classmate->id, 'course_id' => $course->id])
            ->assertRedirect('/registrations');

        $registration = CourseRegistration::sole();

        $this->assertSame($sender->id, $registration->student_id);
        $this->assertNotSame($classmate->id, $registration->student_id);
    }

    public function test_an_account_with_no_student_record_behind_it_is_told_so(): void
    {
        $user = $this->userWithRole('student');

        $this->actingAs($user)
            ->get('/registrations/create')
            ->assertRedirect()
            ->assertSessionHas('error',
                'This account is not linked to a student record yet, so there is nothing to register.');

        $this->actingAs($user)
            ->post('/registrations', ['course_id' => $this->course()->id])
            ->assertSessionHasErrors(['student_id' => 'This account is not linked to a student record yet, so there is nothing to register.']);

        $this->assertDatabaseCount('course_registrations', 0);
    }

    public function test_a_lecturer_is_refused_the_registration_form_and_the_save(): void
    {
        $lecturer = $this->lecturer();

        $this->actingAs($lecturer->user)->get('/registrations/create')->assertForbidden();
        $this->actingAs($lecturer->user)
            ->post('/registrations', ['course_id' => $this->course($lecturer)->id])
            ->assertForbidden();

        $this->assertDatabaseCount('course_registrations', 0);
    }

    public function test_an_administrator_registers_a_named_student_on_behalf_of_them(): void
    {
        $administrator = $this->userWithRole('administrator');
        $student = $this->student();
        $course = $this->course();

        $this->actingAs($administrator)->get('/registrations/create')
            ->assertOk()
            ->assertSee($student->student_number);

        $this->actingAs($administrator)
            ->post('/registrations', ['student_id' => $student->id, 'course_id' => $course->id])
            ->assertRedirect('/registrations')
            ->assertSessionHas('status', "{$student->full_name} is now registered for {$course->course_code}.");

        $this->assertDatabaseHas('course_registrations', [
            'student_id' => $student->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_an_administrator_is_refused_a_student_or_a_course_that_is_not_on_the_books(): void
    {
        $administrator = $this->userWithRole('administrator');

        $this->actingAs($administrator)
            ->post('/registrations', ['student_id' => null, 'course_id' => $this->course()->id])
            ->assertSessionHasErrors('student_id');

        $this->actingAs($administrator)
            ->post('/registrations', ['student_id' => $this->student()->id, 'course_id' => 9999])
            ->assertSessionHasErrors(['course_id' => 'Choose a course that exists on the catalogue.']);

        $this->assertDatabaseCount('course_registrations', 0);
    }

    public function test_a_student_cancels_a_registration_of_their_own(): void
    {
        $student = $this->student();
        $course = $this->course();
        $registration = $this->register($student, $course);

        $this->actingAs($student->user)
            ->from('/registrations')
            ->delete(route('registrations.destroy', $registration))
            ->assertRedirect('/registrations')
            ->assertSessionHas('status', "The registration for {$course->course_code} has been cancelled.");

        $this->assertDatabaseMissing('course_registrations', ['id' => $registration->id]);
    }

    public function test_a_student_cannot_cancel_a_classmates_registration(): void
    {
        $registration = $this->register($this->student(), $this->course());

        $this->actingAs($this->student()->user)
            ->delete(route('registrations.destroy', $registration))
            ->assertForbidden();

        $this->assertDatabaseHas('course_registrations', ['id' => $registration->id]);
    }

    public function test_the_registration_list_is_filtered_to_the_rows_a_role_owns(): void
    {
        $lecturer = $this->lecturer();
        $taught = $this->course($lecturer);
        $someoneElse = $this->course();

        $mine = $this->student();
        $theirs = $this->student();
        $this->register($mine, $taught);
        $this->register($theirs, $someoneElse);

        $this->actingAs($this->userWithRole('administrator'))
            ->get('/registrations')
            ->assertOk()
            ->assertSee($mine->student_number)
            ->assertSee($theirs->student_number);

        $this->actingAs($lecturer->user)
            ->get('/registrations')
            ->assertOk()
            ->assertSee($mine->student_number)
            ->assertDontSee($theirs->student_number);

        $this->actingAs($mine->user)
            ->get('/registrations')
            ->assertOk()
            ->assertSee($mine->student_number)
            ->assertDontSee($theirs->student_number);
    }

    public function test_a_lecturer_reads_the_registration_list_but_may_not_change_it(): void
    {
        $lecturer = $this->lecturer();
        $registration = $this->register($this->student(), $this->course($lecturer));

        $this->actingAs($lecturer->user)->get('/registrations')->assertOk();

        $this->actingAs($lecturer->user)
            ->delete(route('registrations.destroy', $registration))
            ->assertForbidden();
    }

    private function student(): Student
    {
        $user = $this->userWithRole('student');

        $student = Student::create([
            'user_id' => $user->id,
            'student_number' => '24007'.str_pad((string) $this->sequence++, 5, '0', STR_PAD_LEFT),
            'first_name' => 'Muniirah',
            'last_name' => 'Munywanyi',
            'email' => 'student'.$this->sequence.'@student.makerere.ac.ug',
            'programme' => 'Bachelor of Business Computing',
            'year_of_study' => 3,
        ]);

        $student->setRelation('user', $user);

        return $student;
    }

    private function lecturer(): Lecturer
    {
        $user = $this->userWithRole('lecturer');

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
            'lecturer_id' => ($lecturer ?? $this->lecturer())->id,
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

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create(['email' => 'user'.$this->sequence++.'@example.com']);
        $user->assignRole($role);

        return $user;
    }
}
