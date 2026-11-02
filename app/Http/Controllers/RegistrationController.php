<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistrationRequest;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    /**
     * An administrator sees every registration, a lecturer sees the ones on their own courses,
     * a student sees only their own.
     */
    public function index(): View
    {
        $user = auth()->user();

        Gate::authorize('viewAny', CourseRegistration::class);

        $registrations = match (true) {
            $user->hasPermissionTo('manage registrations') => $this->query()->get(),
            $user->hasPermissionTo('view assigned courses') => $this->query()
                ->whereHas('course.lecturer', fn ($query) => $query->where('user_id', $user->id))
                ->get(),
            default => $this->query()
                ->whereHas('student', fn ($query) => $query->where('user_id', $user->id))
                ->get(),
        };

        return view('registrations.index', ['registrations' => $registrations]);
    }

    public function create(): View|RedirectResponse
    {
        $user = auth()->user();

        Gate::authorize('create', CourseRegistration::class);

        if ($user->hasPermissionTo('manage registrations')) {
            return view('registrations.create', [
                'available' => $this->allCourses(),
                'students' => Student::orderBy('last_name')->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'student_number']),
            ]);
        }

        if (is_null($user->student)) {
            return back()->with('error', 'This account is not linked to a student record yet, so there is nothing to register.');
        }

        return view('registrations.create', [
            'available' => Course::with('lecturer')
                ->whereDoesntHave('students', fn ($query) => $query->where('students.id', $user->student->id))
                ->orderBy('course_code')
                ->get(),
            'student' => $user->student,
        ]);
    }

    /**
     * The duplicate check lives in StoreRegistrationRequest, and the unique index on the pivot
     * table stops a race getting one through anyway.
     */
    public function store(StoreRegistrationRequest $request): RedirectResponse
    {
        $registration = CourseRegistration::create([
            ...$request->validated(),
            'status' => 'registered',
            'registered_at' => now(),
        ]);

        return redirect()
            ->route('registrations.index')
            ->with('status', "{$registration->student->full_name} is now registered for {$registration->course->course_code}.");
    }

    public function destroy(CourseRegistration $registration): RedirectResponse
    {
        Gate::authorize('delete', $registration);

        $reference = $registration->course->course_code;

        $registration->delete();

        return back()->with('status', "The registration for {$reference} has been cancelled.");
    }

    private function query(): Builder
    {
        return CourseRegistration::with(['student', 'course.lecturer'])->latest('registered_at');
    }

    private function allCourses(): Collection
    {
        return Course::with('lecturer')->orderBy('course_code')->get();
    }
}
