<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ $course->course_code }} — {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <x-alerts />

            <div class="p-6 bg-white shadow-sm sm:rounded-lg">
                <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-slate-500">Credit units</dt>
                        <dd class="text-sm text-slate-900">{{ $course->credits }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-500">Semester</dt>
                        <dd class="text-sm text-slate-900">{{ $course->semester }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-500">Lecturer</dt>
                        <dd class="text-sm text-slate-900">
                            {{ $course->lecturer?->full_name ?? 'Unassigned' }}
                            <span class="text-slate-500">({{ $course->lecturer?->department }})</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-500">Lecturer login</dt>
                        <dd class="text-sm text-slate-900">{{ $course->lecturer?->user?->email ?? 'No account linked' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-slate-500">Description</dt>
                        <dd class="text-sm text-slate-900">{{ $course->description ?: 'No description recorded.' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="p-6 bg-white shadow-sm sm:rounded-lg">
                <h3 class="mb-3 text-base font-medium text-slate-900">
                    Students registered on this course ({{ $course->students->count() }})
                </h3>

                @forelse ($course->students as $student)
                    <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                        <div>
                            <a href="{{ route('students.show', $student) }}" class="text-sm text-blue-600 hover:underline">
                                {{ $student->full_name }}
                            </a>
                            <p class="text-sm text-slate-500">{{ $student->student_number }} — {{ $student->programme }}</p>
                        </div>
                        <span class="text-sm text-slate-500">{{ $student->pivot->status }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No student has registered for this course yet.</p>
                @endforelse
            </div>

            <div class="flex items-center gap-4">
                @can('update', $course)
                    <a href="{{ route('courses.edit', $course) }}"
                       class="inline-block px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded hover:bg-blue-800">
                        Edit course
                    </a>
                @endcan

                @can('delete', $course)
                    <form method="POST" action="{{ route('courses.destroy', $course) }}"
                          onsubmit="return confirm('Remove this course from the catalogue?')">
                        @csrf
                        @method('DELETE')
                        <x-danger-button>Delete course</x-danger-button>
                    </form>
                @endcan

                <a href="{{ route('courses.index') }}" class="text-sm text-slate-600 hover:underline">Back to the catalogue</a>
            </div>
        </div>
    </div>
</x-app-layout>
