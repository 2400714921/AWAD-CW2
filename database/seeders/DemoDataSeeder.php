<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DemoDataSeeder extends Seeder
{
    /**
     * The password every demo account signs in with, so one machine can play all three roles.
     */
    public const PASSWORD = 'password';

    /**
     * @var list<array{email: string, name: string, role: string}>
     */
    private const ACCOUNTS = [
        ['email' => 'admin@makerere.ac.ug', 'name' => 'System Registrar', 'role' => 'administrator'],
        ['email' => 'lecturer1@makerere.ac.ug', 'name' => 'Godfrey Asingya', 'role' => 'lecturer'],
        ['email' => 'lecturer2@makerere.ac.ug', 'name' => 'Alice Nakabuye', 'role' => 'lecturer'],
        ['email' => 'lecturer3@makerere.ac.ug', 'name' => 'Peter Muwanga', 'role' => 'lecturer'],
        ['email' => 'student1@makerere.ac.ug', 'name' => 'Nansikombi Kirsten Angel', 'role' => 'student'],
        ['email' => 'student2@makerere.ac.ug', 'name' => 'Byonga Shadia Patience', 'role' => 'student'],
        ['email' => 'student3@makerere.ac.ug', 'name' => 'Munywanyi Muniirah', 'role' => 'student'],
        ['email' => 'student4@makerere.ac.ug', 'name' => 'Ntege Janat', 'role' => 'student'],
        ['email' => 'student5@makerere.ac.ug', 'name' => 'Nakimbugwe Daphne', 'role' => 'student'],
        ['email' => 'student6@makerere.ac.ug', 'name' => 'Aisha Nabirye', 'role' => 'student'],
        ['email' => 'student7@makerere.ac.ug', 'name' => 'Ivan Mukasa', 'role' => 'student'],
        ['email' => 'student8@makerere.ac.ug', 'name' => 'Tracy Namutebi', 'role' => 'student'],
    ];

    /**
     * @var list<array{staff_number: string, first_name: string, last_name: string, email: string, department: string}>
     */
    private const LECTURERS = [
        [
            'staff_number' => 'SP-240112',
            'first_name' => 'Godfrey',
            'last_name' => 'Asingya',
            'email' => 'lecturer1@makerere.ac.ug',
            'department' => 'Department of Computing',
        ],
        [
            'staff_number' => 'SP-240113',
            'first_name' => 'Alice',
            'last_name' => 'Nakabuye',
            'email' => 'lecturer2@makerere.ac.ug',
            'department' => 'Department of Information Systems',
        ],
        [
            'staff_number' => 'SP-240114',
            'first_name' => 'Peter',
            'last_name' => 'Muwanga',
            'email' => 'lecturer3@makerere.ac.ug',
            'department' => 'Department of Business Analytics',
        ],
    ];

    /**
     * @var list<array{course_code: string, title: string, credits: int, semester: string, staff_number: string, description: string}>
     */
    private const COURSES = [
        [
            'course_code' => 'BUC2104',
            'title' => 'Database Systems',
            'credits' => 4,
            'semester' => 'Semester 1',
            'staff_number' => 'SP-240112',
            'description' => 'Relational modelling, normalisation and query design for application back ends.',
        ],
        [
            'course_code' => 'BUC2206',
            'title' => 'Object Oriented Programming',
            'credits' => 4,
            'semester' => 'Semester 2',
            'staff_number' => 'SP-240112',
            'description' => 'Classes, inheritance and design patterns practised through PHP and Java.',
        ],
        [
            'course_code' => 'BUC3131',
            'title' => 'Advanced Web Application Development',
            'credits' => 4,
            'semester' => 'Semester 1',
            'staff_number' => 'SP-240113',
            'description' => 'MVC frameworks, authentication, role based access and deployment of web systems.',
        ],
        [
            'course_code' => 'BUC3204',
            'title' => 'Web and Mobile Computing',
            'credits' => 3,
            'semester' => 'Semester 2',
            'staff_number' => 'SP-240113',
            'description' => 'Responsive interfaces, service APIs and delivery to mobile clients.',
        ],
        [
            'course_code' => 'BUC2302',
            'title' => 'Business Analytics',
            'credits' => 3,
            'semester' => 'Semester 1',
            'staff_number' => 'SP-240114',
            'description' => 'Descriptive and predictive analysis of institutional data sets.',
        ],
        [
            'course_code' => 'BUC4108',
            'title' => 'Software Engineering Practice',
            'credits' => 6,
            'semester' => 'Semester 2',
            'staff_number' => 'SP-240114',
            'description' => 'Requirements, version control, testing and team delivery of a working system.',
        ],
    ];

    /**
     * @var list<array{student_number: string, first_name: string, last_name: string, email: string, year_of_study: int, date_of_birth: string}>
     */
    private const STUDENTS = [
        [
            'student_number' => '2400709637',
            'first_name' => 'Nansikombi',
            'last_name' => 'Kirsten',
            'email' => 'student1@makerere.ac.ug',
            'year_of_study' => 3,
            'date_of_birth' => '2004-02-11',
        ],
        [
            'student_number' => '2400714921',
            'first_name' => 'Byonga',
            'last_name' => 'Shadia',
            'email' => 'student2@makerere.ac.ug',
            'year_of_study' => 3,
            'date_of_birth' => '2004-09-27',
        ],
        [
            'student_number' => '2400707324',
            'first_name' => 'Munywanyi',
            'last_name' => 'Muniirah',
            'email' => 'student3@makerere.ac.ug',
            'year_of_study' => 3,
            'date_of_birth' => '2004-05-04',
        ],
        [
            'student_number' => '2400710338',
            'first_name' => 'Ntege',
            'last_name' => 'Janat',
            'email' => 'student4@makerere.ac.ug',
            'year_of_study' => 3,
            'date_of_birth' => '2003-12-19',
        ],
        [
            'student_number' => '2400708517',
            'first_name' => 'Nakimbugwe',
            'last_name' => 'Daphne',
            'email' => 'student5@makerere.ac.ug',
            'year_of_study' => 3,
            'date_of_birth' => '2004-07-08',
        ],
        [
            'student_number' => '2400712456',
            'first_name' => 'Aisha',
            'last_name' => 'Nabirye',
            'email' => 'student6@makerere.ac.ug',
            'year_of_study' => 2,
            'date_of_birth' => '2005-03-22',
        ],
        [
            'student_number' => '2400721033',
            'first_name' => 'Ivan',
            'last_name' => 'Mukasa',
            'email' => 'student7@makerere.ac.ug',
            'year_of_study' => 4,
            'date_of_birth' => '2002-11-30',
        ],
        [
            'student_number' => '2400706812',
            'first_name' => 'Tracy',
            'last_name' => 'Namutebi',
            'email' => 'student8@makerere.ac.ug',
            'year_of_study' => 1,
            'date_of_birth' => '2006-01-15',
        ],
    ];

    /**
     * Each student number maps to the courses that student is sitting on.
     *
     * @var array<string, list<string>>
     */
    private const REGISTRATIONS = [
        '2400709637' => ['BUC2104', 'BUC3131', 'BUC2302'],
        '2400714921' => ['BUC2104', 'BUC3131', 'BUC4108'],
        '2400707324' => ['BUC2206', 'BUC3131', 'BUC3204'],
        '2400710338' => ['BUC3131', 'BUC2302'],
        '2400708517' => ['BUC2104', 'BUC3204', 'BUC2302'],
        '2400712456' => ['BUC2104', 'BUC2206'],
        '2400721033' => ['BUC4108', 'BUC3204'],
        '2400706812' => ['BUC2104', 'BUC2302'],
    ];

    /**
     * Every row is written against its own unique key, so running the seeder a second time updates
     * what is already there instead of adding a copy of it.
     */
    public function run(): void
    {
        $this->accounts();

        $lecturers = $this->lecturers();
        $courses = $this->courses($lecturers);
        $students = $this->students();

        $this->registrations($students, $courses);
    }

    private function accounts(): void
    {
        foreach (self::ACCOUNTS as $account) {
            $user = User::firstOrNew(['email' => $account['email']]);

            $user->name = $account['name'];
            $user->password = self::PASSWORD;
            $user->email_verified_at ??= now();
            $user->save();

            $user->syncRoles([$account['role']]);
        }
    }

    /**
     * @return Collection<string, int>
     */
    private function lecturers(): Collection
    {
        foreach (self::LECTURERS as $lecturer) {
            Lecturer::updateOrCreate(
                ['staff_number' => $lecturer['staff_number']],
                [...$lecturer, 'user_id' => $this->accountId($lecturer['email'])],
            );
        }

        return Lecturer::pluck('id', 'staff_number');
    }

    /**
     * @param  Collection<string, int>  $lecturers
     * @return Collection<string, int>
     */
    private function courses(Collection $lecturers): Collection
    {
        foreach (self::COURSES as $course) {
            Course::updateOrCreate(
                ['course_code' => $course['course_code']],
                [
                    'title' => $course['title'],
                    'credits' => $course['credits'],
                    'semester' => $course['semester'],
                    'description' => $course['description'],
                    'lecturer_id' => $lecturers[$course['staff_number']],
                ],
            );
        }

        return Course::pluck('id', 'course_code');
    }

    /**
     * @return Collection<string, int>
     */
    private function students(): Collection
    {
        foreach (self::STUDENTS as $student) {
            Student::updateOrCreate(
                ['student_number' => $student['student_number']],
                [
                    'first_name' => $student['first_name'],
                    'last_name' => $student['last_name'],
                    'email' => $student['email'],
                    'programme' => 'Bachelor of Business Computing',
                    'year_of_study' => $student['year_of_study'],
                    'date_of_birth' => $student['date_of_birth'],
                    'user_id' => $this->accountId($student['email']),
                ],
            );
        }

        return Student::pluck('id', 'student_number');
    }

    /**
     * @param  Collection<string, int>  $students
     * @param  Collection<string, int>  $courses
     */
    private function registrations(Collection $students, Collection $courses): void
    {
        foreach (self::REGISTRATIONS as $studentNumber => $codes) {
            foreach ($codes as $position => $code) {
                CourseRegistration::updateOrCreate(
                    [
                        'student_id' => $students[$studentNumber],
                        'course_id' => $courses[$code],
                    ],
                    [
                        'status' => 'registered',
                        'registered_at' => now()->subDays($position + 1),
                    ],
                );
            }
        }
    }

    private function accountId(string $email): int
    {
        return User::where('email', $email)->value('id');
    }
}
