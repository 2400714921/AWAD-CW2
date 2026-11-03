<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Register for a course</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <x-alerts />

            <div class="p-6 bg-white shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('registrations.store') }}" class="space-y-6">
                    @csrf

                    @isset($students)
                        <div>
                            <x-input-label for="student_id" value="Student" />
                            <select id="student_id" name="student_id" required
                                    class="block w-full mt-1 border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                                <option value="">Choose a student</option>
                                @foreach ($students as $option)
                                    <option value="{{ $option->id }}" @selected(old('student_id') == $option->id)>
                                        {{ $option->full_name }} — {{ $option->student_number }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('student_id')" />
                        </div>
                    @else
                        <p class="text-sm text-slate-600">
                            Registering <span class="font-medium">{{ $student->full_name }}</span>
                            ({{ $student->student_number }}).
                        </p>
                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                    @endisset

                    <div>
                        <x-input-label for="course_id" value="Course" />
                        <select id="course_id" name="course_id" required
                                class="block w-full mt-1 border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                            <option value="">Choose a course</option>
                            @foreach ($available as $course)
                                <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>
                                    {{ $course->course_code }} — {{ $course->title }}
                                    ({{ $course->credits }} credits, {{ $course->lecturer?->full_name ?? 'unassigned' }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('course_id')" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>Register</x-primary-button>
                        <a href="{{ route('registrations.index') }}" class="text-sm text-slate-600 hover:underline">Cancel</a>
                    </div>
                </form>
            </div>

            <p class="text-sm text-slate-500">
                Courses you are already registered for are kept out of this list, and the register refuses a
                second entry for the same student and course.
            </p>
        </div>
    </div>
</x-app-layout>
