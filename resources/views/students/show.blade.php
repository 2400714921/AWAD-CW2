<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $student->full_name }} — {{ $student->student_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <x-alerts />

            <div class="p-6 bg-white shadow-sm sm:rounded-lg">
                <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Admission number</dt>
                        <dd class="text-sm text-gray-900">{{ $student->student_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="text-sm text-gray-900">{{ $student->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Programme</dt>
                        <dd class="text-sm text-gray-900">{{ $student->programme }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Year of study</dt>
                        <dd class="text-sm text-gray-900">{{ $student->year_of_study }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date of birth</dt>
                        <dd class="text-sm text-gray-900">{{ $student->date_of_birth?->format('d M Y') ?? 'Not recorded' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Login account</dt>
                        <dd class="text-sm text-gray-900">{{ $student->user?->email ?? 'Not linked to an account' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="p-6 bg-white shadow-sm sm:rounded-lg">
                <h3 class="mb-3 text-base font-medium text-gray-900">Registered courses</h3>

                @forelse ($student->courses as $course)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <div>
                            <a href="{{ route('courses.show', $course) }}" class="text-sm text-indigo-600 hover:underline">
                                {{ $course->course_code }} — {{ $course->title }}
                            </a>
                            <p class="text-sm text-gray-500">
                                {{ $course->credits }} credit units, {{ $course->semester }},
                                {{ $course->lecturer?->full_name ?? 'no lecturer assigned' }}
                            </p>
                        </div>
                        <span class="text-sm text-gray-500">{{ $course->pivot->registered_at ?? $course->pivot->created_at }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">This student is not registered for any course yet.</p>
                @endforelse
            </div>

            <div class="flex items-center gap-4">
                @can('update', $student)
                    <a href="{{ route('students.edit', $student) }}"
                       class="inline-block px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded hover:bg-gray-700">
                        Edit record
                    </a>
                @endcan

                @can('delete', $student)
                    <form method="POST" action="{{ route('students.destroy', $student) }}"
                          onsubmit="return confirm('Delete this student record?')">
                        @csrf
                        @method('DELETE')
                        <x-danger-button>Delete record</x-danger-button>
                    </form>
                @endcan

                <a href="{{ route('students.index') }}" class="text-sm text-gray-600 hover:underline">Back to the register</a>
            </div>
        </div>
    </div>
</x-app-layout>
