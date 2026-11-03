<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Student;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * One landing address per role, each showing only the figures that role may read.
     */
    public function __invoke(): View
    {
        $user = auth()->user();

        // checkPermissionTo() rather than hasPermissionTo(): the latter throws when the named
        // permission is absent from the table, which would stop a freshly registered account that
        // has not been given a role yet from reaching any desk at all.
        return view('dashboard', [
            'figures' => $this->figures($user),
            'title' => match (true) {
                $user->checkPermissionTo('manage students') => 'Administrator desk',
                $user->checkPermissionTo('view assigned courses') => 'Lecturer desk',
                default => 'My studies',
            },
        ]);
    }

    /**
     * @return array<string, int|string>
     */
    private function figures(User $user): array
    {
        return match (true) {
            $user->checkPermissionTo('manage students') => [
                'Students on the register' => Student::count(),
                'Courses on the catalogue' => Course::count(),
                'Live registrations' => CourseRegistration::count(),
                'Signed-in accounts' => User::count(),
            ],

            $user->checkPermissionTo('view assigned courses') => [
                'Courses you teach' => $user->lecturer?->courses()->count() ?? 0,
                'Students on your courses' => Student::whereHas(
                    'registrations.course.lecturer',
                    fn ($query) => $query->where('user_id', $user->id)
                )->count(),
                'Registrations you can read' => CourseRegistration::whereHas(
                    'course.lecturer',
                    fn ($query) => $query->where('user_id', $user->id)
                )->count(),
            ],

            default => [
                'Your registrations' => $user->student?->registrations()->count() ?? 0,
                'Courses still open to you' => Course::whereDoesntHave(
                    'students',
                    fn ($query) => $query->where('students.user_id', $user->id)
                )->count(),
                'Your programme' => $user->student?->programme ?? 'No student record linked yet',
            ],
        };
    }
}
