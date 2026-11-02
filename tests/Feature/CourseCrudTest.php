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

class CourseCrudTest extends TestCase
{
    use RefreshDatabase;

    private int $sequence = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_a_guest_cannot_reach_the_catalogue(): void
    {
        $this->get('/courses')->assertRedirect('/login');
        $this->post('/courses')->assertRedirect('/login');
    }

    public function test_an_administrator_adds_a_course_to_the_catalogue(): void
    {
        $administrator = $this->userWithRole('administrator');
        $lecturer = $this->lecturer();

        $this->actingAs($administrator)->get('/courses/create')
            ->assertOk()
            ->assertSee($lecturer->staff_number);

        $response = $this->actingAs($administrator)->post('/courses', $this->coursePayload([
            'course_code' => 'BUC3131',
            'title' => 'Advanced Web Application Development',
            'credits' => 4,
            'lecturer_id' => $lecturer->id,
            'description' => 'Laravel architecture, authentication and testing.',
        ]));

        $course = Course::sole();

        $response->assertRedirect(route('courses.show', $course));
        $response->assertSessionHas('status', 'Course BUC3131 has been added to the catalogue.');

        $this->assertSame($lecturer->id, $course->lecturer_id);
        $this->assertSame(4, $course->credits);

        $this->actingAs($administrator)
            ->get(route('courses.show', $course))
            ->assertOk()
            ->assertSee('BUC3131')
            ->assertSee($lecturer->full_name)
            ->assertSee('Course BUC3131 has been added to the catalogue.');
    }

    public function test_a_lecturer_and_a_student_may_not_write_to_the_catalogue(): void
    {
        $this->actingAs($this->userWithRole('lecturer'))->get('/courses/create')->assertForbidden();
        $this->actingAs($this->userWithRole('student'))->get('/courses/create')->assertForbidden();

        $this->actingAs($this->userWithRole('student'))
            ->post('/courses', ['course_code' => 'BUC9999'])
            ->assertForbidden();

        $this->assertDatabaseCount('courses', 0);
    }

    public function test_a_course_code_already_on_the_catalogue_is_rejected(): void
    {
        $course = $this->course();

        $this->actingAs($this->userWithRole('administrator'))
            ->from('/courses/create')
            ->post('/courses', $this->coursePayload([
                'course_code' => $course->course_code,
                'lecturer_id' => $course->lecturer_id,
            ]))
            ->assertRedirect('/courses/create')
            ->assertSessionHasErrors(['course_code' => 'A course with that code is already on the catalogue.']);

        $this->assertDatabaseCount('courses', 1);
    }

    public function test_credit_units_outside_the_valid_range_are_rejected(): void
    {
        $administrator = $this->userWithRole('administrator');
        $lecturer = $this->lecturer();

        foreach ([0, 13] as $credits) {
            $this->actingAs($administrator)
                ->post('/courses', $this->coursePayload([
                    'credits' => $credits,
                    'lecturer_id' => $lecturer->id,
                ]))
                ->assertSessionHasErrors(['credits' => 'Credit units must be between 1 and 12.']);
        }

        $this->assertDatabaseCount('courses', 0);
    }

    public function test_a_course_must_be_assigned_to_a_lecturer_that_exists(): void
    {
        $this->actingAs($this->userWithRole('administrator'))
            ->post('/courses', $this->coursePayload(['lecturer_id' => null]))
            ->assertSessionHasErrors(['lecturer_id' => 'Every course must be assigned to a lecturer.']);

        $this->actingAs($this->userWithRole('administrator'))
            ->post('/courses', $this->coursePayload(['lecturer_id' => 9999]))
            ->assertSessionHasErrors(['lecturer_id' => 'The chosen lecturer is not in the staff register.']);

        $this->assertDatabaseCount('courses', 0);
    }

    public function test_an_administrator_edits_a_course_without_repealing_the_unique_code(): void
    {
        $course = $this->course();
        $replacement = $this->lecturer();

        $this->actingAs($this->userWithRole('administrator'))
            ->get(route('courses.edit', $course))
            ->assertOk();

        $response = $this->actingAs($this->userWithRole('administrator'))
            ->put(route('courses.update', $course), [
                'course_code' => $course->course_code,
                'title' => 'Advanced Web Application Development',
                'credits' => 6,
                'semester' => 'Semester 2',
                'description' => null,
                'lecturer_id' => $replacement->id,
            ]);

        $course->refresh();

        $response->assertRedirect(route('courses.show', $course));
        $response->assertSessionHas('status', 'The course details have been updated.');
        $this->assertSame(6, $course->credits);
        $this->assertSame('Semester 2', $course->semester);
        $this->assertSame($replacement->id, $course->lecturer_id);
    }

    public function test_an_edit_may_not_take_the_code_of_another_course(): void
    {
        $course = $this->course();
        $other = $this->course();

        $this->actingAs($this->userWithRole('administrator'))
            ->put(route('courses.update', $course), [
                'course_code' => $other->course_code,
                'title' => $course->title,
                'credits' => $course->credits,
                'semester' => $course->semester,
                'lecturer_id' => $course->lecturer_id,
            ])
            ->assertSessionHasErrors(['course_code' => 'Another course already carries that code.']);

        $this->assertSame($course->course_code, $course->fresh()->course_code);
    }

    public function test_a_lecturer_cannot_reassign_a_course_it_teaches(): void
    {
        $lecturer = $this->lecturer();

        $this->actingAs($lecturer->user)
            ->put(route('courses.update', $this->course($lecturer)), ['course_code' => 'BUC7777'])
            ->assertForbidden();
    }

    public function test_an_administrator_removes_a_course_nobody_sits_on(): void
    {
        $course = $this->course();

        $this->actingAs($this->userWithRole('administrator'))
            ->delete(route('courses.destroy', $course))
            ->assertRedirect(route('courses.index'))
            ->assertSessionHas('status', "Course {$course->course_code} has been removed from the catalogue.");

        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_a_course_with_students_registered_on_it_is_protected_from_deletion(): void
    {
        $course = $this->course();
        $this->register($this->student(), $course);

        $this->actingAs($this->userWithRole('administrator'))
            ->from(route('courses.show', $course))
            ->delete(route('courses.destroy', $course))
            ->assertRedirect(route('courses.show', $course))
            ->assertSessionHas('error', "Students are still registered on {$course->course_code}. Clear those registrations first.");

        $this->assertDatabaseHas('courses', ['id' => $course->id]);
        $this->assertDatabaseCount('course_registrations', 1);
    }

    public function test_the_same_address_hands_each_role_a_different_list(): void
    {
        $lecturer = $this->lecturer();
        $assigned = $this->course($lecturer);
        $someoneElse = $this->course();
        $student = $this->student();
        $this->register($student, $someoneElse);

        $this->actingAs($this->userWithRole('administrator'))
            ->get('/courses')
            ->assertOk()
            ->assertSee('All courses')
            ->assertSee($assigned->course_code)
            ->assertSee($someoneElse->course_code);

        $this->actingAs($lecturer->user)
            ->get('/courses')
            ->assertOk()
            ->assertSee('Courses assigned to you')
            ->assertSee($assigned->course_code)
            ->assertDontSee($someoneElse->course_code);

        $this->actingAs($student->user)
            ->get('/courses')
            ->assertOk()
            ->assertSee('Courses you have not registered for yet')
            ->assertSee($assigned->course_code)
            ->assertDontSee($someoneElse->course_code);
    }

    public function test_a_lecturer_opens_a_course_it_teaches_but_not_another_one(): void
    {
        $lecturer = $this->lecturer();

        $this->actingAs($lecturer->user)
            ->get(route('courses.show', $this->course($lecturer)))
            ->assertOk();

        $this->actingAs($lecturer->user)
            ->get(route('courses.show', $this->course()))
            ->assertForbidden();
    }

    public function test_the_course_page_lists_the_students_registered_on_it(): void
    {
        $course = $this->course();
        $student = $this->student();
        $this->register($student, $course);

        $this->actingAs($this->userWithRole('administrator'))
            ->get(route('courses.show', $course))
            ->assertOk()
            ->assertSee($student->full_name)
            ->assertSee($student->student_number)
            ->assertSee('Students registered on this course (1)');
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function coursePayload(array $overrides = []): array
    {
        return array_merge([
            'course_code' => 'BUC'.str_pad((string) $this->sequence++, 4, '0', STR_PAD_LEFT),
            'title' => 'Enterprise Application Development',
            'credits' => 3,
            'semester' => 'Semester 1',
            'description' => null,
            'lecturer_id' => null,
        ], $overrides);
    }

    private function course(?Lecturer $lecturer = null): Course
    {
        return Course::create($this->coursePayload([
            'lecturer_id' => ($lecturer ?? $this->lecturer())->id,
        ]));
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

    private function student(): Student
    {
        $user = $this->userWithRole('student');

        $student = Student::create([
            'user_id' => $user->id,
            'student_number' => '24007'.str_pad((string) $this->sequence++, 5, '0', STR_PAD_LEFT),
            'first_name' => 'Janat',
            'last_name' => 'Ntege',
            'email' => 'student'.$this->sequence.'@student.makerere.ac.ug',
            'programme' => 'Bachelor of Business Computing',
            'year_of_study' => 3,
        ]);

        $student->setRelation('user', $user);

        return $student;
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
