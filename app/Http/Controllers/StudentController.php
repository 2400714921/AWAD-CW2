<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * An administrator reads the whole register, a lecturer reads the students sitting on the
     * courses they teach, and a student only ever reaches their own record.
     */
    public function index(): View
    {
        $user = auth()->user();

        Gate::authorize('viewAny', Student::class);

        $students = match (true) {
            $user->hasPermissionTo('manage students') => Student::with('user')->orderBy('last_name')->orderBy('first_name')->get(),
            $user->hasPermissionTo('view student records') => Student::whereHas(
                'registrations.course.lecturer',
                fn ($query) => $query->where('user_id', $user->id)
            )->orderBy('last_name')->get(),
            default => Student::where('user_id', $user->id)->get(),
        };

        return view('students.index', ['students' => $students]);
    }

    public function create(): View
    {
        Gate::authorize('create', Student::class);

        return view('students.create', [
            'accounts' => User::doesntHave('student')->orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $student = Student::create($request->validated());

        return redirect()
            ->route('students.show', $student)
            ->with('status', "Student {$student->student_number} has been added to the register.");
    }

    /**
     * The registered courses come straight from the belongsToMany relationship.
     */
    public function show(Student $student): View
    {
        Gate::authorize('view', $student);

        return view('students.show', [
            'student' => $student->load(['user', 'courses.lecturer']),
        ]);
    }

    public function edit(Student $student): View
    {
        Gate::authorize('update', $student);

        return view('students.edit', [
            'accounts' => User::where('id', $student->user_id)->orWhereDoesntHave('student')->orderBy('name')->get(['id', 'name', 'email']),
            'student' => $student,
        ]);
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $student->update($request->validated());

        return redirect()
            ->route('students.show', $student)
            ->with('status', 'The student record has been updated.');
    }

    /**
     * Registrations are deleted with a student by the foreign key, so an administrator has to
     * clear them first rather than wipe a term of registrations by accident.
     */
    public function destroy(Student $student): RedirectResponse
    {
        Gate::authorize('delete', $student);

        if ($student->registrations()->exists()) {
            return back()->with('error', "Remove {$student->full_name}'s course registrations before deleting the record.");
        }

        $student->delete();

        return redirect()
            ->route('students.index')
            ->with('status', 'The student record has been deleted.');
    }
}
