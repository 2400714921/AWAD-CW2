<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use App\Models\Lecturer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Three roles, three different course lists from the same address.
     */
    public function index(): View
    {
        $user = auth()->user();

        Gate::authorize('viewAny', Course::class);

        $courses = match (true) {
            $user->hasPermissionTo('manage courses') => Course::with('lecturer')->orderBy('course_code')->get(),
            $user->hasPermissionTo('view assigned courses') => Course::with('lecturer')
                ->whereRelation('lecturer', 'user_id', $user->id)
                ->orderBy('course_code')
                ->get(),
            default => Course::with('lecturer')
                ->whereDoesntHave('students', fn ($query) => $query->where('students.user_id', $user->id))
                ->orderBy('course_code')
                ->get(),
        };

        return view('courses.index', [
            'courses' => $courses,
            'heading' => match (true) {
                $user->hasPermissionTo('manage courses') => 'All courses',
                $user->hasPermissionTo('view assigned courses') => 'Courses assigned to you',
                default => 'Courses you have not registered for yet',
            },
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Course::class);

        return view('courses.create', ['lecturers' => $this->lecturers()]);
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $course = Course::create($request->validated());

        return redirect()
            ->route('courses.show', $course)
            ->with('status', "Course {$course->course_code} has been added to the catalogue.");
    }

    /**
     * The registered students come from the belongsToMany relationship seen from the other side.
     */
    public function show(Course $course): View
    {
        Gate::authorize('view', $course);

        return view('courses.show', [
            'course' => $course->load(['lecturer.user', 'students']),
        ]);
    }

    public function edit(Course $course): View
    {
        Gate::authorize('update', $course);

        return view('courses.edit', [
            'course' => $course,
            'lecturers' => $this->lecturers(),
        ]);
    }

    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $course->update($request->validated());

        return redirect()
            ->route('courses.show', $course)
            ->with('status', 'The course details have been updated.');
    }

    /**
     * Refuse to cascade away a whole set of registrations behind an administrator's back.
     */
    public function destroy(Course $course): RedirectResponse
    {
        Gate::authorize('delete', $course);

        if ($course->registrations()->exists()) {
            return back()->with('error', "Students are still registered on {$course->course_code}. Clear those registrations first.");
        }

        $course->delete();

        return redirect()
            ->route('courses.index')
            ->with('status', "Course {$course->course_code} has been removed from the catalogue.");
    }

    private function lecturers(): Collection
    {
        return Lecturer::orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'staff_number']);
    }
}
