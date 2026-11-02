<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Lecturer;
use App\Models\Student;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_student_sees_the_courses_they_registered_for(): void
    {
        $student = $this->makeStudent();
        $course = $this->makeCourse();

        $student->courses()->attach($course->id, ['status' => 'registered', 'registered_at' => now()]);

        $courses = $student->fresh()->courses;

        $this->assertCount(1, $courses);
        $this->assertTrue($courses->contains($course->id));
        $this->assertSame('registered', $courses->first()->pivot->status);
    }

    public function test_a_course_sees_the_students_registered_on_it(): void
    {
        $course = $this->makeCourse();
        $students = collect([$this->makeStudent(), $this->makeStudent('2400708517')]);

        $course->students()->attach($students->pluck('id')->all());

        $this->assertCount(2, $course->fresh()->students);
        $this->assertTrue($course->fresh()->students->contains($students->first()->id));
    }

    public function test_a_lecturer_sees_the_courses_assigned_to_them(): void
    {
        $course = $this->makeCourse();

        $this->assertTrue($course->lecturer->courses->contains($course->id));
        $this->assertSame('Computing', $course->fresh()->load('lecturer')->lecturer->department);
    }

    public function test_a_registration_row_belongs_to_a_student_and_a_course(): void
    {
        $student = $this->makeStudent();
        $course = $this->makeCourse();

        $registration = CourseRegistration::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'registered',
            'registered_at' => now(),
        ]);

        $this->assertTrue($registration->student->is($student));
        $this->assertTrue($registration->course->is($course));
    }

    public function test_a_student_cannot_register_for_the_same_course_twice(): void
    {
        $this->expectException(QueryException::class);

        $student = $this->makeStudent();
        $course = $this->makeCourse();

        $student->courses()->attach($course->id);
        $student->courses()->attach($course->id);
    }

    public function test_a_course_cannot_belong_to_a_lecturer_that_does_not_exist(): void
    {
        $this->expectException(QueryException::class);

        Course::create([
            'course_code' => 'BUC9999',
            'title' => 'Ghost Course',
            'credits' => 2,
            'semester' => 'Semester 2',
            'lecturer_id' => 9999,
        ]);
    }

    public function test_deleting_a_student_removes_their_registrations(): void
    {
        $student = $this->makeStudent();
        $course = $this->makeCourse();
        $student->courses()->attach($course->id);

        $this->assertSame(1, CourseRegistration::count());

        $student->delete();

        $this->assertSame(0, CourseRegistration::count());
    }

    private function makeStudent(string $studentNumber = '2400709637'): Student
    {
        return Student::create([
            'student_number' => $studentNumber,
            'first_name' => 'Kirsten',
            'last_name' => 'Nansikombi',
            'email' => $studentNumber.'@student.makerere.ac.ug',
            'year_of_study' => 3,
        ]);
    }

    private function makeCourse(string $courseCode = 'BUC3131'): Course
    {
        $lecturer = Lecturer::create([
            'staff_number' => 'SP-'.$courseCode,
            'first_name' => 'Aisha',
            'last_name' => 'Nabbanja',
            'email' => strtolower($courseCode).'@makerere.ac.ug',
            'department' => 'Computing',
        ]);

        return Course::create([
            'course_code' => $courseCode,
            'title' => 'Advanced Web Application Development',
            'credits' => 4,
            'semester' => 'Semester 1',
            'lecturer_id' => $lecturer->id,
        ]);
    }
}
