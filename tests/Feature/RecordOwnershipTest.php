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

class RecordOwnershipTest extends TestCase
{
    use RefreshDatabase;

    private int $sequence = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_a_student_opens_their_own_record_but_not_a_classmates(): void
    {
        $own = $this->studentWithAccount('student');
        $classmate = $this->studentWithAccount('student');

        $this->assertTrue($own->user->can('view', $own));
        $this->assertFalse($own->user->can('view', $classmate));
    }

    public function test_a_lecturer_reads_any_student_record_but_cannot_alter_one(): void
    {
        $lecturer = $this->userWithRole('lecturer');
        $student = $this->studentWithAccount('student');

        $this->assertTrue($lecturer->can('view', $student));
        $this->assertFalse($lecturer->can('update', $student));
        $this->assertFalse($lecturer->can('create', Student::class));
    }

    public function test_an_administrator_changes_student_records_and_a_student_cannot(): void
    {
        $administrator = $this->userWithRole('administrator');
        $student = $this->studentWithAccount('student');

        $this->assertTrue($administrator->can('update', $student));
        $this->assertTrue($administrator->can('delete', $student));
        $this->assertFalse($student->user->can('update', $student));
    }

    public function test_a_lecturer_opens_only_the_courses_assigned_to_them(): void
    {
        $assigned = $this->course($this->lecturerWithAccount('lecturer'));
        $someoneElse = $this->course($this->lecturerWithAccount('lecturer'));

        $this->assertTrue($assigned->lecturer->user->can('view', $assigned));
        $this->assertFalse($assigned->lecturer->user->can('view', $someoneElse));
        $this->assertFalse($assigned->lecturer->user->can('update', $assigned));
    }

    public function test_a_student_sees_their_own_registration_but_not_a_classmates(): void
    {
        $course = $this->course();
        $own = $this->studentWithAccount('student');
        $classmate = $this->studentWithAccount('student');

        $mine = $this->register($own, $course);
        $theirs = $this->register($classmate, $course);

        $this->assertTrue($own->user->can('view', $mine));
        $this->assertFalse($own->user->can('view', $theirs));
        $this->assertTrue($own->user->can('delete', $mine));
        $this->assertFalse($own->user->can('delete', $theirs));
    }

    public function test_the_lecturer_of_a_course_reads_the_registrations_on_it(): void
    {
        $lecturer = $this->lecturerWithAccount('lecturer');
        $course = $this->course($lecturer);
        $registration = $this->register($this->studentWithAccount('student'), $course);

        $this->assertTrue($lecturer->user->can('view', $registration));
        $this->assertFalse($lecturer->user->can('delete', $registration));
        $this->assertTrue($this->userWithRole('administrator')->can('view', $registration));
    }

    public function test_only_an_administrator_or_a_signed_up_student_may_register(): void
    {
        $this->assertTrue($this->userWithRole('student')->can('create', CourseRegistration::class));
        $this->assertTrue($this->userWithRole('administrator')->can('create', CourseRegistration::class));
        $this->assertFalse($this->userWithRole('lecturer')->can('create', CourseRegistration::class));
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create(['email' => 'user'.$this->sequence++.'@example.com']);
        $user->assignRole($role);

        return $user;
    }

    private function studentWithAccount(string $role): Student
    {
        $user = $this->userWithRole($role);

        $student = Student::create([
            'user_id' => $user->id,
            'student_number' => '24007'.str_pad((string) $this->sequence++, 5, '0', STR_PAD_LEFT),
            'first_name' => 'Amara',
            'last_name' => 'Nabukeera',
            'email' => 'student'.now()->timestamp.'-'.$this->sequence.'@student.makerere.ac.ug',
            'year_of_study' => 3,
        ]);

        $student->setRelation('user', $user);

        return $student;
    }

    private function lecturerWithAccount(string $role): Lecturer
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
            'lecturer_id' => ($lecturer ?? $this->lecturerWithAccount('lecturer'))->id,
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
}
